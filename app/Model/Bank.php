<?php

namespace App\Model;
use DB;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks';
    public static function getBanks(){
        $data['banks'] = DB::table('banks')->orderBy('bank_id', 'asc')->get();
        return $data['banks'];
    }
}
