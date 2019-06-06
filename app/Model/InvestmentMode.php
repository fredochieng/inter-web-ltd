<?php

namespace App\Model;
use DB;

use Illuminate\Database\Eloquent\Model;

class InvestmentMode extends Model
{
    protected $table ='inv_modes';
    public static function getInvModes(){
        $data['inv_modes'] = DB::table('inv_modes')->orderBy('id', 'asc')->get();
        return $data['inv_modes'];
    }
}
