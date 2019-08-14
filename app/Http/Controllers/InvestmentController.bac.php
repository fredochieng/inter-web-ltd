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
use Session;
use App\Model\UserDetails;

use Illuminate\Support\Facades\Mail;
use App\Mail\InvestmentApproved;
use App\Mail\InvestmentTerminated;

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

        if (isset($_POST['approve_inv'])) {
            $status = 1;

            DB::table('investments')->where('investment_id', $inv_id)
                ->update([
                    'inv_status_id' => $status
                ]);
            $approved_inv = Investment::getInvestments()->where('investment_id', '=', $inv_id)->first();

            $objDemo = new \stdClass();
            $objDemo->subject = 'Investment Approval';
            $company = "Inter-Web Global Fortune";
            $objDemo->company = $company;

            // //1. Send to the user
            $message = "Your investment has been approved successfully";
            $objDemo->email = $approved_inv->email;
            $objDemo->name = $approved_inv->name;
            $objDemo->amount = $approved_inv->initial_inv;
            $objDemo->inv_date = $approved_inv->inv_date;
            $objDemo->duration = $approved_inv->investment_duration;
            $objDemo->inv_type = $approved_inv->inv_type;
            $objDemo->message = $message;

            Mail::to($approved_inv->email)->send(new InvestmentApproved($objDemo));

            toast('Investment ' . '-' . $trans_id . ' approved successfully', 'success', 'top-right');
            return back();
        } elseif (isset($_POST['update_inv'])) {
            $inv_id = $request->input('investment_id');
            $inv_duration =  $request->input('inv_duration');
            $inv_date =  $request->input('inv_date');

            $investment = Investment::getInvestments()->where('investment_id', $inv_id)->first();

            $inv_type = $investment->inv_type_id;
            $account_no_id = $investment->acc_id;
            $user_id = $investment->user_id;


            $investment_amount = $request->input('inv_amount');
            $investment_amount = (array) $investment_amount;
            $investment_amount = str_replace('Kshs', '', $investment_amount);
            $investment_amount = str_replace(',', '', $investment_amount);
            $investment_amount = str_replace('.00', '', $investment_amount);
            $investment_amount = implode('', $investment_amount);

            $inv_comm_per = 0.05;
            $inv_comm = $inv_comm_per * $investment_amount;

            // GET PAYMENT DATES
            if ($inv_type == 1) {
                $inv_duration = (array) $inv_duration;
                $inv_duration = str_replace(' Months', '', $inv_duration);
                $inv_duration = implode('', $inv_duration);

                $investment_date = Carbon::parse($inv_date)->toDateString();
                $last_pay_date = Carbon::parse($inv_date)->addMonths($inv_duration)->format('Y-m-d');

                // GET ALL THE PAYMENT DATES FOR A USER (MONTHLY INVESTMENT TYPE)
                $inv_date = Carbon::parse($inv_date);

                for ($i = 0; $i < $inv_duration; $i++) {
                    $pay_dates[] = $inv_date->addMonth()->format('Y-m-d');
                }
                $pay_dates = json_encode($pay_dates);
            } elseif ($inv_type == 2) {

                $inv_duration = (array) $inv_duration;
                $inv_duration = str_replace(' Months', '', $inv_duration);
                $inv_duration = implode('', $inv_duration);

                $investment_date = Carbon::parse($inv_date)->toDateString();
                $last_pay_date = Carbon::parse($inv_date)->addMonths($inv_duration)->format('Y-m-d');

                // GET ALL THE PAYMENT DATES FOR A USER (MONTHLY INVESTMENT TYPE)
                $inv_date = Carbon::parse($inv_date);

                for ($i = 0; $i < $inv_duration; $i++) {
                    $pay_dates[] = $inv_date->addMonth()->format('Y-m-d');
                }
                $pay_dates = json_encode($pay_dates);
            } elseif ($inv_type == 3) {
                $monthly_inv_duration = $request->input('monthly_inv_duration');
                $monthly_inv_duration = (array) $monthly_inv_duration;
                $monthly_inv_duration = str_replace(' Months', '', $monthly_inv_duration);
                $monthly_inv_duration = implode('', $monthly_inv_duration);

                $compounded_inv_duration = $request->input('compounded_inv_duration');
                $compounded_inv_duration = (array) $compounded_inv_duration;
                $compounded_inv_duration = str_replace(' Months', '', $compounded_inv_duration);
                $compounded_inv_duration = implode('', $compounded_inv_duration);

                $investment_date = Carbon::parse($inv_date)->toDateString();
                $last_pay_date = Carbon::parse($inv_date)->addMonths($compounded_inv_duration)->format('Y-m-d');

                // GET ALL THE PAYMENT DATES FOR A USER (MONTHLY INVESTMENT TYPE)
                $inv_date = Carbon::parse($inv_date);

                for ($i = 0; $i < $monthly_inv_duration; $i++) {
                    $pay_dates[] = $inv_date->addMonth()->format('Y-m-d');
                }
                $pay_dates = json_encode($pay_dates);
            }

            // END PAYMENT DATES

            if ($inv_type == 1) {
                $monthly_pay = 0.2 * $investment_amount;
                $total_pay = $monthly_pay * $inv_duration;

                $accu_interest_array = array();
                for ($i = 0; $i < $inv_duration; $i++) {
                    $monthly_pay = 0.2 * $investment_amount;
                    $accu_interest_array[] = (int) $monthly_pay;
                }

                $save_account_data = DB::table('accounts')->where('id', $account_no_id)->update(['total_due_payments' => $total_pay]);

                $save_user_payment_schedule = DB::table('payment_schedule')->where('account_no_id', $account_no_id)
                    ->update(['tot_payable_amnt' => $total_pay, 'monthly_amount' => $monthly_pay]);

                $save_investment_data = DB::table('investments')->where('investment_id', $inv_id)
                    ->update([
                        'investment_amount' => $investment_amount, 'initial_inv' => $investment_amount,
                        'inv_comm' => $inv_comm, 'inv_date' => $investment_date, 'last_pay_date' => date('Y-m-d', strtotime($last_pay_date))
                    ]);

                $update_payment_dates = DB::table('user_pay_modes')->where('user_id', $user_id)->update(['pay_dates' => $pay_dates]);
            } elseif ($inv_type == 2) {
                $principal = $investment_amount;
                $interestRate = 0.2;
                $term = $inv_duration - 1;

                $accu_interest_array = array();
                for ($i = 0; $i < $term; $i++) {
                    $total = $principal * $interestRate;
                    $principal += $total;
                    $accu_interest_array[] = (int) $total;
                }
                $monthly_payment = json_encode($accu_interest_array);
                $total_comp_int = json_encode(array_sum($accu_interest_array));

                $save_account_data = DB::table('accounts')->where('id', $account_no_id)->update(['total_due_payments' => $total_comp_int]);

                $save_user_payment_schedule = DB::table('payment_schedule')->where('account_no_id', $account_no_id)
                    ->update(['tot_payable_amnt' => $total_comp_int, 'comp_monthly_pay' => $monthly_payment]);

                $save_investment_data = DB::table('investments')->where('investment_id', $inv_id)
                    ->update([
                        'investment_amount' => $investment_amount, 'initial_inv' => $investment_amount,
                        'inv_comm' => $inv_comm, 'inv_date' => $investment_date, 'last_pay_date' => date('Y-m-d', strtotime($last_pay_date))
                    ]);

                $update_payment_dates = DB::table('user_pay_modes')->where('user_id', $user_id)
                    ->update(['pay_dates' => date('Y-m-d', strtotime($last_pay_date))]);
            } elseif ($inv_type == 3) {

                $monthly_inv_amount = $request->input('monthly_inv_amount');
                $monthly_inv_amount = (array) $monthly_inv_amount;
                $monthly_inv_amount = str_replace('Kshs', '', $monthly_inv_amount);
                $monthly_inv_amount = str_replace(',', '', $monthly_inv_amount);
                $monthly_inv_amount = str_replace('.00', '', $monthly_inv_amount);
                $monthly_inv_amount = implode('', $monthly_inv_amount);

                $compounded_inv_amount =  $investment_amount -  $monthly_inv_amount;

                $monthly_inv_duration = $request->input('monthly_inv_duration');
                $monthly_inv_duration = (array) $monthly_inv_duration;
                $monthly_inv_duration = str_replace(' Months', '', $monthly_inv_duration);
                $monthly_inv_duration = implode('', $monthly_inv_duration);

                $compounded_inv_duration = $request->input('compounded_inv_duration');
                $compounded_inv_duration = (array) $compounded_inv_duration;
                $compounded_inv_duration = str_replace(' Months', '', $compounded_inv_duration);
                $compounded_inv_duration = implode('', $compounded_inv_duration);

                // CALCULATE MONTHLY AND TOTAL PAYMENTS FOR MONHTLY INVESTMENT
                $monthly_inv_pay = 0.2 * $monthly_inv_amount;
                $total_monthly_pay = $monthly_inv_pay * $monthly_inv_duration;

                $principal = $compounded_inv_amount;
                $interestRate = 0.2;
                $term = $compounded_inv_duration - 1;

                $accu_interest_array = array();
                for ($i = 0; $i < $term; $i++) {
                    $total = $principal * $interestRate;
                    $principal += $total;
                    $accu_interest_array[] = (int) $total;
                }
                $monthly_payment = json_encode($accu_interest_array);
                $total_comp_int = json_encode(array_sum($accu_interest_array));

                $total_due_pay = $total_comp_int + $total_monthly_pay;

                $save_account_data = DB::table('accounts')->where('id', $account_no_id)->update(['total_due_payments' => $total_due_pay]);

                $save_user_payment_schedule = DB::table('payment_schedule')->where('account_no_id', $account_no_id)
                    ->update([
                        'tot_payable_amnt' => $total_due_pay, 'monthly_amount' => $monthly_inv_pay,
                        'comp_monthly_pay' => $monthly_payment, 'tot_comp_amount' => $total_comp_int
                    ]);

                $save_investment_data = DB::table('investments')->where('investment_id', $inv_id)
                    ->update([
                        'investment_amount' => $investment_amount, 'initial_inv' => $investment_amount,
                        'inv_comm' => $inv_comm,  'monthly_inv' => $monthly_inv_amount, 'compounded_inv' => $compounded_inv_amount,
                        'inv_date' => $investment_date, 'last_pay_date' => date('Y-m-d', strtotime($last_pay_date))
                    ]);

                $update_payment_dates = DB::table('user_pay_modes')->where('user_id', $user_id)
                    ->update(['pay_dates' => $pay_dates]);
            }
            toast('Investment updated successfullty', 'success', 'top-right');
            return back();
        }
    }

    public function terminateInvestment(Request $request)
    {
        $inv_id = $request->input('investment_id');

        $investment = Investment::getInvestments()->where('investment_id', '=', $inv_id)->first();

        $total_investments = $investment->investment_amount;

        $inv_type = $investment->inv_type_id;
        $invest_dur = $investment->investment_duration;
        $account_no_id = $investment->acc_id;
        $user_id = $investment->user_id;
        $topped = $investment->topped_up;
        $terminated = $investment->termination_type;

        $termination_type = $request->input('termination_type');
        $amount_terminated = $request->input('amount_terminated');
        $amount_after_ter = $request->input('amount_after_ter');

        $next_pay_date = new Carbon(Session::get('next_pay_day'));
        $next_pay_date = $next_pay_date->toDateString();
        $ter_date = Carbon::now('Africa/Nairobi')->toDateString();

        $tot_terminations = DB::table('daily_trans_summary')
            ->where('date', '=',  $ter_date)->first();

        $total_terminations = $tot_terminations->tot_terminations;
        $total_terminations = $total_terminations + $amount_terminated;

        $new_tot_terminations = array(

            'tot_terminations' => $total_terminations
        );
        $new_balance = DB::table('daily_trans_summary')->where('date',  $ter_date)
            ->update($new_tot_terminations);


        if ($termination_type == 2) {

            if ($inv_type == 1) {
                $save_account_data = DB::table('accounts')->where('id', $account_no_id)
                    ->update(['total_due_payments' => $total_investments]);

                $save_user_payment_schedule = DB::table('payment_schedule')->where('account_no_id', $account_no_id)
                    ->update([
                        'tot_payable_amnt' => $total_investments, 'termination_pay' => $total_investments,
                        'monthly_amount' => $total_investments, 'updated_next_pay' => $total_investments, 'updated_monthly_pay' => $total_investments, 'updated_monthly_pay_ter' => $total_investments
                    ]);

                $save_investment_data = DB::table('investments')->where('investment_id', $inv_id)
                    ->update([
                        'termination_type' => $termination_type, 'terminated_at' => Carbon::now('Africa/Nairobi')->toDateString(),
                        'initial_inv' => 0, 'investment_amount' => 0
                    ]);

                $save_ter_inv = array(
                    'ter_inv_id' => $inv_id,
                    'user_id' => $user_id,
                    'before_ter' => $total_investments,
                    'amount_ter' => $amount_terminated,
                    'after_ter' => 0,
                    'termination_type' => $termination_type,
                    'ter_date' => $ter_date
                );
                $save_ter = DB::table('terminations')->insertGetId($save_ter_inv);

                $save_ter_pay_info = array(
                    'user_id' => $user_id,
                    'pay_amount' => $total_investments,
                    'pay_date' => $next_pay_date
                );

                $save_ter_pay = DB::table('termination_payments')->insertGetId($save_ter_pay_info);

                toast('Investment terminated successfullty', 'success', 'top-right');
                return back();
            } elseif ($inv_type == 2) {

                $save_account_data = DB::table('accounts')->where('id', $account_no_id)
                    ->update(['total_due_payments' => $total_investments]);

                $save_user_payment_schedule = DB::table('payment_schedule')->where('account_no_id', $account_no_id)
                    ->update([
                        'tot_payable_amnt' => $total_investments, 'termination_pay' => $total_investments,
                    ]);

                $save_investment_data = DB::table('investments')->where('investment_id', $inv_id)
                    ->update([
                        'termination_type' => $termination_type, 'terminated_at' => Carbon::now('Africa/Nairobi')->toDateString(),
                        'initial_inv' => 0, 'investment_amount' => 0
                    ]);

                $save_ter_inv = array(
                    'ter_inv_id' => $inv_id,
                    'user_id' => $user_id,
                    'before_ter' => $total_investments,
                    'amount_ter' => $amount_terminated,
                    'after_ter' => 0,
                    'termination_type' => $termination_type,
                    'ter_date' => $ter_date
                );
                $save_ter = DB::table('terminations')->insertGetId($save_ter_inv);

                $save_ter_pay_info = array(
                    'user_id' => $user_id,
                    'pay_amount' => $total_investments,
                    'pay_date' => $next_pay_date
                );

                $save_ter_pay = DB::table('termination_payments')->insertGetId($save_ter_pay_info);

                toast('Investment terminated successfullty', 'success', 'top-right');
                return back();
            } elseif ($inv_type == 3) {

                $save_account_data = DB::table('accounts')->where('id', $account_no_id)
                    ->update(['total_due_payments' => $total_investments]);

                $save_user_payment_schedule = DB::table('payment_schedule')->where('account_no_id', $account_no_id)
                    ->update([
                        'tot_payable_amnt' => $total_investments, 'termination_pay' => $total_investments, 'tot_comp_amount' => 0,
                        'monthly_amount' => $total_investments, 'updated_next_pay' => $total_investments, 'updated_monthly_pay' => $total_investments, 'updated_monthly_pay_ter' => $total_investments
                    ]);

                $save_investment_data = DB::table('investments')->where('investment_id', $inv_id)
                    ->update([
                        'termination_type' => $termination_type, 'terminated_at' => Carbon::now('Africa/Nairobi')->toDateString(),
                        'initial_inv' => 0, 'investment_amount' => 0
                    ]);

                $save_ter_inv = array(
                    'ter_inv_id' => $inv_id,
                    'user_id' => $user_id,
                    'before_ter' => $total_investments,
                    'amount_ter' => $amount_terminated,
                    'after_ter' => 0,
                    'termination_type' => $termination_type,
                    'ter_date' => $ter_date
                );
                $save_ter = DB::table('terminations')->insertGetId($save_ter_inv);

                $save_ter_pay_info = array(
                    'user_id' => $user_id,
                    'pay_amount' => $total_investments,
                    'pay_date' => $next_pay_date
                );

                $save_ter_pay = DB::table('termination_payments')->insertGetId($save_ter_pay_info);

                toast('Investment terminated successfullty', 'success', 'top-right');
                return back();
            }
        } elseif ($termination_type == 1) {
            $next_pay_date = new Carbon(Session::get('next_pay_day'));
            $next_pay_amount = Session::get('next_amount');

            $next_pay_date = $next_pay_date->toDateString();
            if ($inv_type == 1) {

                $inv_subtype_id = $request->input('inv_subtype');
                $tot_inv_amount = $investment->investment_amount;
                $comp_inv = $investment->compounded_inv;
                $monthly_inv = $investment->monthly_inv;
                $monthly_dur = $investment->monthly_duration;
                $comp_dur = $investment->comp_duration;
                $monthly_amount = $investment->monthly_amount;
                $updated_next_pay = $investment->updated_next_pay;
                $updated_monthly_pay = $investment->updated_monthly_pay;
                $updated_monthly_pay_ter = $investment->updated_monthly_pay_ter;
                $comp_due_pay = $investment->tot_comp_amount;
                $tot_due_pay = $investment->tot_payable_amnt;

                $monthly_pay = 0.2 * $amount_after_ter;
                $total_pay = $monthly_pay * $invest_dur;

                $accu_interest_array = array();
                for ($i = 0; $i < $invest_dur; $i++) {
                    $monthly_pay = 0.2 * $amount_after_ter;
                    $accu_interest_array[] = (int) $monthly_pay;
                }

                $new_due_pay = $total_pay + $comp_due_pay;
                $new_tot_investment = $tot_inv_amount - $amount_terminated;
                $new_monthly_inv = $monthly_inv - $amount_terminated;

                // if ($topped == 0 && $terminated == '') {
                //     $updated_pay = $amount_terminated + $monthly_amount;
                // } elseif ($topped == 1 && $terminated == '') {
                //     $updated_pay = $amount_terminated + $updated_monthly_pay;
                // } elseif ($topped == '' && $terminated != '') {
                //     $updated_pay = $amount_terminated + $monthly_amount;
                // } elseif ($topped == 1 && $terminated != '') {
                //     $updated_pay = $amount_terminated + $updated_monthly_pay_ter;
                // }
                $updated_pay = $amount_terminated + $monthly_pay;
                // echo $updated_pay;
                // exit;


                $save_account_data = DB::table('accounts')->where('id', $account_no_id)->update(['total_due_payments' => $total_pay]);

                $save_user_payment_schedule = DB::table('payment_schedule')->where('account_no_id', $account_no_id)
                    ->update(['tot_payable_amnt' => $total_pay, 'monthly_amount' => $monthly_pay, 'updated_monthly_pay_ter' => $updated_pay]);

                $save_investment_data = DB::table('investments')->where('investment_id', $inv_id)
                    ->update([
                        'termination_type' => $termination_type, 'terminated_at' => Carbon::now('Africa/Nairobi')->toDateString(),
                        'initial_inv' => $amount_after_ter, 'investment_amount' => $amount_after_ter
                    ]);

                $save_ter_inv = array(
                    'ter_inv_id' => $inv_id,
                    'user_id' => $user_id,
                    'before_ter' => $total_investments,
                    'amount_ter' => $amount_terminated,
                    'after_ter' => $amount_after_ter,
                    'termination_type' => $termination_type,
                    'ter_date' => $ter_date
                );
                $save_ter = DB::table('terminations')->insertGetId($save_ter_inv);

                $save_ter_pay_info = array(
                    'user_id' => $user_id,
                    'pay_amount' => $updated_pay,
                    'pay_date' => $next_pay_date
                );

                $save_ter_pay = DB::table('termination_payments')->insertGetId($save_ter_pay_info);

                // Send investment termination email notification to the client
                $terminated_inv = Investment::getInvestments()->where('investment_id', '=', $inv_id)->first();

                $objDemo = new \stdClass();
                $objDemo->subject = 'Investment Terminated';
                $company = "Inter-Web Global Fortune";
                $objDemo->company = $company;

                // //1. Send to the user
                $message = "Your investment has been partially terminated";
                $objDemo->email = $terminated_inv->email;
                $objDemo->name = $terminated_inv->name;
                $objDemo->before_ter = $total_investments;
                $objDemo->amount_ter = $amount_terminated;
                $objDemo->after_ter = $total_investments - $amount_terminated;
                $objDemo->message = $message;

                // echo "<pre>";
                // print_r($objDemo);
                // exit;

                Mail::to($terminated_inv->email)->send(new InvestmentTerminated($objDemo));

                toast('Investment terminated successfullty', 'success', 'top-right');
                return back();
            } elseif ($inv_type == 2) {

                $principal = $amount_after_ter;
                $interestRate = 0.2;
                $term = $invest_dur - 1;

                $accu_interest_array = array();
                for ($i = 0; $i < $term; $i++) {
                    $total = $principal * $interestRate;
                    $principal += $total;
                    $accu_interest_array[] = (int) $total;
                }
                $monthly_payment = json_encode($accu_interest_array);
                $total_comp_int = json_encode(array_sum($accu_interest_array));

                $save_account_data = DB::table('accounts')->where('id', $account_no_id)
                    ->update(['total_due_payments' => $total_comp_int]);

                $save_user_payment_schedule = DB::table('payment_schedule')->where('account_no_id', $account_no_id)
                    ->update([
                        'tot_payable_amnt' => $total_comp_int, 'comp_monthly_pay' => $monthly_payment
                    ]);

                $save_investment_data = DB::table('investments')->where('investment_id', $inv_id)
                    ->update([
                        'termination_type' => $termination_type, 'terminated_at' => Carbon::now('Africa/Nairobi')->toDateString(),
                        'initial_inv' => $amount_after_ter, 'investment_amount' => $amount_after_ter
                    ]);

                $save_ter_inv = array(
                    'ter_inv_id' => $inv_id,
                    'user_id' => $user_id,
                    'before_ter' => $total_investments,
                    'amount_ter' => $amount_terminated,
                    'after_ter' => $amount_after_ter,
                    'termination_type' => $termination_type,
                    'ter_date' => $ter_date
                );
                $save_ter = DB::table('terminations')->insertGetId($save_ter_inv);

                $save_ter_pay_info = array(
                    'user_id' => $user_id,
                    'pay_amount' => $amount_terminated,
                    'pay_date' => $next_pay_date
                );

                $save_ter_pay = DB::table('termination_payments')->insertGetId($save_ter_pay_info);

                // Send investment termination email notification to the client
                $terminated_inv = Investment::getInvestments()->where('investment_id', '=', $inv_id)->first();

                $objDemo = new \stdClass();
                $objDemo->subject = 'Investment Terminated';
                $company = "Inter-Web Global Fortune";
                $objDemo->company = $company;

                // //1. Send to the user
                $message = "Your investment has been partially terminated";
                $objDemo->email = $terminated_inv->email;
                $objDemo->name = $terminated_inv->name;
                $objDemo->before_ter = $total_investments;
                $objDemo->amount_ter = $amount_terminated;
                $objDemo->after_ter = $total_investments - $amount_terminated;
                $objDemo->message = $message;

                // echo "<pre>";
                // print_r($objDemo);
                // exit;

                Mail::to($terminated_inv->email)->send(new InvestmentTerminated($objDemo));

                toast('Investment terminated successfullty', 'success', 'top-right');
                return back();
            } elseif ($inv_type == 3) {

                $inv_subtype_id = $request->input('inv_subtype');
                $tot_inv_amount = $investment->investment_amount;
                $comp_inv = $investment->compounded_inv;
                $monthly_inv = $investment->monthly_inv;
                $monthly_dur = $investment->monthly_duration;
                $comp_dur = $investment->comp_duration;
                $monthly_amount = $investment->monthly_amount;
                $updated_next_pay = $investment->updated_next_pay;
                $updated_monthly_pay = $investment->updated_monthly_pay;
                $updated_monthly_pay_ter = $investment->updated_monthly_pay_ter;
                $comp_due_pay = $investment->tot_comp_amount;
                $tot_due_pay = $investment->tot_payable_amnt;


                if ($inv_subtype_id == 1) {

                    $monthly_pay = 0.2 * $amount_after_ter;
                    $total_pay = $monthly_pay * $monthly_dur;

                    $accu_interest_array = array();
                    for ($i = 0; $i < $monthly_dur; $i++) {
                        $monthly_pay = 0.2 * $amount_after_ter;
                        $accu_interest_array[] = (int) $monthly_pay;
                    }

                    $new_due_pay = $total_pay + $comp_due_pay;
                    $new_tot_investment = $tot_inv_amount - $amount_terminated;
                    $new_monthly_inv = $monthly_inv - $amount_terminated;


                    $updated_pay = $amount_terminated + $monthly_pay;

                    $save_account_data = DB::table('accounts')->where('id', $account_no_id)->update(['total_due_payments' => $new_due_pay]);

                    $save_user_payment_schedule = DB::table('payment_schedule')->where('account_no_id', $account_no_id)
                        ->update([
                            'tot_payable_amnt' => $new_due_pay, 'monthly_amount' => $monthly_pay, 'updated_monthly_pay_ter' => $updated_pay
                        ]);

                    $save_investment_data = DB::table('investments')->where('investment_id', $inv_id)
                        ->update([
                            'termination_type' => $termination_type, 'terminated_at' => Carbon::now('Africa/Nairobi')->toDateString(),
                            'initial_inv' => $new_tot_investment, 'investment_amount' => $new_tot_investment, 'monthly_inv' => $new_monthly_inv
                        ]);

                    $save_ter_inv = array(
                        'ter_inv_id' => $inv_id,
                        'user_id' => $user_id,
                        'before_ter' => $total_investments,
                        'amount_ter' => $amount_terminated,
                        'after_ter' => $new_tot_investment,
                        'termination_type' => $termination_type,
                        'ter_date' => $ter_date
                    );
                    $save_ter = DB::table('terminations')->insertGetId($save_ter_inv);
                    // $next_pay_date = $next_pay_date->toDateString();
                    // $next_pay_date = (array) $next_pay_date;
                    // $updated_pay = (array) $updated_pay;

                    // $output = array_combine($next_pay_date, $updated_pay);
                    // $output = json_encode($output);

                    $save_ter_pay_info = array(
                        'user_id' => $user_id,
                        'pay_amount' => $updated_pay,
                        'pay_date' => $next_pay_date
                    );

                    $save_ter_pay = DB::table('termination_payments')->insertGetId($save_ter_pay_info);
                } elseif ($inv_subtype_id == 2) {
                    $principal = $amount_after_ter;
                    $interestRate = 0.2;
                    $term = $comp_dur - 1;

                    $accu_interest_array = array();
                    for ($i = 0; $i < $term; $i++) {
                        $total = $principal * $interestRate;
                        $principal += $total;
                        $accu_interest_array[] = (int) $total;
                    }
                    $monthly_payment = json_encode($accu_interest_array);
                    $total_comp_int = json_encode(array_sum($accu_interest_array));

                    $tot_monthly_due = $tot_due_pay - $comp_due_pay;
                    $new_due_pay = $total_comp_int + $tot_monthly_due;
                    $new_tot_investment = $tot_inv_amount - $amount_terminated;
                    $new_comp_inv = $comp_inv - $amount_terminated;

                    $updated_pay = $next_pay_amount + $amount_terminated;
                    $save_account_data = DB::table('accounts')->where('id', $account_no_id)
                        ->update(['total_due_payments' => $new_due_pay]);

                    $save_user_payment_schedule = DB::table('payment_schedule')->where('account_no_id', $account_no_id)
                        ->update([
                            'tot_payable_amnt' => $new_due_pay, 'tot_comp_amount' => $total_comp_int, 'comp_monthly_pay' => $monthly_payment,
                            'updated_monthly_pay_ter' => $updated_pay
                        ]);

                    $save_investment_data = DB::table('investments')->where('investment_id', $inv_id)
                        ->update([
                            'termination_type' => $termination_type, 'terminated_at' => Carbon::now('Africa/Nairobi')->toDateString(),
                            'initial_inv' => $new_tot_investment, 'investment_amount' => $new_tot_investment, 'compounded_inv' => $new_comp_inv
                        ]);

                    // STORE THE INVESTMENT TERMINATION DETAILS

                    $save_ter_inv = array(
                        'ter_inv_id' => $inv_id,
                        'user_id' => $user_id,
                        'before_ter' => $total_investments,
                        'amount_ter' => $amount_terminated,
                        'after_ter' => $new_tot_investment,
                        'termination_type' => $termination_type,
                        'ter_date' => $ter_date
                    );

                    $save_ter = DB::table('terminations')->insertGetId($save_ter_inv);

                    $save_ter_pay_info = array(
                        'user_id' => $user_id,
                        'pay_amount' => $updated_pay,
                        'pay_date' => $next_pay_date
                    );

                    $save_ter_pay = DB::table('termination_payments')->insertGetId($save_ter_pay_info);
                }

                // Send investment termination email notification to the client
                $terminated_inv = Investment::getInvestments()->where('investment_id', '=', $inv_id)->first();

                $objDemo = new \stdClass();
                $objDemo->subject = 'Investment Terminated';
                $company = "Inter-Web Global Fortune";
                $objDemo->company = $company;

                // //1. Send to the user
                $message = "Your investment has been partially terminated";
                $objDemo->email = $terminated_inv->email;
                $objDemo->name = $terminated_inv->name;
                $objDemo->before_ter = $total_investments;
                $objDemo->amount_ter = $amount_terminated;
                $objDemo->after_ter = $total_investments - $amount_terminated;
                $objDemo->message = $message;

                // echo "<pre>";
                // print_r($objDemo);
                // exit;

                Mail::to($terminated_inv->email)->send(new InvestmentTerminated($objDemo));

                toast('Investment terminated successfullty', 'success', 'top-right');
                return back();
            }
        }
    }
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
    { }

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