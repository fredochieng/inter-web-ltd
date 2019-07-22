<?php

namespace App\Model;

use DB;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    public static function getPayments()
    {
        $data['payments'] = DB::table('payments')
            ->select(
                DB::raw('payments.*'),
                DB::raw('payments.created_at AS payment_date'),
                DB::raw('payment_methods.*'),
                DB::raw('accounts.*'),
                DB::raw('client_payment_modes.*'),
                DB::raw('banks.*'),
                DB::raw('users.*'),
                DB::raw('users_details.*')
            )
            ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
            ->leftJoin('client_payment_modes', 'payments.payment_mode_info_id', '=', 'client_payment_modes.pay_id')
            ->leftJoin('payment_methods', 'client_payment_modes.pay_mode_id', '=', 'payment_methods.method_id')
            ->leftJoin('banks', 'client_payment_modes.pay_bank_id', '=', 'banks.bank_id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->where('payments.payment_amount', '>', 0)
            ->orderBy('payments.payment_id', 'desc')->get();
        return $data['payments'];
    }

    public static function sumTotalPayments()
    {
        $data['sum_tot_payments'] = number_format(DB::table('payments')->sum('payment_amount'), 2, '.', ',');
        return $data['sum_tot_payments'];
    }
}