<?php

namespace App\Model;

use DB;

use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    protected $table = 'topups';
    public static function getInvestments($inv_id)
    {
        $data['investments'] = DB::table('investments')
            ->select(
                DB::raw('investments.*'),
                DB::raw('investments.initiated_by'),
                DB::raw('accounts.*'),
                DB::raw('payment_schedule.*'),
                DB::raw('users.*'),
                DB::raw('inv_types.*'),
                DB::raw('inv_modes.*'),
                DB::raw('banks.*'),
                DB::raw('users.*'),
                DB::raw('user_pay_modes.*')

            )
            ->leftJoin('accounts', 'investments.account_no_id', '=', 'accounts.id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            ->leftJoin('inv_types', 'investments.inv_type_id', '=', 'inv_types.inv_id')
            ->leftJoin('inv_modes', 'investments.inv_mode_id', '=', 'inv_modes.id')
            // FIND A WAY OF JOINING WITH INV_BANK_CHEQ_ID TOO TO GET THE BANK CHEQUE NAME
            ->leftJoin('banks', 'investments.inv_bank_id', '=', 'banks.bank_id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->leftJoin('user_pay_modes', 'users.id', '=', 'user_pay_modes.user_id')
            ->where('investments.investment_id', '=', $inv_id)
            ->orderBy('investments.investment_id', 'asc')->first();
        return $data['investments'];
    }

    public static function totalTopups()
    {
        $data['total_topups'] = number_format(DB::table('topups')->sum('topup_amount'), 2, '.', ',');
        return $data['total_topups'];
    }
}