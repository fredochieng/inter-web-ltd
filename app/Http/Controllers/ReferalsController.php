<?php

namespace App\Http\Controllers;

use App\Referals;
use Illuminate\Http\Request;

class ReferalsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
        //
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
