<?php

namespace App\Model;

use DB;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;

use Illuminate\Database\Eloquent\Model;

class TransactionsSummary extends Model
{
    protected $table = 'daily_trans_summary';

    public static function getTransSumamry()
    {
        $today = Carbon::now('Africa/Nairobi')->toDateString();
        $start = Carbon::now('Africa/Nairobi')->subDays(6)->toDateString();

        $start_date = new DateTime($start);
        $end_date   = new DateTime($today);

        $dates = array();
        for ($i = $start_date; $i <= $end_date; $i->modify('+1 day')) {

            $dates[] = $i->format("Y-m-d");
        }

        $trans_summary = DB::table('daily_trans_summary')
            ->whereIn('date', $dates)
            ->orderBy('date', 'asc')->get();
        return $trans_summary;
    }
}