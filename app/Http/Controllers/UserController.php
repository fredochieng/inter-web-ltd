<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
// use Maatwebsite\Excel\Concerns\Exportable;

use Illuminate\Http\Request;
use DB;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use App\User;
use App\Model\Report;
use App\Model\UserDetails;
use App\Model\Account;
use App\Model\Investment;
use App\Model\InvestmentType;
use Faker\Provider\el_GR\Payment;
use App\Model\PaymentMethod;
use App\Model\Bank;
use App\Model\GenerateAccountNumber;
use App\Model\InvestmentMode;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Model\PaymentSchedule;
use App\Model\UserPayMode;

class UserController extends Controller
{
    // use Exportable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function index(Request $request)
    {
        // $data['clients'] = User::getClients();
        // $data['payment_mode'] = PaymentMethod::getPaymentMethods();
        // $data['banks'] = Bank::getBanks();
        // $data['payment_modes'] = DB::table('payment_methods')->pluck('method_name', 'method_id')->all();

        // SEARCH CLIENTS
        $data['clients'] = array();
        $found_data = 'yes';


        if (isset($_GET['find_client'])) {

            $validate_array = array('find_client_by' => 'required',    'find_value' => 'required',);
            $find_by = $request->get('find_client_by');
            $find_by_value = $request->get('find_value');


            if ($find_by == 'id_no') {
                $validate_array['find_value'] = 'required|integer';
            }

            if ($find_by == 'name' || $find_by == 'account_no' || $find_by == 'phone_no') {
                $validate_array['find_value'] = 'required';
            }

            $this->validate($request, $validate_array);

            // QUERIED CLIENT(S) FROM USER MODEL
            $data = User::find_clients($request);


            if (count($data) == 0) {
                $find_by_value = $request->get('find_value');
                // Alert::error('Search Client', 'Oops!!! No client found matching your entry');
                toast('No client found matching your entry', 'warning', 'top-right');
                return redirect("customers?find_client_by={$find_by}&find_value={$find_by_value}");
            } else {
                $data['clients'] = $data;
                if (count($data['clients']) == 1) {
                    //   Alert::success('Search Client', 'Client found matching your entry');
                    toast('Client found matching your entry', 'success', 'top-right');
                    return redirect('client/' . $data['clients'][0]->id . '/edit');
                }
            }
        }

        //   $data['client_details'] = User::get_telephone();
        return view('users.index')->with($data);
        // return Excel::download(new UsersExport, 'users.xlsx');
        // return (new UsersExport)->download('invoices.xlsx');

        // return (new UsersExport)->download('clients.csv', \Maatwebsite\Excel\Excel::CSV, [
        //     'Content-Type' => 'text/csv',
        // ]);
    }

    public function get_numbers(Request $request)
    {
        $search_term = $request->input('q');
        $search_term = '%' . $search_term . '%';

        $data = DB::table('users_details')
            ->select(
                DB::raw('telephone as text'),
                DB::raw('users_details.user_id as id'),
                DB::raw('users.name as referer_name')
            )
            ->join('users', 'users_details.user_id', '=', 'users.id')
            ->where('telephone', 'like', $search_term)
            ->get();

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
        $data['generated_account'] = GenerateAccountNumber::generateAccountNumber();
        $data['accounts'] = Account::getAccounts();
        $data['inv_modes'] = InvestmentMode::getInvModes();
        $data['inv_types'] = InvestmentType::getInvTypes();
        $data['payment_mode'] = PaymentMethod::getPaymentMethods();
        $data['banks'] = Bank::getBanks();
        return view('users.create')->with($data);
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
            // // "name" => 'required|string|min:5|max:50',
            //  'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // // 'telephone' => ['required', 'string', 'max:10', 'unique:users'],
            //  'id_number' => ['required', 'string', 'max:8', 'unique:users'],
            // // 'dob' => ['required'],
            // // 'account_no' => ['required', 'string', 'unique:users'],
            // // 'kin_telephone' => ['required', 'number', 'max:10'],
        ]);
        $id_no_validator = Validator::make($request->all(), [
            'id_no' => ['required', 'string', 'max:8', 'unique:users_details']
        ]);

        $email_validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
        ]);

        $phone_validator = Validator::make($request->all(), [
            'telephone' => ['required', 'string', 'max:10', 'unique:users_details']
        ]);

        // $mpesa_code_validator = Validator::make($request->all(), [
        //     'mpesa_trans_code' => ['string', 'max:20', 'unique:investments']
        // ]);

        if ($validator->fails()) {
            DB::rollBack();
            Alert::error('New Investor', 'Oops!!! An error ocurred while adding new customer');
            return back();
        } elseif ($id_no_validator->fails()) {
            DB::rollBack();
            Alert::error('New Investor', 'Oops!!! ID Number is already registered');
            return back();
        } elseif ($email_validator->fails()) {
            DB::rollBack();
            Alert::error('New Investor', 'Oops!!! Email address is already registered');
            return back();
        } elseif ($phone_validator->fails()) {
            DB::rollBack();
            Alert::error('New Investor', 'Oops!!! Phone number is already registered');
            return back();
        }
        // elseif ($mpesa_code_validator->fails()) {
        //     DB::rollBack();
        //     Alert::error('New Investor', 'Oops!!! MPESA transaction code is incorrect');
        //     // alert()->info('Info', 'Alert')->showConfirmButton('Button Text','#3085d6');
        //     return back();
        // }
        else {

            //CREATE NEW USER AND SAVE IN users TABLE
            $user = new User();
            $user->name = strtoupper($request->input('name'));
            $user->email = $request->input('email');
            $user->refered_by = $request->input('referer_id');
            $password = "12345678";
            $user->password = Hash::make($password);

            $user->save();
            DB::beginTransaction();

            // GET USER DETAILS DATA

            $saved_user_id = $user->id;

            $user->telephone = $request->input('telephone');
            $user->id_no = $request->input('id_no');
            $user->dob = $request->input('dob');
            $user->account_no = $request->input('account_no');
            $user->home_address = strtoupper($request->input('home_address'));
            $user->home_town = strtoupper($request->input('home_town'));
            $user->kin_name = strtoupper($request->input('kin_name'));
            $user->kin_telephone = $request->input('kin_telephone');

            //GET PAYMENT MODE INFO FOR USER

            $user->pay_mode_id = $request->input('pay_mode_id');
            $user->pay_mpesa_no = $request->input('pay_mpesa_no');
            $user->pay_bank_id = $request->input('pay_bank_id');
            $user->pay_bank_acc = $request->input('pay_bank_acc');

            // GET INVESTMENT DATA FOR USER
            $generated_transaction_code = strtoupper(str_random(8));
            $user->trans_id = $generated_transaction_code;
            $user->inv_date = $request->input('inv_date');
            $user->investment_duration = $request->input('inv_duration');
            $last_pay_date = Carbon::parse($user->inv_date)->addMonths($user->investment_duration);
            $user->account_no_id = $request->input('account_no_id');
            $user->investment_amount = $request->input('inv_amount');
            $user->inv_mode_id = $request->input('inv_mode_id');
            $user->inv_type_id = $request->input('inv_type_id');
            $user->mpesa_trans_code = $request->input('mpesa_trans_code');
            $user->inv_bank_id = $request->input('inv_bank_id');
            $user->bank_trans_code = $request->input('bank_trans_code');
            $user->inv_bank_cheq_id = $request->input('inv_bank_cheq_id');
            $user->cheque_no = $request->input('cheque_no');

            // SAVE USER DETAILS DATA
            $auth_user = Auth::user()->id;
            $users_details_data = array(
                'user_id' => $saved_user_id,
                'telephone' =>  $user->telephone,
                'id_no' =>  $user->id_no,
                'dob' => $user->dob,
                'home_address' =>  strtoupper($user->home_address),
                'home_town' => strtoupper($user->home_town),
                'kin_name' =>  strtoupper($user->kin_name),
                'kin_telephone' => $user->kin_telephone,
                'created_by' => $auth_user

            );
            $save_user_details_data = DB::table('users_details')->insertGetId($users_details_data);

            if ($user->inv_type_id == 1) {

                $inv_duration =  $request->input('inv_duration');
                // CALCULATE MONTHLY AND TOTAL PAYMENTS FOR MONHTLY INVESTMENT TYPE
                $inv_amount =  $request->input('inv_amount');
                $monthly_pay = 0.2 * $inv_amount;
                $total_pay = $monthly_pay * $inv_duration;


                // SAVE USER UNIQUE ACCOUNT (GENERATED ACCOUNT DATA)
                $users_accounts_data = array(
                    'user_id' => $saved_user_id,
                    'account_no' => $user->account_no,
                    'total_due_payments' => $total_pay
                );
                $save_user_account_data = DB::table('accounts')->insertGetId($users_accounts_data);

                // SAVE USER PAYMENT SCHEDULE
                $user_payment_schedule = array(
                    'account_no_id' => $save_user_account_data,
                    'inv_type' => $user->inv_type_id,
                    'tot_payable_amnt' => $total_pay,
                    'monthly_amount' => $monthly_pay
                );

                $save_user_payment_schedule = DB::table('payment_schedule')->insertGetId($user_payment_schedule);
            } elseif ($user->inv_type_id == 2) {
                // CALCULATION OF COMPOUND INTEREST MONTHLY
                $principal = $user->investment_amount;
                $interestRate = 0.2;
                $term = $user->investment_duration - 1;

                $accu_interest_array = array();
                for ($i = 0; $i < $term; $i++) {
                    $total = $principal * $interestRate;
                    $principal += $total;
                    $accu_interest_array[] = (int)$total;
                }
                $monthly_payment = json_encode($accu_interest_array);
                $total_comp_int = json_encode(array_sum($accu_interest_array));

                // SAVE USER UNIQUE ACCOUNT (GENERATED ACCOUNT DATA)
                $users_accounts_data = array(
                    'user_id' => $saved_user_id,
                    'account_no' => $user->account_no,
                    'total_due_payments' => $total_comp_int
                );
                $save_user_account_data = DB::table('accounts')->insertGetId($users_accounts_data);

                // SAVE USER PAYMENT SCHEDULE
                $user_payment_schedule = array(
                    'account_no_id' => $save_user_account_data,
                    'inv_type' => $user->inv_type_id,
                    'tot_payable_amnt' => $total_comp_int,
                    'comp_monthly_pay' => $monthly_payment
                );

                $save_user_payment_schedule = DB::table('payment_schedule')->insertGetId($user_payment_schedule);
            } elseif ($user->inv_type_id == 3) {
                // GET INVESTMENT DATA FOR MONTHLY PLUS COMPOUNDEDE INV TYPE
                $monthly_inv_amount = $request->input('monthly_inv_amount');
                $monthly_inv_duration = $request->input('monthly_inv_duration');
                $compounded_inv_amount =  $user->investment_amount -  $monthly_inv_amount;
                $compounded_inv_duration = $request->input('compounded_inv_duration');

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
                    $accu_interest_array[] = (int)$total;
                }
                $monthly_payment = json_encode($accu_interest_array);
                $total_comp_int = json_encode(array_sum($accu_interest_array));

                $total_due_pay = $total_comp_int + $total_monthly_pay;

                // SAVE USER UNIQUE ACCOUNT (GENERATED ACCOUNT DATA)
                $users_accounts_data = array(
                    'user_id' => $saved_user_id,
                    'account_no' => $user->account_no,
                    'total_due_payments' => $total_due_pay
                );
                $save_user_account_data = DB::table('accounts')->insertGetId($users_accounts_data);

                // SAVE USER PAYMENT SCHEDULE
                $user_payment_schedule = array(
                    'account_no_id' => $save_user_account_data,
                    'inv_type' => $user->inv_type_id,
                    'tot_payable_amnt' => $total_due_pay,
                    'monthly_amount' => $monthly_inv_pay,
                    'comp_monthly_pay' => $monthly_payment,
                    'tot_comp_amount' => $total_comp_int
                );

                $save_user_payment_schedule = DB::table('payment_schedule')->insertGetId($user_payment_schedule);
            }

            // CALCULATE LAST PAYMENT DATE
            $inv_duration =  $request->input('inv_duration');
            $inv_date =  $request->input('inv_date');
            $last_pay_date = Carbon::parse($inv_date)->addMonths($inv_duration)->format('Y-m-d');
            echo $last_pay_date;

            // GET ALL THE PAYMENT DATES FOR A USER (MONTHLY INVESTMENT TYPE)
            $inv_date = Carbon::parse($inv_date);

            for ($i = 0; $i < $inv_duration; $i++) {
                $pay_dates[] = $inv_date->addMonth()->format('Y-m-d');
            }
            $pay_dates = json_encode($pay_dates);

            // SAVE USER PREFERED MODE OF PAYMENT
            if ($user->inv_type_id == 1) {
                $user_payment_mode = array(
                    'user_id' => $saved_user_id,
                    'pay_mode_id' => $user->pay_mode_id,
                    'pay_mpesa_no' => $user->pay_mpesa_no,
                    'pay_bank_id' => $user->pay_bank_id,
                    'pay_bank_acc' => $user->pay_bank_acc,
                    'pay_dates' => $pay_dates
                );
                $save_user_payment_data = DB::table('user_pay_modes')->insertGetId($user_payment_mode);
            } elseif ($user->inv_type_id == 2) {
                $user_payment_mode = array(
                    'user_id' => $saved_user_id,
                    'pay_mode_id' => $user->pay_mode_id,
                    'pay_mpesa_no' => $user->pay_mpesa_no,
                    'pay_bank_id' => $user->pay_bank_id,
                    'pay_bank_acc' => $user->pay_bank_acc,
                    'pay_dates' => date('Y-m-d', strtotime($last_pay_date))
                );
                $save_user_payment_data = DB::table('user_pay_modes')->insertGetId($user_payment_mode);
            } else {
                $user_payment_mode = array(
                    'user_id' => $saved_user_id,
                    'pay_mode_id' => $user->pay_mode_id,
                    'pay_mpesa_no' => $user->pay_mpesa_no,
                    'pay_bank_id' => $user->pay_bank_id,
                    'pay_bank_acc' => $user->pay_bank_acc,
                    'pay_dates' => $pay_dates
                );
                $save_user_payment_data = DB::table('user_pay_modes')->insertGetId($user_payment_mode);
            }


            // SAVE USER INVESTMENTS
            $investments_data = array(
                'trans_id' => $user->trans_id,
                'inv_date' => $user->inv_date,
                'account_no_id' => $save_user_account_data,
                'investment_amount' => $user->investment_amount,
                'investment_duration' => $user->investment_duration,
                'inv_type_id' => $user->inv_type_id,
                'inv_mode_id' => $user->inv_mode_id,
                'mpesa_trans_code' => $user->mpesa_trans_code,
                'inv_bank_id' => $user->inv_bank_id,
                'bank_trans_code' => $user->bank_trans_code,
                'inv_bank_cheq_id' => $user->inv_bank_cheq_id,
                'cheque_no' => $user->cheque_no,
                'initiated_by' => Auth::user()->id,
                'last_pay_date' => date('Y-m-d', strtotime($last_pay_date))
            );
            $save_investment_data = DB::table('investments')->insertGetId($investments_data);

            //SAVE USER ROLE DATA
            $role_id = 3;
            $client_role_data = array(
                'role_id' => $role_id,
                'model_id' => $saved_user_id
            );

            $save_user_role_data = DB::table('model_has_roles')->insert($client_role_data);
            DB::commit();
            // Alert::success('New Client', 'Client added successfully');
            toast('New client added successfully', 'success', 'top-right');
            // alert()->success('Investor Created', 'Successfully')->toToast();
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        $data['customer_data'] = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('users_details.*'),
                DB::raw('accounts.*'),
                DB::raw('investments.*'),
                DB::raw('user_pay_modes.*'),
                DB::raw('payment_schedule.*'),
                DB::raw('payment_schedule.monthly_amount'),
                DB::raw('payment_methods.*'),
                DB::raw('banks.*')
            )
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
            ->leftJoin('investments', 'accounts.id', '=', 'investments.account_no_id')
            ->leftJoin('user_pay_modes', 'users.id', '=', 'user_pay_modes.user_id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            ->leftJoin('payment_methods', 'user_pay_modes.pay_mode_id', '=', 'payment_methods.method_id')
            ->leftJoin('banks', 'user_pay_modes.pay_bank_id', '=', 'banks.bank_id')
            ->where('users.id', '=', $id)->first();



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
            $pay_dates = $value;
        }
        $pay_dates = explode(',', $pay_dates);
        $amount = $data['customer_data']->monthly_amount;
        // echo "<pre>";
        // print_r($pay_dates);
        // exit;

        $accumulated = User::interest();
        echo $accumulated;
        exit;

        // CALCULATION OF COMPOND INTEREST

        $data['customer_investments'] = DB::table('investments')
            ->select(
                DB::raw('sum(investment_amount) as user_sum,
                        sum(payout) as user_payout,
                        sum(total_payout) as user_total_payout, account_no_id '),
                DB::raw('accounts.*'),
                DB::raw('users.*')
            )

            ->leftJoin('accounts', 'investments.account_no_id', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->groupBy('investments.account_no_id')
            ->where('users.id', '=', $id)
            ->first();

        // $data['customer_payments'] = DB::table('payments')
        //     ->select(
        //         DB::raw('sum(payment_amount) as total_payments_made, account_no_id '),
        //         DB::raw('accounts.*'),
        //         DB::raw('users.*')
        //     )

        //     ->leftJoin('accounts', 'payments.account_no_id', 'accounts.id')
        //     ->leftJoin('users', 'accounts.user_id', 'users.id')
        //     ->where('users.id', '=', $id)
        //     ->first();

        $data['customer_trans'] = DB::table('investments')
            ->select(
                DB::raw('investments.*'),
                DB::raw('accounts.*'),
                DB::raw('users.*')
            )
            ->leftJoin('accounts', 'investments.account_no_id', '=', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->orderBy('investments.investment_id', 'asc')
            ->where('users.id', '=', $id)
            ->get();

        return view('users.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        // GET PAYMENT METHODS AND BANKS
        $data['payment_mode'] = PaymentMethod::getPaymentMethods();
        $data['banks'] = Bank::getBanks();

        // FETCH CLIENTS DETAILS
        $data['customer_data'] = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('users_details.*'),
                DB::raw('accounts.*'),
                DB::raw('investments.*'),
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
            ->where('users.id', '=', $id)->first();

        // echo "<pre>";
        // print_r( $data['customer_data']);
        // exit;

        // GET CLIENT PAYMENT DATES AND TOTAL AMOUNTS PAID FOR CLIENT (DATE THE CLIENT WAS PAID)
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

        // GET THE NEXT PAYMENT DATE FOR THE CLIENT
        // CHECK IF THE PAYMENT DATE EXISTS, IF YES SKIP AND PICK THE LEAST DATE
        $data['next_pay_date'] = min(array_diff($pay_dates, $user_pay_dates));

        Session::put('next_pay_day', $data['next_pay_date']);

        // GET CLIENT TOTAL INVESMENTS
        $data['customer_investments'] = DB::table('investments')
            ->select(
                DB::raw('sum(investment_amount) as user_sum, account_no_id '),
                DB::raw('investments.*'),
                DB::raw('accounts.*'),
                DB::raw('users.*')
            )

            ->leftJoin('accounts', 'investments.account_no_id', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->groupBy('investments.account_no_id')
            ->where('users.id', '=', $id)
            ->first();

        // GET CLIENT MONTHLY PAYMENTS FOR MONTHLY INVESTMENT TYPE
        $data['tot_payable'] = DB::table('payment_schedule')
            ->select(
                DB::raw('sum(tot_payable_amnt) as user_tot_payable, account_no_id '),
                DB::raw('payment_schedule.*'),
                DB::raw('accounts.*'),
                DB::raw('user_pay_modes.*'),
                DB::raw('users.*')
            )

            ->leftJoin('accounts', 'payment_schedule.account_no_id', 'accounts.id')
            ->leftJoin('user_pay_modes', 'accounts.user_id', 'user_pay_modes.user_id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->groupBy('payment_schedule.account_no_id')
            ->where('users.id', '=', $id)
            ->first();

        // GET CLIENT PAYMENT HISTORY FOR BOTH MONTHLY AND COMPOUNDED

        $data['client_payments'] = DB::table('payments')
            ->select(
                DB::raw('payments.*'),
                DB::raw('payments.created_at AS payment_date'),
                DB::raw('accounts.*'),
                DB::raw('users.*')
            )
            ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->where('users.id', '=', $id)
            ->orderBy('payments.payment_id', 'desc')->get();

        // GET CLIENT PAYMENTS COMPOUNDED

        $client_payments_comp = DB::table('payments')
            ->select(
                DB::raw('payments.*'),
                DB::raw('payments.created_at AS payment_date'),
                DB::raw('accounts.*'),
                DB::raw('users.*')
            )
            ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->where('users.id', '=', $id)
            ->orderBy('payments.payment_id', 'desc')->get();

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
            ->first();

        // echo "<pre>";
        // print_r($client_monthly_com);
        // exit;

        $data['comp_payable_amout'] = $client_monthly_com->tot_payable_amnt;
        // echo "<pre>";
        // print_r($data['comp_payable_amout']);
        // exit;

        // GET PAYMENT PLAN FOR THE CLIENT
        //     $client_monthly_com = json_decode(json_encode($client_monthly_com), true);
        //     $client_monthly_com = array_column($client_monthly_com, 'comp_monthly_pay');

        //     $client_monthly_com = str_replace('[', '', $client_monthly_com);
        //     $client_monthly_com = str_replace(']', '', $client_monthly_com);

        // foreach ($client_monthly_com as $key => $value) {
        //     $client_monthly_com = ($value);
        // }

        // $client_monthly_com = explode(',', $client_monthly_com);

        // GET CLIENT NEXT PAYMENT FOR COMPOUND
        // $data['next_pay_amount'] = min(array_diff($client_monthly_com, $client_payments_comp));

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
        // $data['next_pay_comp_amount'] = min(array_diff($client_comp_payments, $next_pay_comp));

        // GET TOTAL AMOUNT OF PAYMENT FOR MONTHLY + COMP
        // TAKE MONTHLY PAYMENT + $DATA['NEXT_PAY_AMOUNT'] (COMPOUND AMOUNT FOR A MONTH)
        if ($data['tot_payable']->comp_monthly_pay == '') {
            $data['monthly_amnt'] = $data['tot_payable']->monthly_amount;
            $data['updated_monthly_amnt'] = $data['tot_payable']->updated_next_pay;
            // $data['monthly_amnt'] = $data['tot_payable']->monthly_amount;
        }
        // }elseif($data['tot_payable']->comp_monthly_pay !='' && $data['tot_payable']->monthly_amount ==''){
        //     $monthly_amnt = $data['tot_payable']->monthly_amount;
        //     $monthly_comp_amnt = $data['next_pay_amount'];
        //     $data['tot_monthly_payable'] = $monthly_amnt + $monthly_comp_amnt;
        elseif ($data['tot_payable']->comp_monthly_pay != '' && $data['tot_payable']->monthly_amount != '') {
            $data['monthly_amnt'] = $data['tot_payable']->monthly_amount;
            $data['tot_comp_amount'] =  $data['tot_payable']->tot_comp_amount;
            // $data['tot_monthly_payable'] =   $data['monthly_amnt'] + $monthly_comp_amnt;
        }

        // GET CLIENT TOTAL PAYAMENTS

        $data['customer_payments'] = DB::table('payments')
            ->select(
                DB::raw('sum(payment_amount) as total_payments_made, account_no_id '),
                DB::raw('payments.*'),
                DB::raw('accounts.*'),
                DB::raw('users.*')
            )

            ->leftJoin('accounts', 'payments.account_no_id', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->where('users.id', '=', $id)
            ->first();

        // GET CLIENT TOTAL DUE PAYAMENTS FOR A CLIENT

        $data['tot_due_payments'] = DB::table('accounts')
            ->select(
                DB::raw('accounts.*'),
                DB::raw('users.*')
            )
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->where('users.id', '=', $id)
            ->first();

        // echo "<pre>";
        // print_r($data['tot_due_payments']);
        // exit;

        // FETCH CLIENT PERSONAL INVESTMENTS
        $data['customer_trans'] = DB::table('investments')
            ->select(
                DB::raw('investments.*'),
                DB::raw('accounts.*'),
                DB::raw('payment_schedule.*'),
                DB::raw('inv_types.*'),
                DB::raw('inv_modes.*'),
                DB::raw('banks.*'),
                DB::raw('users.*'),
                DB::raw('users_details.*')
            )
            ->leftJoin('accounts', 'investments.account_no_id', '=', 'accounts.id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            ->leftJoin('inv_types', 'investments.inv_type_id', '=', 'inv_types.inv_id')
            ->leftJoin('inv_modes', 'investments.inv_mode_id', '=', 'inv_modes.id')
            // FIND A WAY OF JOINING WITH INV_BANK_CHEQ_ID TOO TO GET THE BANK CHEQUE NAME
            ->leftJoin('banks', 'investments.inv_bank_id', '=', 'banks.bank_id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->orderBy('investments.investment_id', 'asc')
            ->where('users.id', '=', $id)
            ->get();

        $data['customer_trans']->map(function ($item) {

            $name = DB::table('users')
                ->select(
                    DB::raw('users.name AS initiated_by_name')
                )
                ->where('users.id', '=', $item->initiated_by)->get();

            $item->created_by_name = json_encode($name);
            $item->created_by_name = str_replace('[{"initiated_by_name":"', '', $item->created_by_name);
            $item->created_by_name = str_replace('"}]', '', $item->created_by_name);
            return $item;
        });

        // FETCH CLIENT PERSONAL PAYMENTS
        $data['client_payments'] = DB::table('payments')
            ->select(
                DB::raw('payments.*'),
                DB::raw('payments.created_at AS payment_created_at'),
                DB::raw('accounts.*'),
                DB::raw('payment_schedule.*'),
                DB::raw('users.*')
            )
            ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->orderBy('payments.payment_id', 'asc')
            ->where('users.id', '=', $id)
            ->get();

        return view('users.edit')->with($data);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        // $validator = Validator::make($request->all(), [
        //     // "name" => 'string|min:5|max:50',
        // ]);

        // if ($validator->fails()) {
        //     DB::rollBack();
        //     Alert::error('Update User', 'Oops!!! An error ocurred while updating user details');
        //     return back();
        // } else {

        try {
            $user = User::find($id);
            $user->name = strtoupper($request->input('name'));
            // $user->email = $request->input('email');
            $user->save();
            DB::beginTransaction();

            $updated_user_id = $user->id;

            $user->telephone = $request->input('telephone');
            $user->id_no = $request->input('id_no');
            $user->dob = $request->input('dob');
            $user->account_no = $request->input('account_no');
            $user->home_address = strtoupper($request->input('home_address'));
            $user->home_town = strtoupper($request->input('home_town'));
            $user->kin_name = strtoupper($request->input('kin_name'));
            $user->kin_telephone = $request->input('kin_telephone');

            $user->pay_mode_id = $request->input('pay_mode_id');
            $user->pay_mpesa_no = $request->input('pay_mpesa_no');
            $user->pay_bank_id = $request->input('pay_bank_id');
            $user->pay_bank_acc = $request->input('pay_bank_acc');

            $users_details_data = array(
                'user_id' => $updated_user_id,
                'telephone' =>  $user->telephone,
                'id_no' =>  $user->id_no,
                'dob' => $user->dob,
                'home_address' =>  strtoupper($user->home_address),
                'home_town' => strtoupper($user->home_town),
                'kin_name' =>  strtoupper($user->kin_name),
                'kin_telephone' => $user->kin_telephone
            );

            $save_user_details = DB::table('users_details')->where('user_id', $updated_user_id)->update($users_details_data);

            $user_payment_mode = array(
                'user_id' => $updated_user_id,
                'pay_mode_id' => $user->pay_mode_id,
                'pay_mpesa_no' => $user->pay_mpesa_no,
                'pay_bank_id' => $user->pay_bank_id,
                'pay_bank_acc' => $user->pay_bank_acc
            );
            $save_user_payment_data = DB::table('user_pay_modes')->insertGetId($user_payment_mode);

            DB::commit();
            Alert::success('Update lient', 'Client updated successfully');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            Alert::error('Update User', 'Oops!!! An error ocurred while updating client details');
            return back();
        }
        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function ApproveClient(Request $request)
    {
        $approved_caf = DB::table('users')
            ->select(
                DB::raw('users.*')
            )
            ->where('users.id', '=', $request->id)
            ->first();

        $approved_client_id = $request->id;
        $status = 1;

        DB::table('users')->where('id', $approved_client_id)->update(
            [
                'status' => $status
            ]
        );
        Alert::success('Approve Client', 'Client approved successfully');
        return back();
    }

    public function destroy($id)
    {
        DB::table('users')->where('id', $id)->delete();
        Alert::success('Delete User', 'User deleted successfully');
        return back();
    }
}