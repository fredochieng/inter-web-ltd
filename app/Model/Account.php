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
}
