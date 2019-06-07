<?php

namespace App\Http\Controllers;
use App\Model\Investment;
use App\Model\Payment;
use App\Model\Account;
use App\User;
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
        return view('home')->with($data);
    }
}
