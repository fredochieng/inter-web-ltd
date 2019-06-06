<?php

namespace App\Http\Controllers;
use App\Model\Investment;
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
        $data['sum_payout'] = Investment::sumPayout();
        $data['sum_total_payout'] = Investment::totalPayout();
        $data['total_payments'] = Investment::totalPayments();
        $data['total_customers'] = User::getTotalCustomers();
        return view('home')->with($data);
    }
}
