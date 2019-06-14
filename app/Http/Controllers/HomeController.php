<?php

namespace App\Http\Controllers;
use App\Model\Investment;
use App\Model\Payment;
use App\Model\Account;
use App\Model\InvestmentType;
use App\User;
use DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['sum_investments'] = Investment::totalInvestments();
        $data['sum_tot_payable'] = Investment::sumTotalPayable();
        $data['sum_tot_payments'] = Payment::sumTotalPayments();
        $data['sum_tot_due_payments'] = Account::sumTotalDuePayments();
        $data['total_customers'] = User::getTotalCustomers();

        $data['sum_investments1'] = DB::table('investments')->sum('investment_amount');
        $data['sum_tot_due_payments1'] = DB::table('accounts')->sum('total_due_payments');
        $data['sum_tot_payments1'] = DB::table('payments')->sum('payment_amount');
        $data['sum_tot_topups1'] = DB::table('topups')->sum('topup_amount');
        return view('home')->with($data);
    }
}