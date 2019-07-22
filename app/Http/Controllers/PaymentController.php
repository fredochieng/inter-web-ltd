<?php

namespace App\Http\Controllers;

use App\Model\Payment;
use App\Model\PaymentMethod;
use App\Model\UserDetails;
use App\Model\Report;
use App\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Model\Account;
use App\DataTable\DatatablePaginator;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('payments.manage')) {
            abort(401, 'Unauthorized action.');
        }
        $data['inactive_clients'] = User::getClients();
        $data['payments'] = Payment::getPayments();

        $data['payments']->map(function ($item) {

            $name = DB::table('users')
                ->select(
                    DB::raw('users.name AS served_by_name')
                )
                ->where('users.id', '=', $item->served_by)->get();

            $item->served_by_name = json_encode($name);
            $item->served_by_name = str_replace('[{"served_by_name":"', '', $item->served_by_name);
            $item->served_by_name = str_replace('"}]', '', $item->served_by_name);
            return $item;
        });

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
            // "account_id" => ['required'],
            // 'payment_amount' => ['required']
        ]);

        if ($validator->fails()) {
            DB::rollBack();
            Alert::error('New Payment', 'Oops!!! An error ocurred while adding new payment');
            return back();
        } else {

            try {

                $payment = new Payment();
                $payment->account_no_id = $request->input('account_id');
                $payment_amount = $request->input('monthly_pay');
                $payment->payment_amount = str_replace('Kshs', '', $payment_amount);
                $payment->payment_amount = str_replace(',', '', $payment->payment_amount);
                $payment->payment_amount = str_replace('.00', '', $payment->payment_amount);
                $payment->user_pay_date = $request->input('user_date');
                $payment_mode_info_id = $request->input('select_pay_mode');
                $generated_transaction_code = strtoupper(str_random(8));
                $payment->trans_id = $generated_transaction_code;
                $conf_code = $request->input('conf_code');
                $comments = $request->input('comments');
                $inv_type = $request->input('inv_type');

                $payment = new Payment();
                $account_no_id = $request->input('account_id');
                $payment_amount = $request->input('monthly_pay');
                $payment_amount = str_replace('Kshs', '', $payment_amount);
                $payment_amount = str_replace(',', '', $payment_amount);
                $payment_amount = str_replace('.00', '', $payment_amount);

                $comp_payment_amount = $request->input('comp_pay_amount');
                $comp_payment_amount = str_replace('Kshs', '',  $comp_payment_amount);
                $comp_payment_amount = str_replace(',', '',  $comp_payment_amount);
                $comp_payment_amount = str_replace('.00', '',  $comp_payment_amount);

                // COMP AMOUNT FOR MONTHLY AND COMP
                $tot_comp_amount = $request->input('tot_comp_amount');
                $tot_comp_amount = str_replace('Kshs', '',  $tot_comp_amount);
                $tot_comp_amount = str_replace(',', '',  $tot_comp_amount);
                $tot_comp_amount = str_replace('.00', '',  $tot_comp_amount);

                $user_id = $request->input('user_id');

                // FETCH CLIENTS DETAILS AND INVESTMENTS
                $referees = DB::table('users')
                    ->select(
                        DB::raw('users.*'),
                        DB::raw('users.id as referee_id'),
                        DB::raw('users_details.*'),
                        DB::raw('accounts.*'),
                        DB::raw('accounts.id AS accnt_id'),
                        DB::raw('investments.*'),
                        DB::raw('investments.account_no_id as inv_acc_id'),
                        DB::raw('investments.created_at as inv_created_at'),
                        DB::raw('investments.updated_at as inv_updated_at'),
                        DB::raw('user_pay_modes.*'),
                        DB::raw('inv_types.*'),
                        DB::raw('payment_schedule.*'),
                        DB::raw('payment_schedule.monthly_amount'),
                        DB::raw('payments.*'),
                        DB::raw('payment_methods.*'),
                        DB::raw('banks.*')
                    )
                    ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
                    ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
                    ->leftJoin('investments', 'accounts.id', '=', 'investments.account_no_id')
                    ->leftJoin('inv_types', 'investments.inv_type_id', '=', 'inv_types.inv_id')
                    ->leftJoin('user_pay_modes', 'users.id', '=', 'user_pay_modes.user_id')
                    ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
                    ->leftJoin('payments', 'accounts.id', '=', 'payments.account_no_id')
                    ->leftJoin('payment_methods', 'user_pay_modes.pay_mode_id', '=', 'payment_methods.method_id')
                    ->leftJoin('banks', 'user_pay_modes.pay_bank_id', '=', 'banks.bank_id')
                    ->where('users.refered_by', '=', $user_id)
                    ->where('investments.inv_status_id', '=', 1)
                    ->where('tot_inv_comm', '>', 0)
                    ->get();

                $referees->map(function ($item) {
                    $item->tot_inv_comms = $item->tot_inv_comm;
                    $item->inv_comms = $item->inv_comm;
                    $item->updated_tot_inv_comm =  $item->tot_inv_comms -  $item->inv_comms;
                    return $item;
                });

                $investment_ids = array();

                foreach ($referees as $key => $invs) {

                    $investment_ids[] = $invs->investment_id;
                }

                $referees_invs = DB::table('investments')
                    ->select(
                        DB::raw('investments.*')
                    )
                    ->whereIn('investment_id', $investment_ids)
                    ->get();

                $referees_invs->map(function ($item) {
                    $item->tot_inv_comms = $item->tot_inv_comm;
                    $item->inv_comms = $item->inv_comm;
                    $item->updated_tot_inv_comm =  $item->tot_inv_comms -  $item->inv_comms;
                    return $item;
                });

                foreach ($referees_invs as $key => $value) {

                    $save = DB::table('investments')->upsert(
                        [
                            'investment_id' => $value->investment_id,
                            'inv_status_id' => $value->inv_status_id, 'inv_date' => $value->inv_date,
                            'trans_id' => $value->trans_id, 'account_no_id' => $value->account_no_id,
                            'termination_type' => $value->termination_type, 'terminated_at' => $value->terminated_at,
                            'initial_inv' => $value->initial_inv, 'inv_comm' => $value->inv_comm,
                            'tot_inv_comm' => $value->updated_tot_inv_comm, 'investment_amount' => $value->investment_amount,
                            'investment_duration' => $value->investment_duration, 'inv_type_id' => $value->inv_type_id,
                            'inv_mode_id' => $value->inv_mode_id, 'monthly_inv' => $value->monthly_inv,

                            'compounded_inv' => $value->compounded_inv, 'monthly_duration' => $value->monthly_duration,
                            'comp_duration' => $value->comp_duration, 'mpesa_trans_code' => $value->mpesa_trans_code,
                            'inv_bank_id' => $value->inv_bank_id, 'bank_trans_code' => $value->bank_trans_code,
                            'inv_bank_cheq_id' => $value->inv_bank_cheq_id, 'cheque_no' => $value->cheque_no,
                            'last_pay_date' => $value->last_pay_date, 'initiated_by' => $value->initiated_by,
                            'created_at' => $value->created_at
                        ],
                        ['investment_id'],
                        ['tot_inv_comm', 'updated_at']
                    );
                }

                $referee_topups = DB::table('topups')
                    ->select(
                        DB::raw('topups.*'),
                        DB::raw('topups.created_at AS topped_date'),
                        DB::raw('accounts.id'),
                        DB::raw('users.id')
                    )
                    ->leftJoin('accounts', 'topups.account_id', '=', 'accounts.id')
                    ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                    ->where('users.refered_by', '=', $user_id)
                    ->orderBy('topups.topup_id', 'desc')
                    ->where('tot_topup_comm', '>', 0)
                    ->get();

                $topups_ids = array();

                foreach ($referee_topups as $key => $topups) {

                    $topups_ids[] = $topups->topup_id;
                }

                $referees_topups = DB::table('topups')
                    ->select(
                        DB::raw('topups.*')
                    )
                    ->whereIn('topup_id', $topups_ids)
                    ->get();

                $referees_topups->map(function ($item) {
                    $item->tot_topup_comms = $item->tot_topup_comm;
                    $item->topup_comms = $item->topup_comm;
                    $item->updated_tot_topup_comm =  $item->tot_topup_comms -  $item->topup_comm;
                    return $item;
                });

                foreach ($referees_topups as $key => $value) {

                    $save = DB::table('topups')->upsert(
                        [
                            'topup_id' => $value->topup_id, 'created_at' => $value->created_at,
                            'account_id' => $value->account_id, 'topup_amount' => $value->topup_amount,
                            'topup_comm' => $value->topup_comm, 'tot_topup_comm' => $value->updated_tot_topup_comm,
                            'inv_mode_id' => $value->inv_mode_id, 'mpesa_trans_code' => $value->mpesa_trans_code,
                            'inv_bank_id' => $value->inv_bank_id, 'bank_trans_code' => $value->bank_trans_code,
                            'inv_bank_cheq_id' => $value->inv_bank_cheq_id, 'cheque_no' => $value->cheque_no,
                            'topped_at' => $value->topped_at, 'served_by' => $value->served_by
                        ],
                        ['topup_id'],
                        ['tot_topup_comm', 'updated_at']
                    );
                }

                if (empty($tot_comp_amount)) {
                    $tot_comp_amount = 0;
                }

                $auth_user = Auth::user()->id;
                $user_pay_date = $request->input('user_date');
                $generated_transaction_code = strtoupper(str_random(8));
                $trans_id = $generated_transaction_code;
                $inv_type = $request->input('inv_type');

                // GET PAYMENT AMOUNTS FOR MONTHLY + COMPOUNDED

                if ($inv_type == 1) {
                    $payment->account_no_id = $account_no_id;
                    $payment->payment_amount = $payment_amount;
                    $payment->trans_id = $trans_id;
                    $payment->user_pay_date = $user_pay_date;
                    $payment->payment_mode_info_id = $payment_mode_info_id;
                    $payment->comments = $comments;
                    $payment->conf_code = $conf_code;
                    $payment->served_by = $auth_user;

                    $payment->save();
                } elseif ($inv_type == 2) {

                    $payment->account_no_id = $account_no_id;
                    $payment->payment_amount = $comp_payment_amount;
                    $payment->trans_id = $trans_id;
                    $payment->user_pay_date = $user_pay_date;
                    $payment->payment_mode_info_id = $payment_mode_info_id;
                    $payment->comments = $comments;
                    $payment->conf_code = $conf_code;
                    $payment->served_by = $auth_user;

                    $payment->save();
                } elseif ($inv_type == 3) {
                    $payment->account_no_id = $account_no_id;
                    $payment->payment_amount = $payment_amount;
                    $payment->comp_amount_paid = $tot_comp_amount;
                    $payment->trans_id = $trans_id;
                    $payment->user_pay_date = $user_pay_date;
                    $payment->payment_mode_info_id = $payment_mode_info_id;
                    $payment->comments = $comments;
                    $payment->conf_code = $conf_code;
                    $payment->served_by = $auth_user;

                    $payment->total_paid = $payment->payment_amount + $payment->comp_amount_paid;

                    $payment->save();
                }

                $client_tot_payable = DB::table('accounts')
                    ->select(
                        DB::raw('accounts.*'),
                        DB::raw('users.*')
                    )
                    ->leftJoin('users', 'accounts.user_id', 'users.id')
                    ->where('accounts.id', '=', $payment->account_no_id)
                    ->first();

                $client_tot_payable = $client_tot_payable->total_due_payments;
                $client_due_payaments = $client_tot_payable -  $payment->payment_amount;

                if ($inv_type == 3) {
                    $client_due_payaments = $client_tot_payable - $payment->total_paid;
                } elseif ($inv_type == 2) {
                    if ($client_tot_payable < $comp_payment_amount) {

                        $client_due_payaments = $client_tot_payable;
                    } else {

                        $client_due_payaments = $client_tot_payable - $comp_payment_amount;
                    }
                }

                $pay_times = $request->input('pay_times');
                $new_pay_times = $pay_times + 1;

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

                $data['account_balance_array'] = array(

                    'total_due_payments' => $client_due_payaments
                );

                $acc_balances = DB::table('accounts')->where('id',  $just_saved_account_id)
                    ->update($data['account_balance_array']);

                if ($inv_type == 3) {

                    if ($client_tot_payable == $payment->total_paid) {

                        $save_user_payment_schedule = DB::table('payment_schedule')->where('account_no_id', $just_saved_account_id)
                            ->update([
                                'tot_payable_amnt' => 0, 'monthly_amount' => 0, 'updated_next_pay' => 0, 'updated_monthly_pay' => 0
                            ]);

                        $save_investment_data = DB::table('investments')->where('account_no_id', $just_saved_account_id);
                    } else {
                        DB::table('payment_schedule')->where('account_no_id', $just_saved_account_id)->update(
                            [
                                'payment_times' => $new_pay_times
                            ]
                        );
                    }
                } else {

                    DB::table('payment_schedule')->where('account_no_id', $just_saved_account_id)->update(
                        [
                            'payment_times' => $new_pay_times
                        ]
                    );
                }

                DB::commit();

                toast('New payment added successfully', 'success', 'top-right');
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

    public function getNextPayDate($id)
    {
        // GET CLIENT PAYMENT DATES FOR CLIENT (DATE THE CLIENT WAS PAID)


        $user_pay_dates = DB::table('payments')
            ->select(
                DB::raw('payments.*'),
                DB::raw('accounts.id as acc_id'),
                DB::raw('users.id as user_idd')
            )

            ->leftJoin('accounts', 'payments.account_no_id', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->where('users.id', '=', $id)
            ->get();


        // FETCHES ALL THE DATES THE CLIENT WAS PAID
        $user_pay_dates = json_decode(json_encode($user_pay_dates), true);
        $user_pay_dates = array_column($user_pay_dates, 'user_pay_date');


        $pay_dates = DB::table('user_pay_modes')
            ->select(
                DB::raw('user_pay_modes.pay_dates')
            )
            ->where('user_pay_modes.user_id', '=', $id)
            ->first();

        $pay_dates = explode(';',  $pay_dates->pay_dates);
        $pay_dates = array_filter(array_map('trim', $pay_dates));
        $pay_dates = str_replace('["', '', $pay_dates);
        $pay_dates = str_replace('"]', '', $pay_dates);
        $pay_dates = str_replace('","', ',', $pay_dates);

        foreach ($pay_dates as $key => $value) {
            $pay_dates = ($value);
        }

        $pay_dates = explode(',', $pay_dates);

        $data['next_pay_date'] = min(array_diff($pay_dates, $user_pay_dates));
        // GET THE NEXT PAYMENT DATE FOR THE CLIENT
        // CHECK IF THE PAYMENT DATE EXISTS, IF YES SKIP AND PICK THE LEAST DATE
        return  $data['next_pay_date'];
    }

    public function getNextPaymentCompound($id)
    {
        // GET CLIENT PAYMENTS COMPOUNDED

        $client_payments_comp = DB::table('payments')
            ->select(
                DB::raw('payments.*'),
                DB::raw('payments.created_at AS payment_date'),
                DB::raw('payment_schedule.*'),
                DB::raw('accounts.*'),
                DB::raw('users.*')
            )
            ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->where('users.id', '=', $id)
            ->get();

        // FETCHES ALL THE AMOUNTS THE CLIENT WAS PAID
        $client_payments_comp = json_decode(json_encode($client_payments_comp), true);
        $client_payments_comp = array_column($client_payments_comp, 'payment_amount');


        // GET CLIENT MONTHLY PAYMENTS FOR COMPOUNDED
        $client_monthly_com = DB::table('payment_schedule')
            ->select(
                DB::raw('payment_schedule.*'),
                DB::raw('accounts.id as acc_id'),
                DB::raw('users.id as user_idd')
            )

            ->leftJoin('accounts', 'payment_schedule.account_no_id', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->where('users.id', '=', $id)
            ->get();

        $client_monthly_com = json_decode(json_encode($client_monthly_com), true);
        $client_monthly_com = array_column($client_monthly_com, 'comp_monthly_pay');

        $client_monthly_com = str_replace('[', '', $client_monthly_com);
        $client_monthly_com = str_replace(']', '', $client_monthly_com);

        foreach ($client_monthly_com as $key => $value) {
            $client_monthly_com = ($value);
        }

        $client_monthly_com = explode(',', $client_monthly_com);

        // GET CLIENT NEXT PAYMENT FOR COMPOUND
        $data['next_pay_amount'] = min(array_diff($client_monthly_com, $client_payments_comp));

        return $data['next_pay_amount'];
    }
    public function getNextPayComp($id)
    {
        // GET CLIENT PAYMENTS COMPOUNDED

        $next_pay_comp = DB::table('payments')
            ->select(
                DB::raw('payments.*'),
                DB::raw('payments.created_at AS payment_date'),
                DB::raw('payment_schedule.*'),
                DB::raw('accounts.*'),
                DB::raw('users.*')
            )
            ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->where('users.id', '=', $id)
            ->get();

        // FETCHES ALL THE AMOUNTS THE CLIENT WAS PAID
        $next_pay_comp = json_decode(json_encode($next_pay_comp), true);
        $next_pay_comp = array_column($next_pay_comp, 'comp_monthly_amount');

        // GET CLIENT COMP PAYMENTS FOR COMPOUNDED + MONTHLY
        $client_comp_payments = DB::table('payment_schedule')
            ->select(
                DB::raw('payment_schedule.*'),
                DB::raw('accounts.id as acc_id'),
                DB::raw('users.id as user_idd')
            )

            ->leftJoin('accounts', 'payment_schedule.account_no_id', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->where('users.id', '=', $id)
            ->get();

        $client_comp_payments = json_decode(json_encode($client_comp_payments), true);
        $client_comp_payments = array_column($client_comp_payments, 'comp_monthly_pay');

        $client_comp_payments = str_replace('[', '', $client_comp_payments);
        $client_comp_payments = str_replace(']', '', $client_comp_payments);

        foreach ($client_comp_payments as $key => $value) {
            $client_comp_payments = ($value);
        }

        $client_comp_payments = explode(',', $client_comp_payments);

        // GET CLIENT NEXT PAYMENT FOR COMPOUND
        $data['next_pay_comp_amount'] = min(array_diff($client_comp_payments, $next_pay_comp));

        return $data['next_pay_comp_amount'];
    }

    public function SearchClient(Request $request)
    {

        $per_page = $request->input('length', 10);
        $start = $request->input('start', 1);
        $page = (int) ($start / $per_page);
        $query = User::query();
        $query->join('users_details AS UD', 'UD.user_id', 'users.id');
        $query->join('accounts AS AC', 'AC.user_id', 'users.id');
        $query->join('user_pay_modes', 'user_pay_modes.user_id', 'users.id');
        $query->join('payment_methods', 'payment_methods.method_id', 'user_pay_modes.pay_mode_id');
        $query->join('banks', 'banks.bank_id', 'user_pay_modes.pay_bank_id');
        $query->join('payment_schedule AS MIT', 'MIT.account_no_id', 'AC.id');

        $search_by = $request->input('search_by', "");
        $search = $request->input('search', "");


        if (!empty($search)) {
            if ($search_by == 'id_no') {
                $search_params = explode(" ", $search);
                foreach ($search_params as $search_param) {
                    $query->where('id_no', 'LIKE', '%' . $search_param . '%');
                }
            } elseif ($search_by == 'account_no') {
                $query->where('account_no', 'LIKE', '%' . $search);
            } elseif ($search_by == 'telephone') {
                $query->where('telephone', 'LIKE', '%' . $search);
            }
        }

        $clients = $query->paginate($per_page, ['name', 'UD.user_id', 'id_no', 'telephone', 'account_no', 'account_no_id', 'inv_type', 'pay_dates', 'payment_times', 'pay_mode_id', 'method_name', 'pay_mpesa_no', 'bank_name', 'pay_bank_acc', 'tot_comp_amount', 'comp_monthly_pay', 'tot_payable_amnt', 'monthly_amount', 'total_due_payments'], 'page', $page);

        foreach ($clients as $key => $value) {
            $value->user_date = $this->getNextPayDate($value->user_id);
        }

        foreach ($clients as $key => $value) {
            $value->next_pay_comp = $this->getNextPaymentCompound($value->user_id);
        }

        foreach ($clients as $key => $value) {
            $value->next_pay_monthly_comp = $this->getNextPayComp($value->user_id);
        }

        return response()->json(new DatatablePaginator($clients, $request->input('draw')));
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