<?php

namespace App\Model;

use DB;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    public static function nextAccountNumber()
    {
        $last_account = Account::orderBy('account_no', 'desc')->first();
        if (!$last_account) {
            $next_account = 900000;
        } else {
            $last_account = $last_account->account_no;
            $next_account = substr($last_account, -6);
        }
        $next_account = sprintf('%06d', intval($next_account) + 1);

        return $next_account;
    }
    public static function getAccounts()
    {
        $data['accounts'] = DB::table('accounts')->get();
        return $data['accounts'];
    }

    public static function sumTotalDuePayments()
    {
        $data['sum_tot_due_payments'] =  DB::table('accounts')
            ->select(
                DB::raw('sum(total_due_payments) as sum_tot_due_payments'),
                DB::raw('investments.account_no_id')
            )->leftJoin('investments', 'accounts.id', '=', 'investments.account_no_id')
            ->where('investments.inv_status_id', '=', 1)
            ->first();

        $data['sum_tot_due_payments'] = $data['sum_tot_due_payments']->sum_tot_due_payments;
        if ($data['sum_tot_due_payments'] == '') {
            $data['sum_tot_due_payments'] = 0.00;
        } else {
            $data['sum_tot_due_payments'] = number_format($data['sum_tot_due_payments'], 2, '.', ',');
        }

        return $data['sum_tot_due_payments'];
    }
    // public static function sumTotalDuePayments()
    // {
    //     $data['sum_tot_due_payments'] = number_format(
    //         DB::table('accounts')
    //             ->sum('total_due_payments'),
    //         2,
    //         '.',
    //         ','
    //     );
    //     return $data['sum_tot_due_payments'];
    // }
}