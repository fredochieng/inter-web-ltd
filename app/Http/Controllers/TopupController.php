<?php

namespace App\Http\Controllers;

use App\Model\Topup;
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
        try {
            $topup = new Topup();
            $inv_id = $request->input('inv_id');
            $data['investments'] = Topup::getInvestments($inv_id);
            $next_pay_date= new Carbon(Session::get('next_pay_day'));
            $topup_date = new Carbon(Carbon::now('Africa/Nairobi')->toDateString());
            $number_of_days = $next_pay_date->diffInDays($topup_date);

            $inv_type = $request->input('inv_type_id');
            $topup->account_id = $request->input('account_no_id');
            $topup->topup_amount = $request->input('topup_amount');
            $topup->topped_at = Carbon::now()->toDateString();
            $interest_rate = 0.2;
            $days = 30;
            $interest = $interest_rate * $topup->topup_amount;
            $top_int = floor(($interest * $number_of_days)/$days);

            if($inv_type == 1){

                $next_pay_amount = $data['investments']->monthly_amount;
                $data['updated_next_pay'] = $next_pay_amount + $top_int;
                echo"<pre>";
                print_r($data['updated_next_pay']);
                exit;
                }
                // echo "<pre>";
                // print_r($data['investments']);
                // exit;

            // $payment->save();
            // DB::beginTransaction();

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