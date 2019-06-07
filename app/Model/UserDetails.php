<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    protected $table = 'users_details';
    public static function get_telephone(){
        $telephone = DB::table('users_details')
                      ->select(
                          DB::raw('users_details>*'),
                          DB::raw('users')
                      )
                      ->leftJoin('users', 'users_datails.user_id', '=', 'users.id')->get();

                      return $telephone;

    }

}
