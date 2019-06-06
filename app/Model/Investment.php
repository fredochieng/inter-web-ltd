<?php

namespace App\Model;
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
                DB::raw('accounts.*'),
                DB::raw('payment_schedule.*'),
                DB::raw('users.*')
            )
            ->leftJoin('accounts', 'investments.account_no_id', '=', 'accounts.id')
            ->leftJoin('payment_schedule', 'accounts.id', '=', 'payment_schedule.account_no_id')
            ->leftJoin('users', 'accounts.user_id', '=', 'users.id')
            ->orderBy('investments.investment_id', 'asc')->get();
        return $data['investments'];
    }

    public static function totalInvestments(){
        $data['total_investments'] = number_format(DB::table('investments')->sum('investment_amount'), 2, '.', ',');
        return $data['total_investments'];
    }

    public static function sumPayout(){
        $data['sum_payout'] = number_format(DB::table('investments')->sum('payout'), 2, '.', ',');
        return $data['sum_payout'];
    }

    public static function totalPayout(){
        $data['sum_total_payout'] = number_format(DB::table('accounts')->sum('total_due_payments'), 2, '.', ',');
        return $data['sum_total_payout'];
    }

    public static function totalPayments(){
        $data['total_payments'] = number_format(DB::table('payments')->sum('payment_amount'), 2, '.', ',');
        return $data['total_payments'];
    }

    public static function todayTotalInvestments(){
        $today = Carbon::now()->toDateString();
        $data['today_total_investments'] = number_format(DB::table('investments')->where('investments.created_at', '=', $today)->sum('investment_amount'), 2, '.', ',');

            return $data['today_total_investments'];
    }
}
