<?php

namespace App\Http\Controllers;

use App\Model\Referals;
use Illuminate\Http\Request;
use App\Model\Topup;
use DB;

class ReferalsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { }

    public function getRestrictions()
    {
        $data['clients'] = Referals::getRestrictedClients();
        // echo "<pre>";
        // print_r($data['clients']);
        // exit;
        return view('users.commission_restrictions')->with($data);
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
        $referal = new Referals();
        $referal->id_no = $request->input('id_no');
        $referal->phone = $request->input('phone');
        $referal->comm_times = $request->input('comm');
        $referal->save();

        toast('New referal restriction added successfully', 'success', 'top-right');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Referals  $referals
     * @return \Illuminate\Http\Response
     */
    public function show(Referals $referals)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Referals  $referals
     * @return \Illuminate\Http\Response
     */
    public function edit(Referals $referals)
    {
        // FETCH CLIENTS DETAILS
        $data['referrals'] = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('users.id as referee_id'),
                DB::raw('users_details.*'),
                DB::raw('accounts.*'),
                DB::raw('accounts.id AS accnt_id'),
                DB::raw('investments.*'),
                DB::raw('user_pay_modes.*'),
                DB::raw('inv_types.*'),
                DB::raw('payment_schedule.*'),
                DB::raw('payment_schedule.monthly_amount'),
                DB::raw('payments.*'),
                DB::raw('payment_methods.*'),
                DB::raw('banks.*')
            )
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
            ->leftJoin('investments', 'accounts.id', '=', 'investments.account_no_id')
            ->leftJoin('inv_types', 'investments.inv_type_id', '=', 'inv_types.inv_id')
            ->leftJoin('user_pay_modes', 'users.id', '=', 'user_pay_modes.user_id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            ->leftJoin('payments', 'accounts.id', '=', 'payments.account_no_id')
            ->leftJoin('payment_methods', 'user_pay_modes.pay_mode_id', '=', 'payment_methods.method_id')
            ->leftJoin('banks', 'user_pay_modes.pay_bank_id', '=', 'banks.bank_id')
            ->where('refered_by', '=', $id)
            // ->where('investments.inv_status_id', '=', 1)
            ->first();

        //  echo "<pre>";
        //  print_r($data['referrals']);
        //  exit;

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Referals  $referals
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Referals $referals)
    {
        $rest_id = $request->input('rest');
        $id_no = $request->input('id_no');
        $phone = $request->input('phone_no');
        $comm_times = $request->input('comm_times');

        $rest_data = array(
            'id_no' => $id_no,
            'phone' => $phone,
            'comm_times' => $comm_times
        );
        $save_rest_data = DB::table('referal_restrictions')->where('rest_id', $rest_id)->update($rest_data);

        toast('Referal restriction updated successfully', 'success', 'top-right');
        return back();
    }

    public function updateUserReferal(Request $request)
    {
        $referee_id = $request->input('referer_id');
        $user_id = $request->input('user_id');
        $account_no_id = $request->input('accnt_no_id');
        $initial_inv = $request->input('initial_inv');

        $referals_restrictions = Referals::getRestrictedClients();

        $restricted_ids = array();
        foreach ($referals_restrictions as $key => $value) {
            $restricted_ids[] = $value->user_id;
        }

        $user_id = $request->input('user_id');
        // $account_no_id =  $request->input('account_id');

        $referer_data = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('users_details.*')
            )
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->where('users.id', '=', $referee_id)->first();

        $referer_idno  = $referer_data->id_no;

        if (in_array($referee_id, $restricted_ids)) {

            $restricted_client = DB::table('referal_restrictions')
                ->select(
                    DB::raw('referal_restrictions.*')
                )
                ->where('id_no', '=', $referer_idno)
                ->first();

            $comm_times = $restricted_client->comm_times;
            if ($comm_times > 0) {
                $comm_times = $restricted_client->comm_times;
            } elseif ($comm_times == 0) {
                $comm_times = 6;
            }
        } else {

            $comm_times = 6;
        }

        $inv_comm_per = 0.05;
        $inv_comm = $inv_comm_per * $initial_inv;
        $tot_inv_comm = $inv_comm * $comm_times;

        $investments_data = array(
            'inv_comm' => $inv_comm,
            'tot_inv_comm' => $tot_inv_comm
        );

        $update_investments = DB::table('investments')->where('account_no_id', $account_no_id)
            ->update($investments_data);

        $topups = DB::table('topups')
            ->select(
                DB::raw('topups.topup_id'),
                DB::raw('topups.account_id'),
                DB::raw('topups.topup_comm')
            )
            ->where('account_id', '=', $account_no_id)
            ->groupBy('topup_id')
            ->get();

        $topups->map(function ($item)  use ($comm_times) {
            $item->tot_comm  = $item->topup_comm * $comm_times;
            return $item;
        });

        foreach ($topups as $value) {
            DB::table('topups')
                ->where('topup_id', $value->topup_id)
                ->update(['tot_topup_comm' => $value->tot_comm]);
        }

        $new_user_id_referral = array(
            'refered_by' => $referee_id
        );

        $update_referral = DB::table('users')->where('id', $user_id)
            ->update($new_user_id_referral);

        toast('Client referral updated successfully', 'success', 'top-right');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Referals  $referals
     * @return \Illuminate\Http\Response
     */
    public function destroy(Referals $referals)
    {
        //
    }
}