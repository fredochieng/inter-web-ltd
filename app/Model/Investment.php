<?php

namespace App\Model;

use App\User;
use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $table = 'investments';

    public static function getInvestments()
    {
        $data['investments'] = DB::table('investments')
            ->select(
                DB::raw('investments.*'),
                DB::raw('investments.initiated_by'),
                DB::raw('accounts.*'),
                DB::raw('accounts.id as acc_id'),
                DB::raw('payment_schedule.*'),
                DB::raw('users.*'),
                DB::raw('inv_types.*'),
                DB::raw('inv_modes.*'),
                DB::raw('banks.*'),
                DB::raw('users.*'),
                DB::raw('users_details.*'),

            )
            ->leftJoin('accounts', 'investments.account_no_id', '=', 'accounts.id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            ->leftJoin('inv_types', 'investments.inv_type_id', '=', 'inv_types.inv_id')
            ->leftJoin('inv_modes', 'investments.inv_mode_id', '=', 'inv_modes.id')
            // FIND A WAY OF JOINING WITH INV_BANK_CHEQ_ID TOO TO GET THE BANK CHEQUE NAME
            ->leftJoin('banks', 'investments.inv_bank_id', '=', 'banks.bank_id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->orderBy('investments.investment_id', 'asc')->get();
        return $data['investments'];
    }

    public static function totalInvestments()
    {
        $data['total_investments'] = number_format(DB::table('investments')->where('investments.inv_status_id', '=', 1)->sum('investment_amount'), 2, '.', ',');
        return $data['total_investments'];
    }

    public static function totalMonthlyInvestments()
    {
        $data['total_monthly_investments'] = DB::table('investments')
            ->select(
                DB::raw('sum(investment_amount) as tot_monthly_inv')
            )->where('investments.inv_type_id', '=', 1)
            ->where('investments.inv_status_id', '=', 1)
            ->first();

        return $data['total_monthly_investments'];
    }
    public static function totalCompoundedInvestments()
    {
        $data['total_compounded_investments'] = DB::table('investments')
            ->select(
                DB::raw('sum(investment_amount) as tot_comp_inv')
            )->where('investments.inv_type_id', '=', 2)
            ->where('investments.inv_status_id', '=', 1)
            ->first();

        return $data['total_compounded_investments'];
    }
    public static function totalMonthlyAndCompoundedInvestments()
    {
        $data['total_monthly_comp_investments'] = DB::table('investments')
            ->select(
                DB::raw('sum(investment_amount) as tot_monthly_comp_inv')
            )->where('investments.inv_type_id', '=', 3)
            ->where('investments.inv_status_id', '=', 1)
            ->first();

        return $data['total_monthly_comp_investments'];
    }

    public static function sumTotalPayable()
    {
        $data['sum_tot_payable'] = number_format(DB::table('payment_schedule')->sum('tot_payable_amnt'), 2, '.', ',');
        return $data['sum_tot_payable'];
    }

    // public static function totalPayout(){
    //     $data['sum_total_payout'] = number_format(DB::table('payment_schedule')->sum('tot_payable_amnt'), 2, '.', ',');
    //     return $data['sum_total_payout'];
    // }


    // /// Remove this
    // public static function totalPayments(){
    //     $data['total_payments'] = number_format(DB::table('investments')->sum('investment_amount'), 2, '.', ',');
    //     return $data['total_payments'];
    // }

    public static function todayTotalInvestments()
    {
        $today = Carbon::now()->toDateString();
        $data['today_total_investments'] = number_format(DB::table('investments')->where('investments.created_at', '=', $today)->sum('investment_amount'), 2, '.', ',');

        return $data['today_total_investments'];
    }
}