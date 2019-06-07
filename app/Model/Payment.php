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
                DB::raw('accounts.*'),
                 DB::raw('accounts.*'),
                DB::raw('users.*')
            )
            ->leftJoin('accounts', 'payments.account_no_id', '=', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->orderBy('payments.payment_id', 'desc')->get();
        return $data['payments'];
    }

    public static function sumTotalPayments(){
        $data['sum_tot_payments'] = number_format(DB::table('payments')->sum('payment_amount'), 2, '.', ',');
            return $data['sum_tot_payments'];
    }
}
