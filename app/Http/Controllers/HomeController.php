<?php

namespace App\Http\Controllers;

use App\Model\Investment;
use App\Model\Payment;
use App\Model\Account;
use App\Model\Topup;
use App\Model\Report;
use App\Model\InvestmentType;
use App\Model\TransactionsSummary;
use App\User;
use DB;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
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

        $trans_summary = TransactionsSummary::getTransSumamry();

        $all_dates = array();
        $all_tot_investments = array();
        $all_tot_topups = array();
        $all_tot_payments = array();
        $all_tot_terminations = array();

        foreach ($trans_summary as $key => $value) {
            $all_dates[] = $value->date;
            $all_tot_investments[] = $value->tot_investments;
            $all_tot_topups[] = $value->tot_topups;
            $all_tot_payments[] = $value->tot_payments;
            $all_tot_terminations[] = $value->tot_terminations;
        }

        $data['all_dates'] = json_encode($all_dates, true);
        $data['all_tot_investments'] = json_encode($all_tot_investments, true);
        $data['all_tot_topups'] = json_encode($all_tot_topups, true);
        $data['all_tot_payments'] = json_encode($all_tot_payments, true);
        $data['all_tot_terminations'] = json_encode($all_tot_terminations, true);

        return view('home')->with($data);
    }
}