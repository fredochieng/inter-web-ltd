<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Model\Payment;
use Illuminate\Http\Request;
use DB;
use App\User;
use App\Model\Investment;
use App\Model\PaymentMethod;
use App\Model\Bank;
use App\Model\Report;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
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

    public function getInvType($id)
    {
        $inv_type = DB::table('user_pay_modes')
            ->select(
                DB::raw('user_pay_modes.*'),
                DB::raw('users.*'),
                DB::raw('accounts.*'),
                DB::raw('payment_schedule.*')
            )
            ->leftJoin('users', 'user_pay_modes.user_id', '=', 'users.id')
            ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            ->where('user_pay_modes.user_id', '=', $id)
            ->first();

        $inv_type_id = $inv_type->inv_type;
        return $inv_type_id;
    }

    public function getReferer($id)
    {
        $referer = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('users.id as referee_id'),
                DB::raw('users_details.*'),
                DB::raw('accounts.*'),
                DB::raw('accounts.id AS accnt_id'),
                DB::raw('investments.*'),
            )
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
            ->leftJoin('investments', 'accounts.id', '=', 'investments.account_no_id')
            ->where('users.refered_by', '=', $id)
            ->where('investments.inv_status_id', '=', 1)
            ->where('tot_inv_comm', '>', 0)
            ->get();

        return $referer;
    }

    public function getRefererTopups($id)
    {
        $referer_topups = DB::table('topups')
            ->select(
                DB::raw('topups.*'),
                DB::raw('topups.created_at AS topped_date'),
                DB::raw('accounts.id'),
                DB::raw('users.id')
            )
            ->leftJoin('accounts', 'topups.account_id', '=', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->where('users.refered_by', '=', $id)
            ->orderBy('topups.topup_id', 'desc')
            ->where('tot_topup_comm', '>', 0)
            ->get();

        return $referer_topups;
    }

    public function getNextPayment($id)
    {
        $data['tot_payable'] = DB::table('payment_schedule')
            ->select(
                DB::raw('sum(tot_payable_amnt) as user_tot_payable '),
                DB::raw('payment_schedule.*'),
                DB::raw('accounts.*'),
                DB::raw('investments.inv_type_id'),
                DB::raw('investments.last_pay_date'),
                DB::raw('investments.account_no_id AS inv_account_no_id'),
                DB::raw('investments.termination_type'),
                DB::raw('user_pay_modes.*'),
                DB::raw('users.*')
            )

            ->leftJoin('accounts', 'payment_schedule.account_no_id', 'accounts.id')
            ->leftJoin('investments', 'payment_schedule.account_no_id', 'investments.account_no_id')
            ->leftJoin('user_pay_modes', 'accounts.user_id', 'user_pay_modes.user_id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->groupBy('payment_schedule.account_no_id')
            ->where('users.id', '=', $id)
            ->first();


        $terminated = $data['tot_payable']->termination_type;


        if ($data['tot_payable']->topped_up == 0 && $terminated == '') {

            $data['next_amount'] =  $data['tot_payable']->monthly_amount;
            $data['to_be_paid'] =  $data['next_amount'];
        } elseif ($data['tot_payable']->topped_up == 0 && $terminated != '') {
            // $data['next_amount'] =  $data['tot_payable']->updated_monthly_pay_ter + $tot_comm;
            $termination_payments =  DB::table('termination_payments')
                ->select(
                    DB::raw('termination_payments.*'),
                    DB::raw('users.*')
                )
                ->leftJoin('users', 'termination_payments.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->orderBy('termination_payments.ter_pay_id', 'desc')->first();

            if ($termination_payments) {

                $payment_amount =  $termination_payments->pay_amount;
                $payment_date =  $termination_payments->pay_date;

                $ter_payments = DB::table('payments')
                    ->select(
                        DB::raw('payments.*'),
                        DB::raw('accounts.*'),
                        DB::raw('users.*')
                    )
                    ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                    ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                    ->where('users.id', '=', $id)
                    ->where('payments.payment_amount', '=', $payment_amount)
                    ->where('payments.user_pay_date', '=', $payment_date)
                    ->orderBy('payments.payment_id', 'desc')->first();

                if ($ter_payments) {
                    $data['to_be_paid'] =  $data['tot_payable']->monthly_amount;
                } else {
                    $data['to_be_paid'] =  $data['tot_payable']->updated_monthly_pay_ter;
                }
            }
        } elseif ($data['tot_payable']->topped_up == 1 && $terminated == '') {
            $data['updated_next_pay'] =  $data['tot_payable']->updated_next_pay;
            // CHECK IF THE UPDATED NEXT PAY HAS BEEN PAID
            $data['client_payments'] = DB::table('payments')
                ->select(
                    DB::raw('payments.*'),
                    DB::raw('accounts.*'),
                    DB::raw('users.*')
                )
                ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->where('payments.payment_amount', '=',  $data['updated_next_pay'])
                ->orderBy('payments.payment_id', 'desc')->first();

            if ($data['client_payments']) {
                $data['to_be_paid'] =  $data['tot_payable']->updated_monthly_pay;
                // $data['next_amount'] =  $data['tot_payable']->updated_monthly_pay;
            } else {
                $data['to_be_paid'] =  $data['tot_payable']->updated_next_pay;
                // $data['next_amount'] =  $data['tot_payable']->updated_next_pay;
            }
        } elseif ($data['tot_payable']->topped_up == 1 && $terminated != '') {
            $termination_payments =  DB::table('termination_payments')
                ->select(
                    DB::raw('termination_payments.*'),
                    DB::raw('users.*')
                )
                ->leftJoin('users', 'termination_payments.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->orderBy('termination_payments.ter_pay_id', 'desc')->first();

            $payment_amount =  $termination_payments->pay_amount;
            $payment_date =  $termination_payments->pay_date;

            $ter_payments = DB::table('payments')
                ->select(
                    DB::raw('payments.*'),
                    DB::raw('accounts.*'),
                    DB::raw('users.*')
                )
                ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->where('payments.payment_amount', '=', $payment_amount)
                ->where('payments.user_pay_date', '=', $payment_date)
                ->orderBy('payments.payment_id', 'desc')->first();

            if ($ter_payments) {
                $data['to_be_paid'] =  $data['tot_payable']->monthly_amount;
            } else {
                $data['to_be_paid'] =  $data['tot_payable']->updated_monthly_pay_ter;
            }
        }


        if ($data['tot_payable']->comp_monthly_pay == '') {

            //  $data['to_be_paid'] = $data['to_be_paid'];
            // $data['to_be_paid'] = $data['tot_payable']->monthly_amount;
            $data['updated_monthly_amnt'] = $data['tot_payable']->updated_next_pay;

            return $data['to_be_paid'];
        } elseif ($data['tot_payable']->monthly_amount == '' && $data['tot_payable']->comp_monthly_pay != '') {

            $client_monthly_com = DB::table('payment_schedule')
                ->select(
                    DB::raw('payment_schedule.*'),
                    DB::raw('accounts.id as acc_id'),
                    DB::raw('users.id as user_idd')
                )

                ->leftJoin('accounts', 'payment_schedule.account_no_id', 'accounts.id')
                ->leftJoin('users', 'accounts.user_id', 'users.id')
                ->where('users.id', '=', $id)
                ->first();

            $comp_pay_date = $data['tot_payable']->last_pay_date;

            $comp_payment_amount = $client_monthly_com->tot_payable_amnt;

            $payment_exist = DB::table('payments')
                ->select(
                    DB::raw('payments.*'),
                    DB::raw('accounts.*'),
                    DB::raw('users.*')
                )
                ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->where('payments.payment_amount', '=', $comp_payment_amount)
                ->where('payments.user_pay_date', '=', $comp_pay_date)
                ->orderBy('payments.payment_id', 'desc')->first();

            if (empty($payment_exist)) {
                $data['comp_paid'] = 'N';
            } else {
                $data['comp_paid'] = 'Y';
            }

            $today = Carbon::now('Africa/Nairobi')->toDateString();
            // $today = '2020-03-20';

            if ($data['comp_paid'] = 'N' && $comp_pay_date != $today) {

                // $data['to_be_paid1'] = 0;

                // if ($terminated) {
                //     $data['to_be_paid1'] = $data['tot_payable']->total_due_payments;
                // } else {
                //     $data['to_be_paid1'] = 0;
                // }


                if ($terminated) {

                    $termination_payments =  DB::table('termination_payments')
                        ->select(
                            DB::raw('termination_payments.*'),
                            DB::raw('users.*')
                        )
                        ->leftJoin('users', 'termination_payments.user_id', '=', 'users.id')
                        ->where('users.id', '=', 7)
                        ->orderBy('termination_payments.ter_pay_id', 'desc')->first();

                    $payment_amount =  $termination_payments->pay_amount;
                    $payment_date =  $termination_payments->pay_date;

                    $ter_payments = DB::table('payments')
                        ->select(
                            DB::raw('payments.*'),
                            DB::raw('accounts.*'),
                            DB::raw('users.*')
                        )
                        ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                        ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                        ->where('users.id', '=', 7)
                        ->where('payments.payment_amount', '=', $payment_amount)
                        ->where('payments.user_pay_date', '=', $payment_date)
                        ->orderBy('payments.payment_id', 'desc')->first();

                    // echo "<pre>";
                    // print_r($ter_payments);
                    // exit;

                    if ($ter_payments) {

                        if ($comp_pay_date == $today) {

                            $data['to_be_paid1'] = $data['tot_payable']->total_due_payments;
                        } else {
                            $data['to_be_paid1'] = 0;
                        }
                    } else {
                        // echo "Unpaid";
                        $data['to_be_paid1'] = $payment_amount;
                    }
                    //  $data['comp_payable_amout'] = $data['customer_data']->total_due_payments  + $tot_comm;
                } else {

                    if ($comp_pay_date == $today) {
                        $data['to_be_paid1'] = $data['tot_payable']->total_due_payments;
                    } else {
                        $data['to_be_paid1'] = 0;
                    }
                }
            } elseif ($data['comp_paid'] = 'N' && $comp_pay_date == $today) {
                $data['to_be_paid1'] = $data['tot_payable']->total_due_payments;
            }

            return $data['to_be_paid1'];
        } elseif ($data['tot_payable']->comp_monthly_pay != '' && $data['tot_payable']->monthly_amount != '') {
            $data['to_be_paid'] = $data['to_be_paid'];
            // $data['to_be_paid'] = $data['tot_payable']->monthly_amount;
            $data['updated_monthly_amnt'] = $data['tot_payable']->updated_next_pay;
            // $data['to_be_paid2'] = $data['tot_payable']->monthly_amount;
            // $data['to_be_paid2'] = $data['tot_payable']->monthly_amount;
            // $data['to_be_paid3'] =  $data['tot_payable']->tot_comp_amount;

            // return array($data['to_be_paid2'], $data['to_be_paid3']);
            // logger()->error('An error occurred');
            return $data['to_be_paid'];
        }

        // list($first, $second) = getNextPayment();
        // $array = getNextPayment();
        // $x = $array[$data['to_be_paid2']];
        // $y = $array[$data['to_be_paid3']];

    }

    public function duePaymentsReport()
    {
        if (!auth()->user()->can('reports.manage')) {
            abort(401, 'Unauthorized action.');
        }
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

        // echo "<pre>";
        // print_r($data['due_payments_report']);
        // exit;

        return view('reports.due-payments')->with($data);
    }

    public function showDuePaymentsReports(Request $request)
    {
        if (!auth()->user()->can('reports.manage')) {
            abort(401, 'Unauthorized action.');
        }
        $data['clients'] = User::getClients();
        $data['payment_modes'] = PaymentMethod::getPaymentMethods();
        $data['banks'] = Bank::getBanks();

        $date_range = $request->input('date_range');
        $date_range = (array) $date_range;
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
                $item->inv_typeddd = $this->getInvType($item->user_id);
                $item->referer = $this->getReferer($item->user_id);
                $item->referer_topups = $this->getRefererTopups($item->user_id);
                $item->remarks = '';

                // if ($item->next_pay_date <=  $item->paymentsss_dates[5]) {
                $item->inv_comm = json_decode(json_encode($item->referer), true);
                $item->inv_comm = array_column($item->inv_comm, 'inv_comm');

                $item->topup_comm = json_decode(json_encode($item->referer_topups), true);
                $item->topup_comm = array_column($item->topup_comm, 'topup_comm');

                // // SUM ALL THE RELEVANT INVESTMENT COMMISSIONS AND GET THE TOTAL
                $item->inv_comm_sum = 0;
                foreach ($item->inv_comm as $key => $value) {
                    $item->inv_comm_sum += $value;
                }

                // SUM ALL THE RELEVANT TOPUP COMMISSIONS AND GET THE TOTAL
                $item->topup_comm_sum = 0;
                foreach ($item->topup_comm as $key => $value) {
                    $item->topup_comm_sum += $value;
                }

                $item->tot_comm = $item->inv_comm_sum + $item->topup_comm_sum;

                $item->to_be_paid = $this->getNextPayment($item->user_id);

                $item->to_be_paid = $this->getNextPayment($item->user_id) + $item->tot_comm;

                return $item;
            });

            $data['pay_mode'] = 'NO SELECTION';
            $data['pay_bank'] = 'NO SELECTION';

            $data['bank_idd'] = '';
            $data['pay_id'] = '';

            $data['today_due_payment_report'] = $data['due_payments_report']->whereBetween('next_pay_date', array($data['start_date'], $data['end_date']))
                ->where('to_be_paid', '>', 0);

            echo "<pre>";
            print_r($data['today_due_payment_report']);
            exit;

            $data['meta'] = ['Payment Date' => $data['start_date'] . ' To ' . $data['end_date']];

            $data['columns'] = [
                'Account No' => 'account_no', 'Name' => 'name', 'ID Number' => 'id_no', 'Phone Number' => 'telephone',
                'Mode of Payment' => 'method_name', 'Bank' => 'bank_name', 'Bank account' => 'pay_bank_acc',
                'MPESA Number' => 'pay_mpesa_no', 'Amount' => 'to_be_paid', 'Payment Date' => 'next_pay_date', 'Remarks' => 'remarks'
            ];

            $data['title'] = "Due Payments Report";
            // $download_excel = ExcelReport::of($data['title'], $data['meta'],  $data['today_due_payment_report'], $data['columns'])
            //     // ->limit(20)
            //     ->download('interweb_due_payments_report' . '_' . $data['start_date'] . ' to ' . $data['end_date']);

            // return $download_excel;
        } elseif (($date_range != '') && ($pay_mode_id != '') && ($bank_id == '')) {

            $data['type'] = 2;

            $data['due_payments_report'] = Report::duePaymentsReport();

            $data['due_payments_report']->map(function ($item) {

                $item->next_pay_date = $this->getNextPayDate($item->user_id);
                $item->inv_typeddd = $this->getInvType($item->user_id);
                $item->referer = $this->getReferer($item->user_id);
                $item->referer_topups = $this->getRefererTopups($item->user_id);
                $item->remarks = '';

                $item->inv_comm = json_decode(json_encode($item->referer), true);
                $item->inv_comm = array_column($item->inv_comm, 'inv_comm');

                $item->topup_comm = json_decode(json_encode($item->referer_topups), true);
                $item->topup_comm = array_column($item->topup_comm, 'topup_comm');

                // // SUM ALL THE RELEVANT INVESTMENT COMMISSIONS AND GET THE TOTAL
                $item->inv_comm_sum = 0;
                foreach ($item->inv_comm as $key => $value) {
                    $item->inv_comm_sum += $value;
                }

                // SUM ALL THE RELEVANT TOPUP COMMISSIONS AND GET THE TOTAL
                $item->topup_comm_sum = 0;
                foreach ($item->topup_comm as $key => $value) {
                    $item->topup_comm_sum += $value;
                }

                $item->tot_comm = $item->inv_comm_sum + $item->topup_comm_sum;

                $item->to_be_paid = $this->getNextPayment($item->user_id) + $item->tot_comm;

                return $item;
            });

            $data['today_due_payment_report'] = $data['due_payments_report']
                ->where('pay_mode_id', '=', $pay_mode_id)
                ->whereBetween(
                    'next_pay_date',
                    array($data['start_date'], $data['end_date'])
                )
                ->where('to_be_paid', '>', 0);

            $data['pay_bank'] = 'NO SELECTION';

            $data['bank_idd'] = '';
            $data['pay_id'] =  $pay_mode_id;

            $data['pay_mode'] = $data['payment_modes']->where('method_id', '=', $pay_mode_id)->pluck('method_name')->first();

            $user = \Auth::user();

            logger($data['pay_mode'] . ' due payments report from ' . $data['start_date'] . ' to ' . $data['end_date'] .  ' run by ' . $user->name);

            $data['meta'] = ['Payment Date' => $data['start_date'] . ' To ' . $data['end_date']];

            if ($pay_mode_id == 1) {

                $data['columns'] = [
                    'Account No' => 'account_no', 'Name' => 'name', 'ID Number' => 'id_no', 'Phone Number' => 'telephone',
                    'Mode of Payment' => 'method_name', 'MPESA Number' => 'pay_mpesa_no', 'Amount' => 'to_be_paid', 'Payment Date' => 'next_pay_date', 'Remarks' => 'remarks'
                ];

                $data['title'] = "Due Payments Report";
                // $download_excel = ExcelReport::of($data['title'], $data['meta'],  $data['today_due_payment_report'], $data['columns'])
                //     ->download('interweb_due_payments_report' . '_' . $data['start_date'] . ' to ' . $data['end_date']);

                // return $download_excel;
            } else {

                $data['columns'] = [
                    'Account No' => 'account_no', 'Name' => 'name', 'ID Number' => 'id_no', 'Phone Number' => 'telephone',
                    'Mode of Payment' => 'method_name', 'Bank' => 'bank_name', 'Bank Account' => 'pay_bank_acc', 'Amount' => 'to_be_paid', 'Payment Date' => 'next_pay_date', 'Remarks' => 'remarks'
                ];

                $data['title'] = "Due Payments Report";
                // $download_excel = ExcelReport::of($data['title'], $data['meta'],  $data['today_due_payment_report'], $data['columns'])
                //     ->download('payments');

                // return $download_excel;
            }
        }
        if (!empty($date_range) && !empty($pay_mode_id) && !empty($bank_id)) {

            $data['type'] = 3;
            $data['bank_idd'] = $bank_id;

            $data['due_payments_report'] = Report::duePaymentsReport();

            $data['due_payments_report']->map(function ($item) {

                $item->next_pay_date = $this->getNextPayDate($item->user_id);
                $item->inv_typeddd = $this->getInvType($item->user_id);
                $item->referer = $this->getReferer($item->user_id);
                $item->referer_topups = $this->getRefererTopups($item->user_id);
                $item->remarks = '';

                $item->inv_comm = json_decode(json_encode($item->referer), true);
                $item->inv_comm = array_column($item->inv_comm, 'inv_comm');

                $item->topup_comm = json_decode(json_encode($item->referer_topups), true);
                $item->topup_comm = array_column($item->topup_comm, 'topup_comm');

                // SUM ALL THE RELEVANT INVESTMENT COMMISSIONS AND GET THE TOTAL
                $item->inv_comm_sum = 0;
                foreach ($item->inv_comm as $key => $value) {
                    $item->inv_comm_sum += $value;
                }

                // SUM ALL THE RELEVANT TOPUP COMMISSIONS AND GET THE TOTAL
                $item->topup_comm_sum = 0;
                foreach ($item->topup_comm as $key => $value) {
                    $item->topup_comm_sum += $value;
                }

                $item->tot_comm = $item->inv_comm_sum + $item->topup_comm_sum;

                $item->to_be_paid = $this->getNextPayment($item->user_id) + $item->tot_comm;

                return $item;
            });

            $data['today_due_payment_report'] = $data['due_payments_report']
                ->where('pay_mode_id', '=', $pay_mode_id)
                ->where('bank_id', '=', $bank_id)
                ->whereBetween(
                    'next_pay_date',
                    array($data['start_date'], $data['end_date'])
                )
                ->where('to_be_paid', '>', 0);

            $data['pay_bank'] = 'NO SELECTION';

            $data['bank_idd'] = $bank_id;
            $data['pay_id'] =  $pay_mode_id;

            $data['pay_mode'] = $data['payment_modes']->where('method_id', '=', $pay_mode_id)->pluck('method_name')->first();

            $data['pay_bank'] = $data['banks']->where('bank_id', '=', $bank_id)->pluck('bank_name')->first();

            $user = \Auth::user();

            logger($data['pay_bank'] . ' due payments report from ' . $data['start_date'] . ' to ' . $data['end_date'] .  ' run by ' . $user->name);

            $data['meta'] = ['Payment Date' => $data['start_date'] . ' To ' . $data['end_date']];

            $data['columns'] = [
                'Account No' => 'account_no', 'Name' => 'name', 'ID Number' => 'id_no', 'Phone Number' => 'telephone',
                'Mode of Payment' => 'method_name', 'Bank' => 'bank_name', 'Bank Account' => 'pay_bank_acc',
                'Amount' => 'to_be_paid', 'Payment Date' => 'next_pay_date', 'Remarks' => 'remarks'
            ];

            $data['title'] = "Due Payments Report";
            // $download_excel = ExcelReport::of($data['title'], $data['meta'],  $data['today_due_payment_report'], $data['columns'])
            //     ->download('interweb_due_payments_report' . '_' . $data['start_date'] . ' to ' . $data['end_date']);

            // return $download_excel;
        }

        return view('reports.view')->with($data);
    }

    public function downloadExcel(Request $request)
    {
        if (!auth()->user()->can('reports.manage')) {
            abort(401, 'Unauthorized action.');
        }
        $data['clients'] = User::getClients();
        $data['payment_modes'] = PaymentMethod::getPaymentMethods();
        $data['banks'] = Bank::getBanks();

        $date_range = $request->input('date_range');
        $date_range = (array) $date_range;
        $date_range = str_replace(' - ', ',', $date_range);

        foreach ($date_range as $key => $value) {
            $date_range = $value;
        }

        $date_range = explode(',', $date_range);

        $data['start_date'] = date('Y-m-d', strtotime($date_range[0]));
        $data['end_date'] = date('Y-m-d', strtotime($date_range[1]));

        // print_r($data['start_date']);
        // exit;

        $today = Carbon::now()->toDateString();
        $pay_mode_id = $request->input('pay_mode_id');
        $bank_id = $request->input('bank_id');

        if (($date_range != '') && ($pay_mode_id == '') && ($bank_id == '')) {

            $data['type'] = 1;

            $data['due_payments_report'] = Report::duePaymentsReport()->where('total_due_payments', '>', 0);

            $data['due_payments_report']->map(function ($item) {

                $item->next_pay_date = $this->getNextPayDate($item->user_id);
                $item->inv_typeddd = $this->getInvType($item->user_id);
                $item->referer = $this->getReferer($item->user_id);
                $item->referer_topups = $this->getRefererTopups($item->user_id);
                $item->remarks = '';

                // if ($item->next_pay_date <=  $item->paymentsss_dates[5]) {
                $item->inv_comm = json_decode(json_encode($item->referer), true);
                $item->inv_comm = array_column($item->inv_comm, 'inv_comm');

                $item->topup_comm = json_decode(json_encode($item->referer_topups), true);
                $item->topup_comm = array_column($item->topup_comm, 'topup_comm');

                // // SUM ALL THE RELEVANT INVESTMENT COMMISSIONS AND GET THE TOTAL
                $item->inv_comm_sum = 0;
                foreach ($item->inv_comm as $key => $value) {
                    $item->inv_comm_sum += $value;
                }

                // SUM ALL THE RELEVANT TOPUP COMMISSIONS AND GET THE TOTAL
                $item->topup_comm_sum = 0;
                foreach ($item->topup_comm as $key => $value) {
                    $item->topup_comm_sum += $value;
                }

                $item->tot_comm = $item->inv_comm_sum + $item->topup_comm_sum;

                $item->to_be_paid = $this->getNextPayment($item->user_id);

                $item->to_be_paid = $this->getNextPayment($item->user_id) + $item->tot_comm;

                return $item;
            });

            $data['pay_mode'] = 'NO SELECTION';
            $data['pay_bank'] = 'NO SELECTION';
            $data['bank_idd'] = $bank_id;

            $data['today_due_payment_report'] = $data['due_payments_report']->whereBetween('next_pay_date', array($data['start_date'], $data['end_date']))
                ->where('to_be_paid', '>', 0);

            $data['meta'] = ['Payment Date' => $data['start_date'] . ' To ' . $data['end_date']];

            $data['columns'] = [
                'Account No' => 'account_no', 'Name' => 'name', 'ID Number' => 'id_no', 'Phone Number' => 'telephone',
                'Mode of Payment' => 'method_name', 'Bank' => 'bank_name', 'Bank account' => 'pay_bank_acc',
                'MPESA Number' => 'pay_mpesa_no', 'Amount' => 'to_be_paid', 'Payment Date' => 'next_pay_date', 'Remarks' => 'remarks'
            ];

            $data['title'] = "Due Payments Report";
            $download_excel = ExcelReport::of($data['title'], $data['meta'],  $data['today_due_payment_report'], $data['columns'])
                // ->limit(20)
                ->download('interweb_due_payments_report' . '_' . $data['start_date'] . ' to ' . $data['end_date']);

            return $download_excel;
        } elseif (($date_range != '') && ($pay_mode_id != '') && ($bank_id == '')) {

            $data['type'] = 2;
            $data['pay_id'] = $pay_mode_id;
            $data['bank_idd'] = $bank_id;

            $data['due_payments_report'] = Report::duePaymentsReport();

            $data['due_payments_report']->map(function ($item) {

                $item->next_pay_date = $this->getNextPayDate($item->user_id);
                $item->inv_typeddd = $this->getInvType($item->user_id);
                $item->referer = $this->getReferer($item->user_id);
                $item->referer_topups = $this->getRefererTopups($item->user_id);
                $item->remarks = '';

                $item->inv_comm = json_decode(json_encode($item->referer), true);
                $item->inv_comm = array_column($item->inv_comm, 'inv_comm');

                $item->topup_comm = json_decode(json_encode($item->referer_topups), true);
                $item->topup_comm = array_column($item->topup_comm, 'topup_comm');

                // // SUM ALL THE RELEVANT INVESTMENT COMMISSIONS AND GET THE TOTAL
                $item->inv_comm_sum = 0;
                foreach ($item->inv_comm as $key => $value) {
                    $item->inv_comm_sum += $value;
                }

                // SUM ALL THE RELEVANT TOPUP COMMISSIONS AND GET THE TOTAL
                $item->topup_comm_sum = 0;
                foreach ($item->topup_comm as $key => $value) {
                    $item->topup_comm_sum += $value;
                }

                $item->tot_comm = $item->inv_comm_sum + $item->topup_comm_sum;

                $item->to_be_paid = $this->getNextPayment($item->user_id) + $item->tot_comm;

                return $item;
            });

            $data['today_due_payment_report'] = $data['due_payments_report']
                ->where('pay_mode_id', '=', $pay_mode_id)
                ->whereBetween(
                    'next_pay_date',
                    array($data['start_date'], $data['end_date'])
                )
                ->where('to_be_paid', '>', 0);

            $data['pay_mode'] = 'NO SELECTION';
            $data['pay_bank'] = 'NO SELECTION';
            $data['pay_mode_id'] = $pay_mode_id;

            $data['pay_mode'] = $data['payment_modes']->where('method_id', '=', $pay_mode_id)->pluck('method_name')->first();

            $user = \Auth::user();

            logger($data['pay_mode'] . ' due payments report from ' . $data['start_date'] . ' to ' . $data['end_date'] .  ' run by ' . $user->name);

            $data['meta'] = ['Payment Date' => $data['start_date'] . ' To ' . $data['end_date']];

            if ($pay_mode_id == 1) {

                $data['columns'] = [
                    'Account No' => 'account_no', 'Name' => 'name', 'ID Number' => 'id_no', 'Phone Number' => 'telephone',
                    'Mode of Payment' => 'method_name', 'MPESA Number' => 'pay_mpesa_no', 'Amount' => 'to_be_paid', 'Payment Date' => 'next_pay_date', 'Remarks' => 'remarks'
                ];

                $data['title'] = "Due Payments Report";
                $download_excel = ExcelReport::of($data['title'], $data['meta'],  $data['today_due_payment_report'], $data['columns'])
                    ->download('interweb_due_payments_report' . '_' . $data['start_date'] . ' to ' . $data['end_date']);

                return $download_excel;
            } else {

                $data['columns'] = [
                    'Account No' => 'account_no', 'Name' => 'name', 'ID Number' => 'id_no', 'Phone Number' => 'telephone',
                    'Mode of Payment' => 'method_name', 'Bank' => 'bank_name', 'Bank Account' => 'pay_bank_acc', 'Amount' => 'to_be_paid', 'Payment Date' => 'next_pay_date', 'Remarks' => 'remarks'
                ];

                $data['title'] = "Due Payments Report";
                $download_excel = ExcelReport::of($data['title'], $data['meta'],  $data['today_due_payment_report'], $data['columns'])
                    ->download('payments');

                return $download_excel;
            }
        }
        if (!empty($date_range) && !empty($pay_mode_id) && !empty($bank_id)) {

            $data['type'] = 3;
            $data['bank_idd'] = $bank_id;

            $data['due_payments_report'] = Report::duePaymentsReport();

            $data['due_payments_report']->map(function ($item) {

                $item->next_pay_date = $this->getNextPayDate($item->user_id);
                $item->inv_typeddd = $this->getInvType($item->user_id);
                $item->referer = $this->getReferer($item->user_id);
                $item->referer_topups = $this->getRefererTopups($item->user_id);
                $item->remarks = '';

                $item->inv_comm = json_decode(json_encode($item->referer), true);
                $item->inv_comm = array_column($item->inv_comm, 'inv_comm');

                $item->topup_comm = json_decode(json_encode($item->referer_topups), true);
                $item->topup_comm = array_column($item->topup_comm, 'topup_comm');

                // SUM ALL THE RELEVANT INVESTMENT COMMISSIONS AND GET THE TOTAL
                $item->inv_comm_sum = 0;
                foreach ($item->inv_comm as $key => $value) {
                    $item->inv_comm_sum += $value;
                }

                // SUM ALL THE RELEVANT TOPUP COMMISSIONS AND GET THE TOTAL
                $item->topup_comm_sum = 0;
                foreach ($item->topup_comm as $key => $value) {
                    $item->topup_comm_sum += $value;
                }

                $item->tot_comm = $item->inv_comm_sum + $item->topup_comm_sum;

                $item->to_be_paid = $this->getNextPayment($item->user_id) + $item->tot_comm;

                return $item;
            });

            $data['today_due_payment_report'] = $data['due_payments_report']
                ->where('pay_mode_id', '=', $pay_mode_id)
                ->where('bank_id', '=', $bank_id)
                ->whereBetween(
                    'next_pay_date',
                    array($data['start_date'], $data['end_date'])
                )
                ->where('to_be_paid', '>', 0);

            $data['pay_bank'] = $data['banks']->where('bank_id', '=', $bank_id)->pluck('bank_name')->first();

            $user = \Auth::user();

            logger($data['pay_bank'] . ' due payments report from ' . $data['start_date'] . ' to ' . $data['end_date'] .  ' run by ' . $user->name);

            $data['meta'] = ['Payment Date' => $data['start_date'] . ' To ' . $data['end_date']];

            $data['columns'] = [
                'Account No' => 'account_no', 'Name' => 'name', 'ID Number' => 'id_no', 'Phone Number' => 'telephone',
                'Mode of Payment' => 'method_name', 'Bank' => 'bank_name', 'Bank Account' => 'pay_bank_acc',
                'Amount' => 'to_be_paid', 'Payment Date' => 'next_pay_date', 'Remarks' => 'remarks'
            ];

            $data['title'] = "Due Payments Report";
            $download_excel = ExcelReport::of($data['title'], $data['meta'],  $data['today_due_payment_report'], $data['columns'])
                ->download('interweb_due_payments_report' . '_' . $data['start_date'] . ' to ' . $data['end_date']);

            return $download_excel;
        }

        return view('reports.view')->with($data);
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