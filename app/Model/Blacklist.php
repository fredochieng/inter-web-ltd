<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;

class Blacklist extends Model
{
    protected $table = 'blacklist';
    public static function getBlacklists()
    {
        $blacklists = DB::table('blacklist')
            ->select(
                DB::raw('blacklist.*')
            )->get();
        return $blacklists;
    }
}