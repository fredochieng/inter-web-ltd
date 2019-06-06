<?php

namespace App\Http\Controllers;

use App\Model\Report;
use Illuminate\Http\Request;
use DB;
use App\User;
use App\Model\Investment;
use Illuminate\Validation\Rules\Exists;
use Carbon\Carbon;

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
        $data['records'] ->map(function ($item){
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
