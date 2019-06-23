<?php

namespace App\Model;

use DB;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public static function customerReport()
    {
        $data['records'] = DB::table('investments')
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
            ->get();

        return $data['records'];
    }
    public static function customerDuePaymentsReport()
    {
        $data['user_due_payments'] = DB::table('accounts')
            ->select(
                DB::raw('sum(total_due_payments) as user_due_payments'),
                DB::raw('users.*')
            )
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->groupBy('accounts.id')
            ->get();
        return $data['user_due_payments'];
    }

    public static function duePaymentsReport()
    {
        // FETCH CLIENTS DETAILS
        $data['due_payments_report'] = DB::table('users')
            ->select(
                DB::raw('users.*'),
                DB::raw('users.id AS client_id'),
                DB::raw('users_details.*'),
                DB::raw('accounts.*'),
                DB::raw('investments.*'),
                DB::raw('user_pay_modes.*'),
                DB::raw('inv_types.*'),
                DB::raw('payment_schedule.*'),
                DB::raw('payment_schedule.monthly_amount'),
                //DB::raw('payments.account_no_id'),
                DB::raw('payment_methods.*'),
                DB::raw('banks.*'),
                DB::raw('model_has_roles.*')
            )
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
            ->leftJoin('investments', 'accounts.id', '=', 'investments.account_no_id')
            ->leftJoin('inv_types', 'investments.inv_type_id', '=', 'inv_types.inv_id')
            ->leftJoin('user_pay_modes', 'users.id', '=', 'user_pay_modes.user_id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            // ->leftJoin('payments', 'accounts.id', '=', 'payments.account_no_id')
            ->leftJoin('payment_methods', 'user_pay_modes.pay_mode_id', '=', 'payment_methods.method_id')
            ->leftJoin('banks', 'user_pay_modes.pay_bank_id', '=', 'banks.bank_id')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', '=', 3)
            ->get();

        return $data['due_payments_report'];
    }
}