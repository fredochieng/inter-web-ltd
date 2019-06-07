<?php

namespace App\Model;
use DB;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

  public static function getAccounts(){
        $data['accounts'] = DB::table('accounts')->get();
        return $data['accounts'];
  }

  public static function sumTotalDuePayments(){
    $data['sum_tot_due_payments'] = number_format(DB::table('accounts')->sum('total_due_payments'), 2, '.', ',');
        return $data['sum_tot_due_payments'];
}
}
