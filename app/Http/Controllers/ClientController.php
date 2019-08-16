<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use App\User;
use DB;
use Carbon\Carbon;
use App\Model\Blacklist;
use App\Model\Referals;
use App\Model\UserDetails;
use App\Model\Account;

use Illuminate\Support\Facades\Mail;
use App\Mail\SuccessfulRegistration;

class ClientController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['generated_account'] = Account::nextAccountNumber();
        $blacklists = Blacklist::getBlacklists();

        $blacklists = json_decode(json_encode($blacklists), true);
        $id_nos = array_column($blacklists, 'id_no');
        $data['phone_nos'] = array_column($blacklists, 'phone');

        return view('users.add')->with($data);
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

        if ($validator->fails()) {
            DB::rollBack();
            toast('Oops!!! An error ocurred while adding new customer', 'error', 'top-right');
            return back();
        } elseif ($id_no_validator->fails()) {
            DB::rollBack();
            toast('Oops!!! ID Number is already registered', 'error', 'top-right');
            return back();
        } elseif ($email_validator->fails()) {
            DB::rollBack();
            toast('Oops!!! Email address is already registered', 'error', 'top-right');
            return back();
        } elseif ($phone_validator->fails()) {
            DB::rollBack();
            toast('Oops!!! Phone number is already registered', 'error', 'top-right');
            return back();
        } elseif ((!empty($refered_by)) && (in_array($id_no, $id_nos))) {
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
            // GET USER DETAILS DATA

            $saved_user_id = $user->id;

            $telephone = $request->input('telephone');
            $id_no = $request->input('id_no');
            $dob = $request->input('dob');
            $account_no = $request->input('account_no');
            $home_address = strtoupper($request->input('home_address'));
            $home_town = strtoupper($request->input('home_town'));
            $kin_name = strtoupper($request->input('kin_name'));
            $kin_telephone = $request->input('kin_telephone');

            $generated_transaction_code = strtoupper(str_random(8));
            $trans_id = $generated_transaction_code;
            $today = Carbon::now('Africa/Nairobi')->toDateString();
            $last_pay_date = Carbon::parse($today)->addMonths(12);

            // SAVE USER DETAILS DATA
            $auth_user = Auth::user()->id;
            $users_details_data = array(
                'user_id' => $saved_user_id,
                'telephone' =>  $telephone,
                'id_no' =>  $id_no,
                'dob' => $dob,
                'home_address' =>  strtoupper($home_address),
                'home_town' => strtoupper($home_town),
                'kin_name' =>  strtoupper($kin_name),
                'kin_telephone' => $kin_telephone,
                'created_by' => $auth_user
            );

            $save_user_details_data = DB::table('users_details')->insertGetId($users_details_data);

            // SAVE USER UNIQUE ACCOUNT (GENERATED ACCOUNT DATA)
            $users_accounts_data = array(
                'user_id' => $saved_user_id,
                'account_no' => $account_no,
                'total_due_payments' => 0
            );
            $save_user_account_data = DB::table('accounts')->insertGetId($users_accounts_data);

            // SAVE USER INVESTMENTS
            $investments_data = array(
                'trans_id' => $trans_id,
                'inv_date' => $today,
                'account_no_id' => $save_user_account_data,
                'investment_amount' => 0,
                'initial_inv' => 0,
                'inv_comm' => 0,
                'tot_inv_comm' => 0,
                'investment_duration' => 0,
                'inv_type_id' => 1,
                'inv_mode_id' => 4,
                'mpesa_trans_code' => $trans_id,
                'initiated_by' => Auth::user()->id,
                'last_pay_date' => date('Y-m-d', strtotime($last_pay_date))
            );
            $save_investment_data = DB::table('investments')->insertGetId($investments_data);

            // GET ALL THE PAYMENT DATES FOR A USER (MONTHLY INVESTMENT TYPE)
            $inv_datee = Carbon::now('Africa/Nairobi');

            for ($i = 0; $i < 12; $i++) {
                $pay_dates[] = $inv_datee->addMonth()->format('Y-m-d');
            }
            $pay_dates = json_encode($pay_dates);

            // SAVE USER PREFERED MODE OF PAYMENT
            $user_payment_mode = array(
                'user_id' => $saved_user_id,
                'pay_mode_id' => 1,
                'pay_mpesa_no' => $telephone,
                'pay_dates' => $pay_dates
            );
            $save_user_payment_data = DB::table('user_pay_modes')->insertGetId($user_payment_mode);

            // SAVE USER PAYMENT SCHEDULE
            $user_payment_schedule = array(
                'account_no_id' => $save_user_account_data,
                'inv_type' => 1,
                'tot_payable_amnt' => 0,
                'monthly_amount' => 0
            );

            $save_user_payment_schedule = DB::table('payment_schedule')->insertGetId($user_payment_schedule);

            // SAVE CLIENT PAYMENT MODES
            $client_pay_modes = array(
                'user_id' => $saved_user_id,
                'pay_mode_id' => 1,
                'pay_mpesa_no' => $telephone

            );

            $save_user_pay_modes_data = DB::table('client_payment_modes')->insertGetId($client_pay_modes);

            //SAVE USER ROLE DATA
            $role_id = 3;
            $client_role_data = array(
                'role_id' => $role_id,
                'model_id' => $saved_user_id
            );
            $save_user_role_data = DB::table('model_has_roles')->insert($client_role_data);

            // Send welcome mail to the registered client

            $objDemo = new \stdClass();
            $objDemo->subject = 'Successful Registration';
            $company = "Inter-Web Global Fortune Limited";
            $objDemo->company = $company;

            //1. Send to the user
            $message = "You have been successfully registered as a client at Inter-Web Global Fortune";
            $objDemo->email = $user->email;
            $objDemo->name = $user->name;
            $objDemo->account_no = $account_no;
            $objDemo->message = $message;

            Mail::to($objDemo->email)->send(new SuccessfulRegistration($objDemo));

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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}