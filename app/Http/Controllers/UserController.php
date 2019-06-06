<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use App\User;
use App\Model\Report;
use App\Model\UserDetail;
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['active_clients'] = User::getActiveCustomers();
        $data['inactive_clients'] = User::getInactiveCustomers();
        $data['payment_mode'] = PaymentMethod::getPaymentMethods();
        $data['banks'] = Bank::getBanks();
        $data['payment_modes'] = DB::table('payment_methods')->pluck('method_name', 'method_id')->all();
        // $data['inactive_clients']->map(function ($item) {
        //     $created_name = DB::table('users_details')
        //         ->select(
        //             DB::raw('users.name as created_by_name')
        //         )
        //         ->leftJoin('users', 'users_details.created_by', '=', 'users.id')
        //         ->get();
        //     return $item;
        //     foreach ($created_name as $key => $value) {
        //         echo "<pre>";
        //         print_r($value);
        //         exit;
        //     }
        // });
        //  echo "<pre>";
        //        print_r($data['active_clients']);
        //    exit;

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
                DB::raw('users.name as referer_name')
                // DB::raw('users_details.id_ as user_telephone')
                // DB::raw('sum(investment_amount) as user_sum,
                //         sum(payout) as user_payout,
                //         sum(total_payout) as user_total_payout, account_no_id ')
            )
            ->join('users', 'users_details.user_id', '=', 'users.id')
            // ->join('users_details', 'users.id', '=', 'users_details.user_id')
            // ->Join('investments', 'accounts.id', '=', 'investments.account_no_id')
            ->where('telephone', 'like', $search_term)
            // ->groupBy('investments.account_no_id')
            ->get();

        // $user_investments = Report::customerReport();

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
            $user->status = 0;
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

            // SAVE USER UNIQUE ACCOUNT (GENERATED ACCOUNT DATA)
            $users_accounts_data = array(
                'user_id' => $saved_user_id,
                'account_no' => $user->account_no,
            );
            $save_user_account_data = DB::table('accounts')->insertGetId($users_accounts_data);

            if($user->inv_type_id==1){
                // CALCULATE LAST PAYMENT DATE
                $inv_duration =  $request->input('inv_duration');
                $inv_date =  $request->input('inv_date');
                $last_pay_date = Carbon::parse($inv_date)->addMonths($inv_duration);

                // CALCULATE MONTHLY AND TOTAL PAYMENTS FOR MONHTLY INVESTMENT TYPE
                $inv_amount =  $request->input('inv_amount');
                $monthly_pay = 0.2 * $inv_amount;
                $total_pay = $monthly_pay * $inv_duration;

                // GET ALL THE PAYMENT DATES FOR A USER (MONTHLY INVESTMENT TYPE)
                $inv_date = Carbon::parse($inv_date);

                for ($i = 0; $i < $inv_duration; $i++) {
                    $pay_dates[] = $inv_date->addMonths()->format('Y-m-d');
                }
                $pay_dates = json_encode($pay_dates);

                // SAVE USER PREFERED MODE OF PAYMENT
                $user_payment_mode = array(
                    'user_id' => $saved_user_id,
                    'pay_mode_id' => $user->pay_mode_id,
                    'pay_mpesa_no' => $user->pay_mpesa_no,
                    'pay_bank_id' => $user->pay_bank_id,
                    'pay_bank_acc' => $user->pay_bank_acc,
                    'pay_dates' => $pay_dates
                );
                $save_user_payment_data = DB::table('user_pay_modes')->insertGetId($user_payment_mode);

                // SAVE USER PAYMENT SCHEDULE
                $user_payment_schedule = array(
                    'account_no_id' => $save_user_account_data,
                    'tot_payable_amnt' => $total_pay,
                    'monthly_amount' => $monthly_pay
                );

                $save_user_payment_schedule = DB::table('payment_schedule')->insertGetId($user_payment_schedule);
            }elseif($user->inv_type_id == 1){
                //  // CALCULATION OF COMPOND INTEREST
                //  $investment = 60000;
                //  $year = 1;
                //  $rate = 15;
                //  $n = 12;
                //  function interest($investment, $year, $rate, $n){
                //      $accummulated = 0;
                //      if($year > 1){
                //          $accummulated = interest($investment, $year-1, $rate, $n);
                //      }
                //      $accummulated += $investment;
                //      $accummulated = $accummulated * pow(1 + $rate/(100 * $n),$n);
                //     //  return $accummulated;
                //     echo $accummulated;
                //  }
                //  exit;
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
            Alert::success('New Customer', 'Customer added successfully');
            // toast('New Investor', 'Investor added successfully', 'success', 'top-right')->autoClose(5000);
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

            //  $data['pay_dates'] = UserPayMode::where('user_id', $id)->first();

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

        $data['customer_payments'] = DB::table('payments')
            ->select(
                DB::raw('sum(payment_amount) as total_payments_made, account_no_id '),
                DB::raw('accounts.*'),
                DB::raw('users.*')
            )

            ->leftJoin('accounts', 'payments.account_no_id', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->where('users.id', '=', $id)
            ->first();

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
