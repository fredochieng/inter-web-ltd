<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Model\Report;
use Illuminate\Http\Request;
use DB;
use App\User;
use App\Model\Investment;
use App\Model\PaymentMethod;
use App\Model\Bank;
use Carbon\Carbon;
use ExcelReport;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { }

    public function customerReport()
    {
        $data['records'] = Report::customerReport();
        $data['records']->map(function ($item) {
            $due = Report::customerDuePaymentsReport();
            return $item;
        });

        return view('reports.customer')->with($data);
    }

    public function investmentReport()
    {
        $data['total_investments'] = Investment::totalInvestments();
        $data['sum_payout'] = Investment::sumPayout();
        $data['due_payments'] = Investment::totalPayout();
        $data['today_total_investments'] = Investment::todayTotalInvestments();

        return view('reports.investment')->with($data);
    }

    public function getNextPayDate($id)
    {

        // GET THE DATES THE CLIENT HAS BEEN PAID
        $user_pay_dates = DB::table('payments')
            ->select(
                DB::raw('payments.*'),
                DB::raw('accounts.id as acc_id'),
                DB::raw('users.id as user_idd')
            )
            ->leftJoin('accounts', 'payments.account_no_id', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->where('users.id', '=', $id)
            ->get();

        $user_pay_dates = json_decode(json_encode($user_pay_dates), true);
        $user_pay_dates = array_column($user_pay_dates, 'user_pay_date');


        // GET THE REAL PAYMENTS DATE FOR THE CLIENT
        $pay_dates = DB::table('user_pay_modes')
            ->select(
                DB::raw('user_pay_modes.pay_dates')
            )
            ->where('user_pay_modes.user_id', '=', $id)
            ->get();

        $pay_dates = explode(';',  $pay_dates);
        $client_pay_dates = array_filter(array_map('trim', $pay_dates));
        $client_pay_dates = str_replace('[{"pay_dates":"[\"', '', $client_pay_dates);
        $client_pay_dates = str_replace('[{"pay_dates":"', '', $client_pay_dates);
        $client_pay_dates = str_replace('\"]"}]', '', $client_pay_dates);
        $client_pay_dates = str_replace('"}]', '', $client_pay_dates);
        $client_pay_dates = str_replace('\",\"', ',', $client_pay_dates);

        foreach ($client_pay_dates as $key => $value) {
            $client_pay_dates = $value;
        }

        $client_pay_dates = explode(',', $client_pay_dates);

        $data['next_pay_date'] = array_diff($client_pay_dates, $user_pay_dates);

        if (empty($data['next_pay_date'])) {

            $data['next_pay_date'] = "FULLY PAID";
        } else {

            $data['next_pay_date'] = min(array_diff($client_pay_dates, $user_pay_dates));
        }


        // GET CLIENTS NEXT PAYMENT DATE
        // $data['next_pay_date'] = min(array_diff($client_pay_dates, $user_pay_dates));

        return $data['next_pay_date'];
    }

    public function getNextPayment($id)
    {
        $data['tot_payable'] = DB::table('payment_schedule')
            ->select(
                DB::raw('sum(tot_payable_amnt) as user_tot_payable, account_no_id '),
                DB::raw('payment_schedule.*'),
                DB::raw('accounts.*'),
                DB::raw('user_pay_modes.*'),
                DB::raw('users.*')
            )

            ->leftJoin('accounts', 'payment_schedule.account_no_id', 'accounts.id')
            ->leftJoin('user_pay_modes', 'accounts.user_id', 'user_pay_modes.user_id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->groupBy('payment_schedule.account_no_id')
            ->where('users.id', '=', $id)
            ->first();

        $data['to_be_paid'] = '';
        $data['to_be_paid1'] = '';
        $data['to_be_paid2'] = '';
        $data['to_be_paid3'] = '';

        if ($data['tot_payable']->comp_monthly_pay == '') {

            $data['to_be_paid'] = $data['tot_payable']->monthly_amount;
            $data['updated_monthly_amnt'] = $data['tot_payable']->updated_next_pay;

            return $data['to_be_paid'];
        } elseif ($data['tot_payable']->monthly_amount == '' && $data['tot_payable']->comp_monthly_pay != '') {

            $data['to_be_paid1'] = $data['tot_payable']->tot_payable_amnt;

            return $data['to_be_paid1'];
        } elseif ($data['tot_payable']->comp_monthly_pay != '' && $data['tot_payable']->monthly_amount != '') {

            $data['to_be_paid2'] = $data['tot_payable']->monthly_amount;
            // $data['to_be_paid2'] = $data['tot_payable']->monthly_amount;
            // $data['to_be_paid3'] =  $data['tot_payable']->tot_comp_amount;

            // return array($data['to_be_paid2'], $data['to_be_paid3']);
            // logger()->error('An error occurred');
            return $data['to_be_paid2'];
        }

        // list($first, $second) = getNextPayment();
        // $array = getNextPayment();
        // $x = $array[$data['to_be_paid2']];
        // $y = $array[$data['to_be_paid3']];

    }

    public function duePaymentsReport()
    {
        $data['clients'] = User::getClients();
        $data['payment_modes'] = PaymentMethod::getPaymentMethods();
        $data['banks'] = Bank::getBanks();
        $data['due_payments_report'] = Report::duePaymentsReport();

        $data['due_payments_report']->map(function ($item) {

            $item->next_pay_date = $this->getNextPayDate($item->user_id);

            if ($item->inv_type_id == 1 || 2) {
                $item->to_be_paid = $this->getNextPayment($item->user_id);
            } elseif ($item->inv_type_id == 3) {
                $item->to_be_paid = $this->getNextPayment($item->user_id);
                // $item->to_be_paid2 = $this->getNextPayment($item->user_id);
                // $item->to_be_paid[] = $this->getNextPayment($item->user_id);
            };
            return $item;
        });

        return view('reports.due-payments')->with($data);
    }

    public function showDuePaymentsReports(Request $request)
    {

        $data['clients'] = User::getClients();
        $data['payment_modes'] = PaymentMethod::getPaymentMethods();
        $data['banks'] = Bank::getBanks();

        $date_range = $request->input('date_range');
        $date_range = (array)$date_range;
        $date_range = str_replace(' - ', ',', $date_range);

        foreach ($date_range as $key => $value) {
            $date_range = $value;
        }

        $date_range = explode(',', $date_range);
        $data['start_date'] = date('Y-m-d', strtotime($date_range[0]));
        $data['end_date'] = date('Y-m-d', strtotime($date_range[1]));

        $today = Carbon::now()->toDateString();
        $pay_mode_id = $request->input('pay_mode_id');
        $bank_id = $request->input('bank_id');

        if (($date_range != '') && ($pay_mode_id == '') && ($bank_id == '')) {

            $data['type'] = 1;

            $data['due_payments_report'] = Report::duePaymentsReport();

            $data['due_payments_report']->map(function ($item) {

                $item->next_pay_date = $this->getNextPayDate($item->user_id);

                if ($item->inv_type_id == 1 || 2) {
                    $item->to_be_paid = $this->getNextPayment($item->user_id);
                } elseif ($item->inv_type_id == 3) {
                    $item->to_be_paid = $this->getNextPayment($item->user_id);
                    // $item->to_be_paid2 = $this->getNextPayment($item->user_id);
                    // $item->to_be_paid[] = $this->getNextPayment($item->user_id);
                }
                return $item;
            });

            $data['pay_mode'] = 'NO SELECTION';
            $data['pay_bank'] = 'NO COLLECTION';

            $data['meta'] = ['Payment Date' => $data['start_date'] . ' To ' . $data['end_date']];

            $data['today_due_payment_report'] = $data['due_payments_report']->whereBetween('next_pay_date', array($data['start_date'], $data['end_date']));
            $data['columns'] = [
                'Account No' => 'account_no', 'Name' => 'name', 'ID Number' => 'id_no',
                'Mode of Payment' => 'method_name', 'Bank' => 'bank_name', 'Bank account' => 'pay_bank_acc',
                'MPESA Number' => 'pay_mpesa_no', 'Amount' => 'to_be_paid', 'Payment Date' => 'next_pay_date'
            ];

            //return view('reports.view')->with($data);
            $data['title'] = "Due Payments Report";
            $download_excel = ExcelReport::of($data['title'], $data['meta'],  $data['today_due_payment_report'], $data['columns'])
                ->limit(20)
                ->download('payments');

            return $download_excel;

            // function downloadExcel()
            // {
            //     $title = "Due Payments Report";
            //     $meta = ['Payment Date' => '23-04-2019' . ' To ' . '23-08-2019'];
            //     $download_excel = ExcelReport::of($title, $meta)
            //         ->limit(20)
            //         ->download('payments');

            //     return $download_excel;
            // }
            // return $data['today_due_payment_report'];
        } elseif (($date_range != '') && ($pay_mode_id != '')) {

            $data['type'] = 2;
            $data['due_payments_report'] = Report::duePaymentsReport();

            $data['due_payments_report']->map(function ($item) {


                $item->next_pay_date = $this->getNextPayDate($item->user_id);

                if ($item->inv_type_id == 1 || 2) {
                    $item->to_be_paid = $this->getNextPayment($item->user_id);
                } elseif ($item->inv_type_id == 3) {
                    $item->to_be_paid = $this->getNextPayment($item->user_id);
                    // $item->to_be_paid2 = $this->getNextPayment($item->user_id);
                    // $item->to_be_paid[] = $this->getNextPayment($item->user_id);
                }
                return $item;
            });

            $data['today_due_payment_report'] = $data['due_payments_report']->where('pay_mode_id', '=>', $pay_mode_id)->whereBetween(
                'next_pay_date',
                array($data['start_date'], $data['end_date'])
            );

            $data['pay_mode'] = 'NO SELECTION';
            $data['pay_bank'] = 'NO COLLECTION';
            $data['pay_mode_id'] = $pay_mode_id;

            $data['pay_mode'] = $data['payment_modes']->where('method_id', '=', $pay_mode_id)->pluck('method_name')->first();

            $user = \Auth::user();

            logger($data['pay_mode'] . ' due payments report from ' . $data['start_date'] . ' to ' . $data['end_date'] .  ' run by ' . $user->name);
        }
        if (!empty($date_range) && !empty($pay_mode_id) && !empty($bank_id)) {


            $data['type'] = 3;

            $data['due_payments_report'] = Report::duePaymentsReport();

            $data['due_payments_report']->map(function ($item) {

                $item->next_pay_date = $this->getNextPayDate($item->user_id);

                if ($item->inv_type_id == 1 || 2) {
                    $item->to_be_paid = $this->getNextPayment($item->user_id);
                } else {
                    $item->to_be_paid[] = $this->getNextPayment($item->user_id);
                }
                return $item;
            });

            $data['today_due_payment_report'] = $data['due_payments_report']
                ->where('pay_mode_id', '=', $pay_mode_id)
                ->where('bank_id', '=', $bank_id)
                ->whereBetween(
                    'next_pay_date',
                    array($data['start_date'], $data['end_date'])
                );

            $data['pay_bank'] = $data['banks']->where('bank_id', '=', $bank_id)->pluck('bank_name')->first();

            $user = \Auth::user();

            logger($data['pay_bank'] . ' due payments report from ' . $data['start_date'] . ' to ' . $data['end_date'] .  ' run by ' . $user->name);
        }

        return view('reports.view')->with($data);
    }

    public function downloadExcel()
    {
        // $queryBuilder = $this->showDuePaymentsReports($title, $meta,  $data['today_due_payment_report'], $columns);

        // $download_excel = ExcelReport::of($title, $meta,  $data['today_due_payment_report'], $columns)
        //     ->limit(20)
        //     ->download('payments');

        // return $download_excel;
        $title = "Due Payments Report";
        $meta = ['Payment Date' => '2019-01-02' . ' To ' . '2019-12-01'];
        $queryBuilder = User::getClients();

        $columns = [
            'Name' => 'name',
            'Email' => 'email',
            'Registered At' => 'created_at'
        ];
        $download_excel = ExcelReport::of($title, $meta, $queryBuilder, $columns)
            ->limit(20)
            ->download('payments');

        return $download_excel;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    { }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        //
    }
}