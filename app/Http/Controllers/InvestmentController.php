<?php

namespace App\Http\Controllers;

use App\Exports\InvestmentsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\Investment;
use App\Model\Account;
use App\Model\Payment;
use App\Model\Topup;
use App\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use App\Model\UserDetails;

use Illuminate\Support\Facades\Mail;
use App\Mail\InvestmentApproved;

class InvestmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function export()
    {
        return Excel::download(new InvestmentsExport, 'investments.xlsx');
    }

    public function index()
    {
        if (!auth()->user()->can('investments.view')) {
            abort(401, 'Unauthorized action.');
        }

        $data['investments'] = Investment::getInvestments();
        $data['sum_investments'] = Investment::totalInvestments();
        $data['topups'] = Topup::getTopups();

        $data['investments']->map(function ($item) {

            $name = DB::table('users')
                ->select(
                    DB::raw('users.name AS initiated_by_name')
                )
                ->where('users.id', '=', $item->initiated_by)->get();

            $item->created_by_name = json_encode($name);
            $item->created_by_name = str_replace('[{"initiated_by_name":"', '', $item->created_by_name);
            $item->created_by_name = str_replace('"}]', '', $item->created_by_name);

            $topups  = DB::table('topups')
                ->select(
                    DB::raw('topups.id'),
                    DB::raw('accounts.id'),
                    DB::raw('users.id')
                )
                ->leftJoin('accounts', 'topups.account_id', 'accounts.id')
                ->leftJoin('users', 'accounts.user_id', 'users.id')
                ->where('users.id', '=', $item->user_id)->count();
            $item->number_of_topups = $topups;
            return $item;
        });
        // echo "<pre>";
        // print_r($data['investments']);
        // exit;
        return view('investments.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['accounts'] = Account::getAccounts();
        $data['customers'] = User::getCustomers();
        return view('investments.create')->with($data);
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
            'investment_amount' => ['required'],
            'interest_rate' => ['required']
        ]);

        if ($validator->fails()) {
            DB::rollBack();
            Alert::error('New Investment', 'Oops!!! An error ocurred while adding new investment');
            return back();
        } else {

            try {
                $investment = new Investment();
                $investment->account_no_id = $request->input('account_no_id');
                $investment->investment_amount = $request->input('investment_amount');
                $investment->investment_duration = $request->input('investment_duration');
                $investment->interest_rate = $request->input('interest_rate');
                $generated_transaction_code = strtoupper(str_random(8));
                $investment->trans_id = $generated_transaction_code;

                $maturity_date = Carbon::now('Africa/Nairobi')->addMonths($investment->investment_duration);
                $investment->maturity_date = $maturity_date;

                $investment->payout = ($investment->interest_rate * $investment->investment_amount *  $investment->investment_duration) / 100;
                $investment->total_payout = $investment->investment_amount + $investment->payout;

                $investment->save();
                DB::beginTransaction();


                $just_saved_account_id = $investment->account_no_id;

                $account_due_payments = DB::table('accounts')
                    ->select(
                        DB::raw('accounts.*'),
                        DB::raw('investments.*')
                    )
                    ->leftJoin('investments', 'accounts.id', 'investments.account_no_id')
                    ->where('accounts.id', '=',  $just_saved_account_id)
                    ->first();

                $investments_bal = $account_due_payments->total_investments;
                $investments_bal = $investments_bal +  $investment->investment_amount;

                $interests_bal = $account_due_payments->total_due_interests;
                $interests_bal = $interests_bal + $investment->payout;

                $balanace = $account_due_payments->total_due_payments;
                $balance = $balanace +  $investment->total_payout;

                $data['account_balance_array'] = array(
                    'total_investments' => $investments_bal,
                    'total_due_interests' => $interests_bal,
                    'total_due_payments' => $balance
                );

                $acc_balances = DB::table('accounts')->where('id',  $just_saved_account_id)
                    ->update($data['account_balance_array']);

                DB::commit();
                Alert::success('New Investment', 'Investment added successfully');
                toast('New Investment added successfully', 'success', 'top-right');
                return back();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                return back();
            }
        }
    }

    public function approve(Request $request)
    {
        $inv_id = $request->input('investment_id');
        $trans_id = $request->input('trans_id');

        $status = 1;

         DB::table('investments')->where('investment_id', $inv_id)
            ->update([
                'inv_status_id' => $status
            ]);
        //$approved_inv = Investment::getInvestments()->where('investments.investment_id', '=', $inv_id)->first();

        // echo "<pre>";
        // print_r($approved_inv);
        // exit;

        // $objDemo = new \stdClass();
        // $objDemo->subject = 'Successful Registration';
        // $company = "Inter-Web Global Fortune";
        // $objDemo->company = $company;

        // //1. Send to the user
        // $message = "You have been successfully registered as a client at Inter-Web Global Fortune";
        // $objDemo->email = $user->email;
        // $objDemo->name = $user->name;
        // $objDemo->account_no = $user->account_no;
        // $objDemo->amount = $user->investment_amount;
        // $objDemo->inv_date = $inv_date;
        // $objDemo->duration = $inv_duration;
        // $objDemo->inv_type = $inv_type_name;
        // $objDemo->message = $message;

        // Mail::to($objDemo->email)->send(new InvestmentApproved($objDemo));

        toast('Investment ' . '-' . $trans_id . ' approved successfully', 'success', 'top-right');
        return back();
    }

    // Fetch issue subcategories
    public function getUser()
    {
        $account_no_id = Input::get('account_no_id');
        $user_name = User::where('id', '=', $account_no_id)->get();
        return response()->json($user_name);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function show(Investment $investment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function edit(Investment $investment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Investment $investment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Investment $investment)
    {
        //
    }
}