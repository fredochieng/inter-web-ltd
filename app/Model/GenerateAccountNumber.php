<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GenerateAccountNumber extends Model
{
    public static function generateAccountNumber(){
        $data['generated_account_no'] = str_pad(mt_rand(0, 999999), 8, STR_PAD_LEFT);
        return $data['generated_account_no'];
    }
}
