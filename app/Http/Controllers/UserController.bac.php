<?php

namespace App\Http\Controllers;

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
use App\Model\Terminations;
use Faker\Provider\el_GR\Payment;
use App\Model\PaymentMethod;
use App\Model\Bank;
use App\Model\Blacklist;
use App\Model\Referals;
use App\Model\GenerateAccountNumber;
use App\Model\InvestmentMode;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Model\PaymentSchedule;
use App\Model\UserPayMode;

use Illuminate\Support\Facades\Mail;
use App\Mail\SuccessfulRegistration;
use App\Mail\InvestmentReceived;

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

        // return Excel::download(new UsersExport, 'users.xlsx');
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

        return view('users.index')->with($data);
    }

    public function get_numbers(Request $request)
    {
        $search_term = $request->input('q');
        $search_term = '%' . $search_term . '%';

        $data = DB::table('users_details')
            ->select(
                DB::raw('telephone as text'),
                DB::raw('users_details.user_id as id'),
                DB::raw('users.name as referer_name'),
                DB::raw('model_has_roles.role_id')
            )
            ->join('users', 'users_details.user_id', '=', 'users.id')
            ->join('model_has_roles', 'users_details.user_id', 'model_has_roles.model_id')
            ->where('telephone', 'like', $search_term)
            ->where('model_has_roles.role_id', '=', 3)
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
        $data['generated_account'] = Account::nextAccountNumber();
        $data['accounts'] = Account::getAccounts();
        $data['inv_modes'] = InvestmentMode::getInvModes();
        $data['inv_types'] = InvestmentType::getInvTypes();
        $data['payment_mode'] = PaymentMethod::getPaymentMethods();
        $data['banks'] = Bank::getBanks();
        $blacklists = Blacklist::getBlacklists();

        $blacklists = json_decode(json_encode($blacklists), true);
        $id_nos = array_column($blacklists, 'id_no');
        $data['phone_nos'] = array_column($blacklists, 'phone');

        // echo "<pre>";
        // print_r($id_nos);
        // echo "<pre>";
        // print_r($phone_nos);
        // exit;
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
        $blacklists = Blacklist::getBlacklists();
        $referals_restrictions = Referals::getRestrictedClients();

        $restricted_ids = array();
        foreach ($referals_restrictions as $key => $value) {
            $restricted_ids[] = $value->user_id;
        }

        $blacklists = json_decode(json_encode($blacklists), true);
        $id_nos = array_column($blacklists, 'id_no');
        $phone_nos = array_column($blacklists, 'phone');

        $refered_by = $request->input('referer_id');
        $telephone = $request->input('telephone');
        $id_no = $request->input('id_no');

        if ($refered_by) {
            if (in_array($refered_by, $restricted_ids)) {
                $restricted_client = DB::table('referal_restrictions')
                    ->select(
                        DB::raw('referal_restrictions.*')
                    )->get();

                $comm_times = $restricted_client[0]->comm_times;
                if ($comm_times > 0) {
                    $comm_times = $restricted_client[0]->comm_times;
                } elseif ($comm_times == 0) {
                    $comm_times = 6;
                }
            } else {
                $comm_times = 6;
            }
        } else {
            $comm_times = 6;
        }

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

        $real_inv_date = $request->input('inv_date');

        $tot_inv = DB::table('daily_trans_summary')
            ->where('date', '=', $real_inv_date)->first();

        $total_investment = $tot_inv->tot_investments;
        $total_investment = $total_investment + $request->input('inv_amount');

        $new_tot_investment = array(

            'tot_investments' => $total_investment
        );
        $new_balance = DB::table('daily_trans_summary')->where('date', $real_inv_date)
            ->update($new_tot_investment);

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

        elseif ((!empty($refered_by)) && (in_array($id_no, $id_nos))) {
            DB::rollBack();
            toast('This client cannot be referred again', 'error', 'top-right');
            return back();
        } elseif ((!empty($refered_by)) && (in_array($telephone, $phone_nos))) {
            DB::rollBack();
            toast('This client cannot be referred again', 'error', 'top-right');
            return back();
        } else {

            //CREATE NEW USER AND SAVE IN users TABLE
            $user = new User();
            $user->name = strtoupper($request->input('name'));
            $user->email = $request->input('email');
            $user->refered_by = $request->input('referer_id');
            $password = "FSHS$%@gdjd.//]]\[...>>><<<<<<<";
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
            $user->inv_type_id = $request->input('inv_type_id');
            $user->inv_mode_id = $request->input('inv_mode_id');
            $user->mpesa_trans_code = $request->input('mpesa_trans_code');
            $user->inv_bank_id = $request->input('inv_bank_id');
            $user->bank_trans_code = $request->input('bank_trans_code');
            $user->inv_bank_id = $request->input('inv_cheq_bank_id');
            $user->cheque_no = $request->input('cheque_no');
            $monthly_inv_amount = $request->input('monthly_inv_amount');
            $monthly_inv_duration = $request->input('monthly_inv_duration');
            $compounded_inv_amount =  $user->investment_amount -  $monthly_inv_amount;
            $compounded_inv_duration = $request->input('compounded_inv_duration');

            // // SAVE USER DETAILS DATA
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

            $inv_comm_per = 0.05;
            $inv_comm = $inv_comm_per * $user->investment_amount;
            $tot_inv_comm = $inv_comm * $comm_times;

            if ($user->inv_type_id == 1) {

                $inv_duration =  $request->input('inv_duration');
                // CALCULATE MONTHLY AND TOTAL PAYMENTS FOR MONHTLY INVESTMENT TYPE
                $inv_amount =  $request->input('inv_amount');
                $monthly_pay = 0.2 * $inv_amount;
                $total_pay = $monthly_pay * $inv_duration;

                $accu_interest_array = array();
                for ($i = 0; $i < $inv_duration; $i++) {
                    $monthly_pay = 0.2 * $inv_amount;
                    $accu_interest_array[] = (int) $monthly_pay;
                }

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

                // SAVE USER INVESTMENTS
                $investments_data = array(
                    'trans_id' => $user->trans_id,
                    'inv_date' => $user->inv_date,
                    'account_no_id' => $save_user_account_data,
                    'investment_amount' => $user->investment_amount,
                    'initial_inv' => $user->investment_amount,
                    'inv_comm' => $inv_comm,
                    'tot_inv_comm' => $tot_inv_comm,
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
            } elseif ($user->inv_type_id == 2) {
                // CALCULATION OF COMPOUND INTEREST MONTHLY
                $principal = $user->investment_amount;
                $interestRate = 0.2;
                $term = $user->investment_duration - 1;

                $accu_interest_array = array();
                for ($i = 0; $i < $term; $i++) {
                    $total = $principal * $interestRate;
                    $principal += $total;
                    $accu_interest_array[] = (int) $total;
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

                // SAVE USER INVESTMENTS
                $investments_data = array(
                    'trans_id' => $user->trans_id,
                    'inv_date' => $user->inv_date,
                    'account_no_id' => $save_user_account_data,
                    'initial_inv' => $user->investment_amount,
                    'investment_amount' => $user->investment_amount,
                    'inv_comm' => $inv_comm,
                    'tot_inv_comm' => $tot_inv_comm,
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
            } elseif ($user->inv_type_id == 3) {
                // GET INVESTMENT DATA FOR MONTHLY PLUS COMPOUNDEDED INV TYPE
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
                    $accu_interest_array[] = (int) $total;
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

                //SAVE USER PAYMENT SCHEDULE
                $user_payment_schedule = array(
                    'account_no_id' => $save_user_account_data,
                    'inv_type' => $user->inv_type_id,
                    'tot_payable_amnt' => $total_due_pay,
                    'monthly_amount' => $monthly_inv_pay,
                    'comp_monthly_pay' => $monthly_payment,
                    'tot_comp_amount' => $total_comp_int
                );

                $save_user_payment_schedule = DB::table('payment_schedule')->insertGetId($user_payment_schedule);

                // SAVE USER INVESTMENTS
                $investments_data = array(
                    'trans_id' => $user->trans_id,
                    'inv_date' => $user->inv_date,
                    'account_no_id' => $save_user_account_data,
                    'initial_inv' => $user->investment_amount,
                    'investment_amount' => $user->investment_amount,
                    'inv_comm' => $inv_comm,
                    'tot_inv_comm' => $tot_inv_comm,
                    'investment_duration' => $user->investment_duration,
                    'monthly_inv' => $monthly_inv_amount,
                    'compounded_inv' => $compounded_inv_amount,
                    'monthly_duration' => $monthly_inv_duration,
                    'comp_duration' => $compounded_inv_duration,
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
            }

            // CALCULATE LAST PAYMENT DATE
            $inv_duration =  $request->input('inv_duration');
            $inv_date =  $request->input('inv_date');
            $last_pay_date = Carbon::parse($inv_date)->addMonths($inv_duration)->format('Y-m-d');

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
                    // 'pay_dates' => date('Y-m-d', strtotime($last_pay_date))
                    'pay_dates' => $pay_dates
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

            // SAVE CLIENT PAYMENT MODES
            $client_pay_modes = array(
                'user_id' => $saved_user_id,
                'pay_mode_id' => $user->pay_mode_id,
                'pay_mpesa_no' => $user->pay_mpesa_no,
                'pay_bank_id' => $user->pay_bank_id,
                'pay_bank_acc' => $user->pay_bank_acc,

            );

            $save_user_pay_modes_data = DB::table('client_payment_modes')->insertGetId($client_pay_modes);

            //SAVE USER ROLE DATA
            $role_id = 3;
            $client_role_data = array(
                'role_id' => $role_id,
                'model_id' => $saved_user_id
            );
            $save_user_role_data = DB::table('model_has_roles')->insert($client_role_data);

            if (!empty($user->refered_by)) {
                $referee_data = DB::table('accounts')
                    ->select(
                        DB::raw('accounts.*'),
                        DB::raw('users.id as referee_id')
                    )
                    ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                    ->where('users.id', '=', $user->refered_by)
                    ->first();

                $account_id = $referee_data->id;
                $due_pay = $referee_data->total_due_payments;
                $new_due_pay = $due_pay + $inv_comm;


                $acc_bal = array(

                    'total_due_payments' => $new_due_pay
                );
                $acc_balances = DB::table('accounts')->where('id', $account_id)
                    ->update($acc_bal);
            }


            DB::commit();

            $inv_type = InvestmentType::getInvTypes()->where('inv_id', '=', $user->inv_type_id)->first();
            $inv_type_name = $inv_type->inv_type;

            $objDemo = new \stdClass();
            $objDemo->subject = 'Successful Registration';
            $objDemo->subject1 = 'Investment Received';
            $company = "Inter-Web Global Fortune";
            $objDemo->company = $company;

            //1. Send to the user
            $message = "You have been successfully registered as a client at Inter-Web Global Fortune";
            $message1 = "We have received your investment. You will be notified upon approval of your investment.";
            $objDemo->email = $user->email;
            $objDemo->name = $user->name;
            $objDemo->account_no = $user->account_no;
            $objDemo->amount = $user->investment_amount;
            $objDemo->inv_date = $inv_date;
            $objDemo->duration = $inv_duration;
            $objDemo->inv_type = $inv_type_name;
            $objDemo->message = $message;
            $objDemo->message1 = $message1;

            Mail::to($objDemo->email)->send(new SuccessfulRegistration($objDemo));
            Mail::to($objDemo->email)->send(new InvestmentReceived($objDemo));
            toast('New client added successfully', 'success', 'top-right');
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

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
        $data['inv_modes'] = InvestmentMode::getInvModes();
        $data['inv_types'] = InvestmentType::getInvTypes()->where('inv_id', '!=', '3');

        // FETCH CLIENTS DETAILS
        $data['customer_data'] = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('users_details.*'),
                DB::raw('accounts.*'),
                DB::raw('accounts.id AS accnt_id'),
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
            ->where('users.id', '=', $id)
            ->first();

        // GET CLIENT REFERALS AND ALL THE RELATED DATA (INVESTMENTS, TOPUPS)
        // FETCH CLIENTS DETAILS AND INVESTMENTS
        $data['referer'] = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('users.id as referee_id'),
                DB::raw('users_details.*'),
                DB::raw('accounts.*'),
                DB::raw('accounts.id AS accnt_id'),
                DB::raw('investments.*'),
                DB::raw('user_pay_modes.*'),
                DB::raw('inv_types.*'),
                DB::raw('payment_schedule.*'),
                DB::raw('payment_schedule.monthly_amount'),
                DB::raw('payment_methods.*'),
                DB::raw('banks.*')
            )
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
            ->leftJoin('investments', 'accounts.id', '=', 'investments.account_no_id')
            ->leftJoin('inv_types', 'investments.inv_type_id', '=', 'inv_types.inv_id')
            ->leftJoin('user_pay_modes', 'users.id', '=', 'user_pay_modes.user_id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id') //.''''
            ->leftJoin('payment_methods', 'user_pay_modes.pay_mode_id', '=', 'payment_methods.method_id')
            ->leftJoin('banks', 'user_pay_modes.pay_bank_id', '=', 'banks.bank_id')
            ->where('users.refered_by', '=', $id)
            ->where('investments.inv_status_id', '=', 1)
            ->where('tot_inv_comm', '>', 0)
            ->get();

        // GET CLIENT TOPUP HISTORY FOR REFEREE

        $referer_topups = DB::table('topups')
            ->select(
                DB::raw('topups.*'),
                DB::raw('topups.created_at AS topped_date'),
                DB::raw('accounts.id'),
                DB::raw('users.id')
            )
            ->leftJoin('accounts', 'topups.account_id', '=', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->where('users.refered_by', '=', $id)
            ->orderBy('topups.topup_id', 'desc')
            ->where('tot_topup_comm', '>', 0)
            ->get();

        // echo "<pre>";
        // print_r($referer_topups);
        // exit;

        // END CLIENT REFERALS

        $data['client_payment_modes'] = DB::table('client_payment_modes')
            ->select(
                DB::raw('client_payment_modes.*'),
                DB::raw('payment_methods.*'),
                DB::raw('banks.*'),
                DB::raw('users.*')
            )
            ->leftJoin('payment_methods', 'client_payment_modes.pay_mode_id', '=', 'payment_methods.method_id')
            ->leftJoin('banks', 'client_payment_modes.pay_bank_id', '=', 'banks.bank_id')
            ->leftJoin('users', 'client_payment_modes.user_id', '=', 'users.id')
            ->where('users.id', '=', $id)
            ->get();

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

        $inv_dates = json_decode(json_encode($data['referer']), true);
        $inv_dates = array_column($inv_dates, 'inv_date');

        // ELIMINATE DATES LESS THAN THE PREVIOUS PAYMENT DATE

        $topup_dates = json_decode(json_encode($referer_topups), true);
        $topup_dates = array_column($topup_dates, 'topped_at');

        $next_pay = array_diff($pay_dates, $user_pay_dates);
        if (empty($next_pay)) {
            $data['next_pay_date'] = 'FULLY PAID';
        } else {
            $data['next_pay_date'] = min(array_diff($pay_dates, $user_pay_dates));
            $data['next_pay_date'] = json_decode(json_encode($data['next_pay_date'], true));
            $data['next_pay_date'] = (array) $data['next_pay_date'];
        }

        // GET INVETSMENT AND TOPUP COMMISSIONS FOR ALL REFERED CLIENTS
        $inv_comm = json_decode(json_encode($data['referer']), true);
        $inv_comm = array_column($inv_comm, 'inv_comm');

        $topup_comm = json_decode(json_encode($referer_topups), true);
        $topup_comm = array_column($topup_comm, 'topup_comm');

        // SUM ALL THE RELEVANT INVESTMENT COMMISSIONS AND GET THE TOTAL
        $inv_comm_sum = 0;
        foreach ($inv_comm as $key => $item) {
            $inv_comm_sum += $item;
        }

        // SUM ALL THE RELEVANT TOPUP COMMISSIONS AND GET THE TOTAL
        $topup_comm_sum = 0;
        foreach ($topup_comm as $key => $item) {
            $topup_comm_sum += $item;
        }

        $tot_comm = $inv_comm_sum + $topup_comm_sum;

        $next_pay = array_diff($pay_dates, $user_pay_dates);
        if (empty($data['next_pay_date'])) {
            $data['next_pay_date'] = "FULLY PAID";
        } elseif (!empty($next_pay)) {

            $data['next_pay_date'] = min(array_diff($pay_dates, $user_pay_dates));
        }

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

        $data['inv_duration'] = $data['customer_investments']->investment_duration;
        $data['inv_date'] = $data['customer_investments']->inv_date;
        Session::put('inv_duration', $data['inv_duration']);
        Session::put('inv_date', $data['inv_date']);

        $today = Carbon::now('Africa/Nairobi')->toDateString();
        $today = '2020-03-22';

        $comp_pay_date = $data['customer_investments']->last_pay_date;
        Session::put('last_pay_date', $comp_pay_date);

        if ($comp_pay_date == $today) {
            $data['comp_pay_date'] = 1;
        } else {
            $data['comp_pay_date'] = 0;
        }

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


        $terminated = $data['customer_investments']->termination_type;
        // echo "<pre>";
        // print_r($terminated);
        // exit;
        if ($data['tot_payable']->topped_up == 0 && $terminated == '') {
            // ADD THE EXPECTED PAYMENT AMOUNT PLUS THE COMMISSION
            $data['next_amount'] =  $data['tot_payable']->monthly_amount + $tot_comm;
        } elseif ($data['tot_payable']->topped_up == 0 && $terminated != '') {
            // echo "Chris";
            $termination_payments =  DB::table('termination_payments')
                ->select(
                    DB::raw('termination_payments.*'),
                    DB::raw('users.*')
                )
                ->leftJoin('users', 'termination_payments.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->orderBy('termination_payments.ter_pay_id', 'desc')->first();

            $payment_amount =  $termination_payments->pay_amount;
            $payment_date =  $termination_payments->pay_date;

            $ter_payments = DB::table('payments')
                ->select(
                    DB::raw('payments.*'),
                    DB::raw('accounts.*'),
                    DB::raw('users.*')
                )
                ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->where('payments.payment_amount', '=', $payment_amount)
                ->where('payments.user_pay_date', '=', $payment_date)
                ->orderBy('payments.payment_id', 'desc')->first();

            if ($ter_payments) {

                $data['next_amount'] =  $data['tot_payable']->monthly_amount + $tot_comm;
            } else {

                // $data['next_amount'] =  $data['tot_payable']->updated_monthly_pay + $tot_comm;
                $data['next_amount'] =  $data['tot_payable']->updated_monthly_pay_ter + $tot_comm;
            }

            //  echo   $data['next_amount'];
        } elseif ($data['tot_payable']->topped_up == 1 && $terminated == '') {

            $data['updated_next_pay'] =  $data['tot_payable']->updated_next_pay + $tot_comm;

            // CHECK IF THE UPDATED NEXT PAY HAS BEEN PAID
            $data['client_payments'] = DB::table('payments')
                ->select(
                    DB::raw('payments.*'),
                    DB::raw('accounts.*'),
                    DB::raw('users.*')
                )
                ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->where('payments.total_payment', '=',  $data['updated_next_pay'])
                ->orderBy('payments.payment_id', 'desc')->first();

            if ($data['client_payments']) {
                $data['next_amount'] =  $data['tot_payable']->updated_monthly_pay + $tot_comm;
            } else {
                $data['next_amount'] =  $data['tot_payable']->updated_next_pay + $tot_comm;
            }
        } elseif ($data['tot_payable']->topped_up == 1 && $terminated != '') {

            $termination_payments =  DB::table('termination_payments')
                ->select(
                    DB::raw('termination_payments.*'),
                    DB::raw('users.*')
                )
                ->leftJoin('users', 'termination_payments.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->orderBy('termination_payments.ter_pay_id', 'desc')->first();

            $payment_amount =  $termination_payments->pay_amount;
            $payment_date =  $termination_payments->pay_date;

            $ter_payments = DB::table('payments')
                ->select(
                    DB::raw('payments.*'),
                    DB::raw('accounts.*'),
                    DB::raw('users.*')
                )
                ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->where('payments.payment_amount', '=', $payment_amount)
                ->where('payments.user_pay_date', '=', $payment_date)
                ->orderBy('payments.payment_id', 'desc')->first();

            if ($ter_payments) {
                $data['next_amount'] =  $data['tot_payable']->monthly_amount + $tot_comm;
            } else {
                $data['next_amount'] =  $data['tot_payable']->updated_monthly_pay_ter + $tot_comm;
            }
        }

        $data['next_amount'] =  $data['next_amount'];

        Session::put('next_amount', $data['next_amount']);

        $data['tot_comm'] = $tot_comm;

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


        // GET CLIENT PAYMENT HISTORY FOR BOTH MONTHLY AND COMPOUNDED

        $data['client_topups'] = DB::table('topups')
            ->select(
                DB::raw('topups.*'),
                DB::raw('topups.created_at AS topped_date'),
                DB::raw('accounts.*'),
                DB::raw('accounts.id as acc_id'),
                DB::raw('inv_modes.*'),
                DB::raw('banks.*'),
                DB::raw('users.*'),
                DB::raw('users_details.*')
            )
            ->leftJoin('accounts', 'topups.account_id', '=', 'accounts.id')
            ->leftJoin('inv_modes', 'topups.inv_mode_id', '=', 'inv_modes.id')
            ->leftJoin('banks', 'topups.inv_bank_id', '=', 'banks.bank_id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->leftJoin('users_details', 'users.id', 'users_details.user_id')
            ->where('users.id', '=', $id)
            ->orderBy('topups.topup_id', 'desc')
            ->get();

        $data['number_of_topups'] = count($data['client_topups']);

        $data['client_topups']->map(function ($item) {

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

        $data['tot_topups'] = DB::table('topups')
            ->select(
                DB::raw('sum(topup_amount) as c_tot_topups, account_id'),
                DB::raw('accounts.id'),
                DB::raw('users.id')
            )
            ->leftJoin('accounts', 'topups.account_id', '=', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->where('users.id', '=', $id)
            ->groupBy('topups.account_id')
            ->first();

        $data['c_tot_inv'] =  $data['customer_investments']->user_sum;
        if ($data['tot_topups'] == '') {

            $data['c_tot_topups'] = 0;
        } else {
            $data['c_tot_topups'] = $data['tot_topups']->c_tot_topups;
        }

        $data['real_tot_inv'] =  $data['c_tot_inv'];
        // $data['real_tot_inv'] =  $data['c_tot_inv'] - $data['c_tot_topups'];


        if ($data['customer_data']->inv_status_id == 0) {

            $data['approved'] = "N";
        } else {
            $data['approved'] = "Y";
        }
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

        if ($data['customer_data']->inv_type == 2) {
            $comp_payment_amount = $client_monthly_com->tot_payable_amnt;

            $payment_exist = DB::table('payments')
                ->select(
                    DB::raw('payments.*'),
                    DB::raw('accounts.*'),
                    DB::raw('users.*')
                )
                ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                ->where('users.id', '=', $id)
                ->where('payments.payment_amount', '=', $comp_payment_amount)
                ->where('payments.user_pay_date', '=', $comp_pay_date)
                ->orderBy('payments.payment_id', 'desc')->first();

            if (empty($payment_exist)) {

                $data['comp_paid'] = 'N';
            } else {

                $data['comp_paid'] = 'Y';
            }
        }

        $today = Carbon::now('Africa/Nairobi')->toDateString();
        // $today = '2020-03-22';

        if ($data['customer_data']->inv_type == 2) {
            if ($data['customer_data']->inv_status_id == 0) {

                $data['approved'] = "N";
                $data['comp_payable_amout'] = $data['customer_data']->total_due_payments = 0;
            } elseif ($data['customer_data']->inv_status_id == 1 &&  $data['comp_paid'] = 'N' && $comp_pay_date != $today) {
                //echo "Christine";
                $data['approved'] = "Y";
                if ($terminated) {

                    $termination_payments =  DB::table('termination_payments')
                        ->select(
                            DB::raw('termination_payments.*'),
                            DB::raw('users.*')
                        )
                        ->leftJoin('users', 'termination_payments.user_id', '=', 'users.id')
                        ->where('users.id', '=', $id)
                        ->orderBy('termination_payments.ter_pay_id', 'desc')->first();

                    $payment_amount =  $termination_payments->pay_amount;
                    $payment_date =  $termination_payments->pay_date;

                    $ter_payments = DB::table('payments')
                        ->select(
                            DB::raw('payments.*'),
                            DB::raw('accounts.*'),
                            DB::raw('users.*')
                        )
                        ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
                        ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
                        ->where('users.id', '=', $id)
                        ->where('payments.payment_amount', '=', $payment_amount)
                        ->where('payments.user_pay_date', '=', $payment_date)
                        ->orderBy('payments.payment_id', 'desc')->first();

                    // echo "<pre>";
                    // print_r($ter_payments);
                    // exit;

                    if ($ter_payments) {
                        //echo "PAid";
                        if ($comp_pay_date == $today) {
                            $data['comp_payable_amout'] = $data['customer_data']->total_due_payments  + $tot_comm;
                        } else {
                            $data['comp_payable_amout'] = 0;
                            $data['comp_payable_amout'] = $data['comp_payable_amout'] + $tot_comm;
                        }
                    } else {
                        // echo "Unpaid";
                        $data['comp_payable_amout'] = $payment_amount  + $tot_comm;
                    }
                    //  $data['comp_payable_amout'] = $data['customer_data']->total_due_payments  + $tot_comm;
                } else {


                    if ($comp_pay_date == $today) {
                        $data['comp_payable_amout'] = $data['customer_data']->total_due_payments  + $tot_comm;
                    } else {
                        $data['comp_payable_amout'] = 0;
                        $data['comp_payable_amout'] = $data['comp_payable_amout'] + $tot_comm;
                    }
                }
            } elseif ($data['customer_data']->inv_status_id == 1 &&  $data['comp_paid'] = 'N' && $comp_pay_date == $today) {

                $data['approved'] = "Y";
                $data['comp_payable_amout'] = $data['customer_data']->total_due_payments  + $tot_comm;
            }
            // echo $data['comp_payable_amout'];
            // exit;
        }
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

        // GET TOTAL AMOUNT OF PAYMENT FOR MONTHLY + COMP
        // TAKE MONTHLY PAYMENT + $DATA['NEXT_PAY_AMOUNT'] (COMPOUND AMOUNT FOR A MONTH)
        if ($data['tot_payable']->comp_monthly_pay == '' && $data['tot_payable']->total_due_payments != 0) {
            $data['monthly_amnt'] = $data['tot_payable']->monthly_amount;
            $data['updated_monthly_amnt'] = $data['tot_payable']->updated_next_pay;
        } elseif ($data['tot_payable']->total_due_payments == 0) {
            $data['monthly_amnt'] = 0;
        } elseif ($data['tot_payable']->comp_monthly_pay != '' && $data['tot_payable']->monthly_amount != '') {
            $data['monthly_amnt'] = $data['tot_payable']->monthly_amount;
            $data['tot_comp_amount'] =  $data['tot_payable']->tot_comp_amount;
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

        if ($data['tot_due_payments']->total_due_payments <= 0) {
            $data['fully_paid'] = 'Y';
        } else {
            $data['fully_paid'] = 'N';
        }

        $today = Carbon::parse('Africa/Nairobi')->now()->toDateString();
        //$today = '2020-03-22';
        if ($today == $data['next_pay_date']) {
            $data['pay_day'] = 'Y';
        } else {
            $data['pay_day'] = 'N';
        }

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
                DB::raw('payment_methods.*'),
                DB::raw('client_payment_modes.*'),
                DB::raw('banks.*'),
                DB::raw('payment_schedule.*'),
                DB::raw('users.*'),
                DB::raw('users_details.*')
            )
            ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            ->leftJoin('client_payment_modes', 'payments.payment_mode_info_id', '=', 'client_payment_modes.pay_id')
            ->leftJoin('payment_methods', 'client_payment_modes.pay_mode_id', '=', 'payment_methods.method_id')
            ->leftJoin('banks', 'client_payment_modes.pay_bank_id', '=', 'banks.bank_id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->orderBy('payments.payment_id', 'asc')
            ->where('users.id', '=', $id)
            ->get();

        $data['client_payments']->map(function ($item) {

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

        $data['terminations'] = Terminations::getTerminations()
            ->where('user_id', '=', $id);

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

        try {
            $user = User::find($id);
            $user->name = strtoupper($request->input('name'));
            $user->email = $request->input('email');

            // echo $user;
            // exit;
            $user->save();

            // echo "<pre>";
            // print_r($user);
            // exit;
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

            // $user_payment_mode = array(
            //     'user_id' => $updated_user_id,
            //     // 'pay_mpesa_no' => $user->pay_mpesa_no,
            //     // 'pay_bank_id' => $user->pay_bank_id,
            //     'pay_bank_acc' => $user->pay_bank_acc
            // );

            // // echo "<pre>";
            // // print_r($user_payment_mode);
            // // exit;
            // $save_user_payment_data = DB::table('user_pay_modes')->insertGetId($user_payment_mode);


            DB::commit();
            toast('Client details updated successfully', 'success', 'top-right');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            toast('Oops!!! An error ocurred while updating client details', 'error', 'top-right');
            return back();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function getUserProfile()
    {
        $data['user'] = Auth::user()->id;

        $data['auth_users'] = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('users.id as user_id'),
                DB::raw('users.name AS user_name'),
                DB::raw('users_details.*'),
                DB::raw('model_has_roles.*'),
                DB::raw('roles.name AS role_name')
            )
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('users.id', $data['user'])
            ->orderBy('users.id', 'desc')
            ->first();

        return view('users.profile')->with($data);
    }

    public function updateUserProfile(Request $request, user $user)
    {

        try {
            if (Hash::check($request->input('confirm_password'), $user->password)) {
                $user->email = $request->input('email');
                $user->name = $request->input('name');

                if (!empty($request->input('password'))) {
                    $user->password = Hash::make($request->input('password'));
                    $user->save();
                }
                $user->save();

                toast('Profile updated successfully', 'success', 'top-right');
                return back();
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }
        toast('Your current password is wrong', 'error', 'top-right');

        return back();
    }

    public function destroy($id)
    {
        DB::table('users')->where('id', $id)->delete();
        toast('User deleted successfully', 'success', 'top-right');
        return back();
    }
}