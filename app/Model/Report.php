<?php

namespace App\Model;
use DB;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public static function customerReport(){
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
}
