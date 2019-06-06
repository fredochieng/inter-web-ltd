<?php

namespace App\Http\Controllers;

use App\Model\Payment;
use App\Model\PaymentMethod;
use App\Model\Report;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['payments'] = Payment::getPayments();
        // echo "<pre>";
        // print_r($data['payments']);
        // exit;
        return view('payments.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['payment_methods'] = PaymentMethod::getPaymentMethods();
        return view('payments.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "account_no_id" => ['required'],
            'payment_amount' => ['required']
        ]);

        if ($validator->fails()) {
            DB::rollBack();
            Alert::error('New Payment', 'Oops!!! An error ocurred while adding new payment');
            return back();
        } else {

            try {
                $payment = new Payment();
                $payment->account_no_id = $request->input('account_no_id');
                $payment->payment_amount = $request->input('payment_amount');
                $generated_transaction_code = strtoupper(str_random(8));
                $payment->trans_id = $generated_transaction_code;
                $payment->payment_method_id = $request->input('payment_method_id');

                $payment->save();
                DB::beginTransaction();

                $just_saved_account_id = $payment->account_no_id;

                $account_due_payments = DB::table('accounts')
                ->select(
                    DB::raw('accounts.*'),
                    DB::raw('investments.*')
                    )
                    ->leftJoin('investments', 'accounts.id', 'investments.account_no_id')
                    ->where('accounts.id', '=',  $payment->account_no_id)
                    ->first();

                    // $investments_bal = $account_due_payments->total_investments;
                    // $investments_bal = $investments_bal +  $payment->payment_amount;

                    // $interests_bal = $account_due_payments->total_due_interests;
                    // $interests_bal = $interests_bal + $payment->payout;

                    $balanace = $account_due_payments->total_due_payments;
                    $balance = $balanace - $payment->payment_amount;

                    $data['account_balance_array'] = array(
                        'total_due_payments' => $balance
                    );

                    $acc_balances = DB::table('accounts')->where('id',  $just_saved_account_id)
                    ->update($data['account_balance_array']);
                    DB::commit();
                    $alert_subject = 'New Payment';
                    $alert_message = 'Payment added successfully';

                Alert::success('New Payment', 'Payment added successfully');
                return back();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                return back();
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
