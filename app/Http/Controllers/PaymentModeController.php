<?php

namespace App\Http\Controllers;

use App\Model\PaymentMode;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PaymentModeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $payment_mode = new PaymentMode();
        $payment_mode->user_id = $request->input('user_id');
        $payment_mode->pay_mode_id = $request->input('pay_mode_id');
        $payment_mode->pay_bank_id = $request->input('pay_bank_id');
        $payment_mode->pay_bank_acc = $request->input('pay_bank_acc');
        $payment_mode->pay_mpesa_no = $request->input('pay_mpesa_no');

        $payment_mode->save();

        toast('New payment mode added successfully', 'success', 'top-right');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentMode  $paymentMode
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentMode $paymentMode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentMode  $paymentMode
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentMode $paymentMode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentMode  $paymentMode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentMode $paymentMode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentMode  $paymentMode
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentMode $paymentMode)
    {
        //
    }
}