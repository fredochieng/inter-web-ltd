<?php

namespace App\Http\Controllers;

use App\Model\Topup;
use App\Model\InvestmentMode;
use App\Model\Bank;
use User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use Session;
use Carbon\Carbon;

class TopupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('topups.view')) {
            abort(401, 'Unauthorized action.');
        }
        $data['topups'] = Topup::getTopups();

        return view('topups.index')->with($data);
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
        try {
            $topup = new Topup();
            $inv_id = $request->input('inv_id');
            $data['investments'] = Topup::getInvestments($inv_id);

            $next_pay_date = new Carbon(Session::get('next_pay_day'));
            $pay_times = $data['investments']->payment_times;

            $topup_date = $request->input('topup_date');
            $number_of_days = $next_pay_date->diffInDays($topup_date);

            $inv_type = $request->input('inv_type_id');
            $user_id = $request->input('user_id');
            $topup->account_id = $request->input('account_no_id');
            $topup->topup_amount = $request->input('topup_amount');
            $topup->served_by = Auth::user()->id;
            $topup->inv_mode_id = $request->input('inv_mode_id');
            $topup->mpesa_trans_code = $request->input('mpesa_trans_code');
            $topup->inv_bank_id = $request->input('inv_bank_id');
            $topup->bank_trans_code = $request->input('bank_trans_code');
            //  $topup->inv_bank_id = $request->input('inv_cheq_bank_id');
            $topup->cheque_no = $request->input('cheque_no');
            $topup->topped_at = $request->input('topup_date');
            $referee_id = $request->input('referee_id');

            $interest_rate = 0.2;
            $days = 30;
            $interest = $interest_rate * $topup->topup_amount;
            $top_int = floor(($interest * $number_of_days) / $days);

            $topup_comm_per = 0.05;
            $topup_comm = $topup_comm_per *  $topup->topup_amount;
            $tot_topup_comm = $topup_comm * 6;
            $topup->topup_comm = $topup_comm;
            $topup->tot_topup_comm = $tot_topup_comm;

            if (!empty($referee_id)) {
                $referee_data = DB::table('accounts')
                    ->select(
                        DB::raw('accounts.*'),
                        DB::raw('users.id as referee_id')
                    )
                    ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                    ->where('users.id', '=', $referee_id)
                    ->first();

                $account_id = $referee_data->id;
                $due_pay = $referee_data->total_due_payments;
                $new_due_pay = $due_pay + $topup_comm;

                $acc_bal = array(

                    'total_due_payments' => $new_due_pay
                );

                $acc_balances = DB::table('accounts')->where('id', $account_id)
                    ->update($acc_bal);
            }

            if ($inv_type == 1) {

                if ($data['investments']->topped_up  == 0) {
                    $next_pay_amount = $data['investments']->monthly_amount;
                } else {
                    $updated_next_pay = $data['investments']->updated_next_pay;

                    // CHECK IF THE UPDATED NEXT PAY HAS BEEN PAID
                    $data['client_payments'] = DB::table('payments')
                        ->select(
                            DB::raw('payments.*'),
                            DB::raw('accounts.*'),
                            DB::raw('users.*')
                        )
                        ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                        ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                        ->where('users.id', '=', $user_id)
                        ->where('payments.total_payment', '=', $updated_next_pay)
                        ->orderBy('payments.payment_id', 'desc')->first();

                    if ($data['client_payments']) {
                        $next_pay_amount =  $data['investments']->updated_monthly_pay;
                    } else {
                        $next_pay_amount =  $data['investments']->updated_next_pay;
                    }
                }

                $data['updated_next_pay'] = $next_pay_amount + $top_int;

                $inv_amount = $data['investments']->investment_amount;
                $topped_amount = $data['investments']->topup_amount;

                $inv_duration = $data['investments']->investment_duration;
                $tot_topups = $topped_amount + $topup->topup_amount;

                $tot_investments = $inv_amount + $topup->topup_amount;
                $new_monthly_pay = $interest_rate * $tot_investments;

                $pay_times_remaining = $inv_duration - $pay_times - 1;
                $new_tot_monthly_payable = $new_monthly_pay * $pay_times_remaining;
                $new_due_payments = $new_tot_monthly_payable + $data['updated_next_pay'];

                $topup->save();

                DB::table('investments')->where('account_no_id', $topup->account_id)->update(
                    [
                        'investment_amount' => $tot_investments
                    ]
                );

                DB::table('payment_schedule')->where('account_no_id', $topup->account_id)->update(
                    [
                        'topped_up' => 1, 'topup_amount' => $tot_topups,
                        'updated_next_pay' => $data['updated_next_pay'], 'updated_monthly_pay' => $new_monthly_pay
                    ]
                );

                DB::table('accounts')->where('id', $topup->account_id)->update(
                    [
                        'total_due_payments' => $new_due_payments
                    ]
                );
                toast('New topup added successfully', 'success', 'top-right');
                return back();
            } elseif ($inv_type == 2) {

                // GET LAST PAYMENT DATE
                $last_pay_date = new Carbon(Session::get('last_pay_date'));

                $interest_rate = 0.2;
                $days = 30;
                $number_of_days = $last_pay_date->diffInDays($topup_date);
                $interest = $interest_rate * $topup->topup_amount;
                $top_int = floor(($interest * $number_of_days) / $days);

                $next_pay_amount = $data['investments']->tot_payable_amnt;
                $updated_next_pay = $next_pay_amount + $top_int;

                //GET CLIENT TOTAL INVESTMENT AND UPDATE EITH THE NEW VALUE
                $inv_amount = $data['investments']->investment_amount;
                $topped_amount = $data['investments']->topup_amount;
                $tot_topups = $topped_amount + $topup->topup_amount;
                $tot_investments = $inv_amount + $topup->topup_amount;

                $topup->save();

                DB::table('investments')->where('account_no_id', $topup->account_id)->update(
                    [
                        'investment_amount' => $tot_investments
                    ]
                );

                DB::table('payment_schedule')->where('account_no_id', $topup->account_id)->update(
                    [
                        'topped_up' => 1, 'topup_amount' => $tot_topups,
                        'tot_payable_amnt' => $updated_next_pay
                    ]
                );

                DB::table('accounts')->where('id', $topup->account_id)->update(
                    [
                        'total_due_payments' => $updated_next_pay
                    ]
                );
                toast('New topup added successfully', 'success', 'top-right');
                return back();
            } elseif ($inv_type == 3) {

                $inv_subtype_id = $request->input('inv_subtype_id');
                if ($inv_subtype_id == 1) {

                    if ($data['investments']->topped_up  == 0) {
                        $next_pay_amount = $data['investments']->monthly_amount;
                    } else {
                        $updated_next_pay = $data['investments']->updated_next_pay;

                        // CHECK IF THE UPDATED NEXT PAY HAS BEEN PAID
                        $data['client_payments'] = DB::table('payments')
                            ->select(
                                DB::raw('payments.*'),
                                DB::raw('accounts.*'),
                                DB::raw('users.*')
                            )
                            ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                            ->where('users.id', '=', $user_id)
                            ->where('payments.total_payment', '=', $updated_next_pay)
                            ->orderBy('payments.payment_id', 'desc')->first();

                        if ($data['client_payments']) {
                            $next_pay_amount =  $data['investments']->updated_monthly_pay;
                        } else {
                            $next_pay_amount =  $data['investments']->updated_next_pay;
                        }
                    }

                    $data['updated_next_pay'] = $next_pay_amount + $top_int;
                    // $next_pay_amount =  $data['investments']->updated_next_pay;

                    $inv_amount = $data['investments']->monthly_inv;
                    $overall_inv = $data['investments']->investment_amount;;

                    $topped_amount = $data['investments']->topup_amount;

                    $inv_duration = $data['investments']->monthly_duration;
                    $tot_topups = $topped_amount + $topup->topup_amount;

                    $tot_investments = $inv_amount + $topup->topup_amount;
                    $new_tot_overall_inv = $overall_inv + $topup->topup_amount;
                    $new_monthly_pay = $interest_rate * $tot_investments;

                    $pay_times_remaining = $inv_duration - $pay_times - 1;
                    $new_tot_monthly_payable = $new_monthly_pay * $pay_times_remaining;
                    $comp_due_amount = $data['investments']->tot_comp_amount;

                    $new_due_payments = $new_tot_monthly_payable + $data['updated_next_pay'] + $comp_due_amount;
                    // echo $new_tot_overall_inv;
                    // exit;

                    $topup->save();

                    DB::table('investments')->where('account_no_id', $topup->account_id)->update(
                        [
                            'monthly_inv' => $tot_investments, 'investment_amount' => $new_tot_overall_inv
                        ]
                    );

                    DB::table('payment_schedule')->where('account_no_id', $topup->account_id)->update(
                        [
                            'topped_up' => 1, 'topup_amount' => $tot_topups, 'tot_payable_amnt' => $new_due_payments,
                            'updated_next_pay' => $data['updated_next_pay'], 'updated_monthly_pay' => $new_monthly_pay
                        ]
                    );

                    DB::table('accounts')->where('id', $topup->account_id)->update(
                        [
                            'total_due_payments' => $new_due_payments
                        ]
                    );
                    toast('New topup added successfully', 'success', 'top-right');
                    return back();
                } else {

                    // GET LAST PAYMENT DATE
                    $last_pay_date = new Carbon(Session::get('last_pay_date'));
                    $last_pay_date = Carbon::parse($last_pay_date)->toDateString();

                    $interest_rate = 0.2;
                    $days = 30;
                    $topup_date = new Carbon(Carbon::now('Africa/Nairobi')->toDateString());
                    $number_of_days = Carbon::parse($last_pay_date)->diffInDays($topup_date);

                    $interest = $interest_rate * $topup->topup_amount;

                    $top_int = floor(($interest * $number_of_days) / $days);

                    $next_pay_amount = $data['investments']->tot_comp_amount;
                    $tot_overall__due_payments = $data['investments']->tot_payable_amnt;
                    $overall_inv = $data['investments']->investment_amount;
                    $investments = $data['investments']->compounded_inv;
                    $updated_next_pay = $next_pay_amount + $top_int;

                    // //GET CLIENT TOTAL INVESTMENT AND UPDATE EITH THE NEW VALUE
                    $new_tot_overall_inv = $overall_inv + $topup->topup_amount;


                    $topped_amount = $data['investments']->topup_amount;
                    $tot_topups = $topped_amount + $topup->topup_amount;
                    $new_comp_investments = $investments + $topup->topup_amount;
                    $new_overall_due_apyments = $tot_overall__due_payments + $top_int;

                    $topup->save();

                    DB::table('investments')->where('account_no_id', $topup->account_id)->update(
                        [
                            'investment_amount' => $new_tot_overall_inv, 'compounded_inv' => $new_comp_investments
                        ]
                    );

                    DB::table('payment_schedule')->where('account_no_id', $topup->account_id)->update(
                        [
                            'topped_up' => 1, 'topup_amount' => $tot_topups,
                            'tot_payable_amnt' => $new_overall_due_apyments, 'tot_comp_amount' => $updated_next_pay
                        ]
                    );

                    DB::table('accounts')->where('id', $topup->account_id)->update(
                        [
                            'total_due_payments' => $new_overall_due_apyments
                        ]
                    );
                    toast('New topup added successfully', 'success', 'top-right');
                    return back();
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Topup  $topup
     * @return \Illuminate\Http\Response
     */
    public function show(Topup $topup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Topup  $topup
     * @return \Illuminate\Http\Response
     */
    public function edit(Topup $topup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Topup  $topup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Topup $topup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Topup  $topup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Topup $topup)
    {
        //
    }
}