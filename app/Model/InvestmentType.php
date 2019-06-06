<?php

namespace App\Model;
use Db;

use Illuminate\Database\Eloquent\Model;

class InvestmentType extends Model
{
    protected $table = 'inv_types';
    public static function getInvTypes(){
        $data['inv_types'] = DB::table('inv_types')->orderBy('inv_id', 'asc')->get();
        return $data['inv_types'];
    }
}
