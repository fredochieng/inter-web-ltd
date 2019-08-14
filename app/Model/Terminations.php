<?php

namespace App\Model;

use DB;
use Illuminate\Database\Eloquent\Model;

class Terminations extends Model
{
    protected $table = 'terminations';

    public static function getTerminations()
    {
        $terminations = DB::table('terminations')
            ->select(
                DB::raw('terminations.*'),
                DB::raw('terminations.created_at as termination_date'),
                DB::raw('users.*'),
                DB::raw('accounts.*')
            )
            ->leftJoin('users', 'terminations.user_id', '=', 'users.id')
            ->leftJoin('accounts', 'users.id', '=', 'accounts.user_id')
            ->get();

        return $terminations;
    }
}