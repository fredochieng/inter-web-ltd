<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use DB;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Log;

class TransJob
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date = null)
    {
        if ($date == null) {
            $date = Carbon::now();
            // $date = Carbon::parse('2019-01-01');
        }
        $this->date = $date;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $today = $this->date->toDateString();
        //$start_date = '2018-01-01';
        $start_date = $this->date->startOfMonth()->toDateString();
        $end_date = $this->date->endOfMonth()->toDateString();

        $start_date = new DateTime($start_date);
        $end_date   = new DateTime($end_date);

        $dates = array();
        for ($i = $start_date; $i <= $end_date; $i->modify('+1 day')) {

            $dates[] = $i->format("Y-m-d");
        }

        foreach ($dates as $value) {

            DB::table('daily_trans_summary')->upsert(
                [
                    'date' => $value
                ],
                ['date']
            );
        }


        // Log::info("Today is: ->" . $today);
    }
}