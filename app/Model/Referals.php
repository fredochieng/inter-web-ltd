<?php

namespace App\Model;

use DB;

use Illuminate\Database\Eloquent\Model;

class Referals extends Model
{
    protected $table = 'referal_restrictions';

    public static function getRestrictedClients()
    {
        $restricted_clients = DB::table('referal_restrictions')
            ->select(
                DB::raw('referal_restrictions.*'),
                DB::raw('users.*'),
                DB::raw('users_details.*'),
                DB::raw('accounts.*')

            )
            ->leftJoin('users_details', 'referal_restrictions.id_no', '=', 'users_details.id_no')
            ->leftJoin('users', 'users_details.user_id', '=', 'users.id')
            ->leftJoin('accounts', 'users.id', 'accounts.user_id')
            ->orderBy('referal_restrictions.rest_id', 'desc')->get();
        return $restricted_clients;
    }
}