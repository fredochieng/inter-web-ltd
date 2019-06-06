<?php

namespace App\Http\Controllers;

use App\Model\Account;
use App\Model\Investment;
use App\Model\Report;
use Illuminate\Http\Request;
use DB;
use SebastianBergmann\CodeCoverage\Report\Xml\Report as SebastianBergmannReport;

class AccountController extends Controller
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

    public function get_accounts(Request $request)
    {
        $search_term = $request->input('q');
        $search_term = '%' . $search_term . '%';

        $data = DB::table('accounts')
            ->select(
                DB::raw('account_no as text'),
                DB::raw('accounts.id as id'),
                DB::raw('users.name as user_name'),
                DB::raw('users_details.telephone as user_telephone')
                // DB::raw('sum(investment_amount) as user_sum,
                //         sum(payout) as user_payout,
                //         sum(total_payout) as user_total_payout, account_no_id ')
            )
            ->join('users', 'accounts.user_id', '=', 'users.id')
            ->join('users_details', 'users.id', '=', 'users_details.user_id')
            // ->Join('investments', 'accounts.id', '=', 'investments.account_no_id')
            ->where('account_no', 'like', $search_term)
            // ->groupBy('investments.account_no_id')
            ->get();

        $user_investments = Report::customerReport();

        echo json_encode($data);
        exit;
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
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        //
    }
}
