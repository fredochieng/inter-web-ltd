<?php

namespace App\Model;

use DB;

use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    protected $table = 'topups';
    public static function getTopups()
    {
        $data['topups'] = DB::table('topups')
            ->select(

                DB::raw('topups.*'),
                DB::raw('topups.created_at as topped_date'),
                DB::raw('accounts.*'),
                DB::raw('inv_modes.*'),
                DB::raw('banks.*'),
                DB::raw('users.*'),
                DB::raw('users_details.*')
            )
            ->leftJoin('accounts', 'topups.account_id', '=', 'accounts.id')
            ->leftJoin('inv_modes', 'topups.inv_mode_id', '=', 'inv_modes.id')
            ->leftJoin('banks', 'topups.inv_bank_id', '=', 'banks.bank_id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->leftJoin('users_details', 'users.id', '=', 'users_details.user_id')
            ->get();

        $data['topups']->map(function ($item) {

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

        return $data['topups'];
    }
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