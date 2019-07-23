<?php

namespace App\Http\Controllers;

use App\Model\Investment;
use App\Model\Payment;
use App\Model\Account;
use App\Model\Topup;
use App\Model\Report;
use App\Model\InvestmentType;
use App\User;
use DB;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Illuminate\Http\Request;
use function GuzzleHttp\json_decode;
use App\Jobs\DailyTransactionsSummaryJob;
use App\Jobs\SendWelcomeEmail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['sum_investments'] = Investment::totalInvestments();
        $data['total_monthly_investments'] = Investment::totalMonthlyInvestments();
        $data['total_compounded_investments'] = Investment::totalCompoundedInvestments();
        $data['total_monthly_comp_investments'] = Investment::totalMonthlyAndCompoundedInvestments();

        $data['sum_tot_payable'] = Investment::sumTotalPayable();
        $data['sum_tot_payments'] = Payment::sumTotalPayments();
        $data['sum_tot_topups'] = Topup::totalTopups();
        $data['sum_tot_due_payments'] = Account::sumTotalDuePayments();
        $data['total_customers'] = User::getTotalCustomers();
        $data['total_secretaries'] = User::getTotalSecretaries();

        $data['sum_investments1'] = DB::table('investments')->sum('investment_amount');
        $data['sum_tot_due_payments1'] = DB::table('accounts')->sum('total_due_payments');
        $data['sum_tot_payments1'] = DB::table('payments')->sum('total_payment');
        $data['sum_tot_topups1'] = DB::table('topups')->sum('topup_amount');

        $investments = Investment::getInvestments();

        // $report = $this->showDuePaymentsReports();

        // echo "<pre>";
        // print_r($investments);
        // exit;


        return view('home')->with($data);
    }

    public function summaryJob()
    {
        // $emailJob = new SendWelcomeEmail();
        // dispatch($emailJob);

        $transJob = new DailyTransactionsSummaryJob();
        dispatch($transJob);

        exit;

        $investments = DB::table('investments')
            ->select(
                DB::raw('investments.inv_status_id'),
                DB::raw('investments.inv_date'),
                DB::raw('investments.investment_amount'),
                DB::raw('sum(investment_amount) as tot_investment_amount'),
                DB::raw('topups.topped_at'),
                DB::raw('topups.topup_amount'),
                DB::raw('sum(topup_amount) as tot_topup_amount'),
                DB::raw('payments.user_pay_date'),
                DB::raw('payments.total_payment'),
                DB::raw('sum(total_payment) as tot_payment_amount'),
                DB::raw('terminations.ter_date'),
                DB::raw('terminations.amount_ter'),
                DB::raw('sum(amount_ter) as tot_ter_amount'),
            )
            ->LEFTJOIN('topups', 'investments.inv_date', '=', 'topups.topped_at')
            ->LEFTJOIN('payments', 'investments.inv_date', '=', 'payments.user_pay_date')
            ->LEFTJOIN('terminations', 'investments.inv_date', '=', 'terminations.ter_date')
            ->groupBy('investments.inv_date', 'topups.topped_at', 'payments.user_pay_date', 'terminations.ter_date');

        $investments1 = DB::table('investments')
            ->select(
                DB::raw('investments.inv_status_id'),
                DB::raw('investments.inv_date'),
                DB::raw('investments.investment_amount'),
                DB::raw('sum(investment_amount) as tot_investment_amount'),
                DB::raw('topups.topped_at'),
                DB::raw('topups.topup_amount'),
                DB::raw('sum(topup_amount) as tot_topup_amount'),
                DB::raw('payments.user_pay_date'),
                DB::raw('payments.total_payment'),
                DB::raw('sum(total_payment) as tot_payment_amount'),
                DB::raw('terminations.ter_date'),
                DB::raw('terminations.amount_ter'),
                DB::raw('sum(amount_ter) as tot_ter_amount'),
            )
            ->RIGHTJOIN('topups', 'investments.inv_date', '=', 'topups.topped_at')
            ->RIGHTJOIN('payments', 'investments.inv_date', '=', 'payments.user_pay_date')
            ->RIGHTJOIN('terminations', 'investments.inv_date', '=', 'terminations.ter_date')
            ->groupBy('investments.inv_date', 'topups.topped_at', 'payments.user_pay_date', 'terminations.ter_date')
            ->orderBy('topups.topped_at')
            ->unionAll($investments)
            ->get();

        $investments1 = (array) $investments1;
        $investments1 = json_encode($investments1);
        $investments1 = json_decode($investments1, true);

        foreach ($investments1 as $key => $row) { }

        $final_inv = array_unique($row, SORT_REGULAR);

        foreach ($final_inv as $key => $value) {
            $inv_date = $value['inv_date'];
            $topup_date = $value['topped_at'];
            $pay_date = $value['user_pay_date'];
            $ter_date = $value['ter_date'];

            $tot_investments = $value['tot_investment_amount'];
            $tot_topups = $value['tot_topup_amount'];
            $tot_payments = $value['tot_payment_amount'];
            $tot_terminations = $value['tot_ter_amount'];

            if (empty($inv_date)) {
                //  echo "FRed";
                $date = $ter_date or $topup_date or $pay_date;
            } else {
                $date = $inv_date;
            }

            if (empty($tot_investments)) {
                $tot_investments = 0;
            } else {
                $tot_investments = $tot_investments;
            }

            if (empty($tot_topups)) {
                $tot_topups = 0;
            } else {
                $tot_topups = $tot_topups;
            }

            if (empty($tot_payments)) {
                $tot_payments = 0;
            } else {
                $tot_payments = $tot_payments;
            }

            if (empty($tot_terminations)) {
                $tot_terminations = 0;
            } else {
                $tot_terminations = $tot_terminations;
            }



            DB::table('transactions_summary')->upsert(
                [
                    'date' => $date, 'tot_investments' => $tot_investments, 'tot_topups' => $tot_topups,
                    'tot_payments' => $tot_payments, 'tot_terminations' => $tot_terminations
                ],
                ['date'],
                ['tot_investments', 'tot_topups', 'tot_payments', 'tot_terminations', 'updated_at']
            );
        }

        $all_dates = array();
        $all_investments = array();
        $all_topups = array();
        $all_payments = array();
        $all_ters = array();
        $value = array();
        foreach ($final_inv as $key => $value) {
            $inv_date = $value['inv_date'];
            $topup_date = $value['topped_at'];
            $pay_date = $value['user_pay_date'];
            $ter_date = $value['ter_date'];

            $all_dates[] = $inv_date;
            $all_dates[] = $topup_date;
            $all_dates[] = $pay_date;
            $all_dates[] = $ter_date;

            if (empty($value['tot_investment_amount'])) {
                $all_investments[] = 0;
            } else {

                $all_investments[] = $value['tot_investment_amount'];
            }
            if (empty($value['tot_topup_amount'])) {
                $all_topups[] = 0;
            } else {

                $all_topups[] = $value['tot_topup_amount'];
            }

            if (empty($value['tot_payment_amount'])) {
                $all_payments[] = 0;
            } else {
                $all_payments[] = $value['tot_payment_amount'];
            }

            if (empty($value['tot_ter_amount'])) {
                $all_ters[] = 0;
            } else {
                $all_ters[] = $value['tot_ter_amount'];
            }
        }


        $compare_function = function ($a, $b) {

            $a_timestamp = strtotime($a);
            $b_timestamp = strtotime($b);

            return $a_timestamp <=> $b_timestamp;
        };

        usort($all_dates, $compare_function);
        $all_dates = array_filter(array_unique($all_dates, SORT_REGULAR));

        $data['all_dates'] = json_encode($all_dates, true);
        $data['all_investments'] = json_encode($all_investments);
        $data['all_topups'] = json_encode($all_topups, true);
        $data['all_payments'] = json_encode($all_payments);
        $data['all_ters'] = json_encode($all_ters, true);

        echo "<pre>";
        // print_r($output);
        echo "<pre>";
        exit;
        print_r($all_investments);
        echo "<pre>";
        print_r($all_topups);
        echo "<pre>";
        print_r($all_payments);
        echo "<pre>";
        print_r($all_ters);
        exit;
    }
}