<?php

namespace App\Model;
use DB;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';

    public static function getPaymentMethods(){
        $data['payment_methods'] = DB::table('payment_methods')->get();
        return $data['payment_methods'];
    }
}
