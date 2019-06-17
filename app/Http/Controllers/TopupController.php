<?php

namespace App\Http\Controllers;

use App\Model\Topup;
use App\Model\InvestmentMode;
use App\Model\Bank;
use User;
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
    { }

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

            $topup_date = new Carbon(Carbon::now('Africa/Nairobi')->toDateString());
            $number_of_days = $next_pay_date->diffInDays($topup_date);

            $inv_type = $request->input('inv_type_id');
            $topup->account_id = $request->input('account_no_id');
            $topup->topup_amount = $request->input('topup_amount');
            $topup->inv_mode_id = $request->input('inv_mode_id');
            $topup->mpesa_trans_code = $request->input('mpesa_trans_code');
            $topup->inv_bank_id = $request->input('inv_bank_id');
            $topup->bank_trans_code = $request->input('bank_trans_code');
            $topup->inv_bank_id = $request->input('inv_cheq_bank_id');
            $topup->cheque_no = $request->input('cheque_no');
            $topup->topped_at = Carbon::now()->toDateString();
            $interest_rate = 0.2;
            $days = 30;
            $interest = $interest_rate * $topup->topup_amount;
            $top_int = floor(($interest * $number_of_days) / $days);

            if ($inv_type == 1) {

                if ($data['investments']->topped_up  == 0) {
                    $next_pay_amount = $data['investments']->monthly_amount;
                } else {

                    $next_pay_amount = $data['investments']->updated_monthly_pay;
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

                // echo $new_due_payments;
                // echo
                //     exit;

                // $multiplier = $inv_duration - 1;
                // $separator = ',';
                // $updated_pay = implode($separator, array_fill(0, $multiplier, $new_monthly_pay));
                // $updated_pay = (array) $updated_pay;

                // $updated_pay = json_encode($updated_pay);


                // echo"<pre>";
                // print_r($updated_pay);
                // exit;

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