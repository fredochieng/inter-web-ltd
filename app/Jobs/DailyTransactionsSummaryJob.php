<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use DB;

class DailyTransactionsSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    //private $date;

    public function __construct()
    { }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $investments = DB::table('investments')
            ->select(
                DB::raw('investments.inv_status_id'),
                DB::raw('investments.inv_date'),
                DB::raw('investments.investment_amount'),
                DB::raw('sum(investment_amount) as tot_investment_amount'),
                DB::raw('topups.topped_at'),
                DB::raw('topups.topup_amount'),
                DB::raw('sum(topup_amount) as tot_topup_amount'),
                DB::raw('payments.user_pay_date'),
                DB::raw('payments.payment_amount'),
                DB::raw('sum(payment_amount) as tot_payment_amount'),
                DB::raw('terminations.ter_date'),
                DB::raw('terminations.amount_ter'),
                DB::raw('sum(amount_ter) as tot_ter_amount'),
            )
            ->LEFTJOIN('topups', 'investments.inv_date', '=', 'topups.topped_at')
            ->LEFTJOIN('payments', 'investments.inv_date', '=', 'payments.user_pay_date')
            ->LEFTJOIN('terminations', 'investments.inv_date', '=', 'terminations.ter_date')
            ->groupBy('investments.inv_date', 'topups.topped_at', 'payments.user_pay_date', 'terminations.ter_date');

        $investments1 = DB::table('investments')
            ->select(
                DB::raw('investments.inv_status_id'),
                DB::raw('investments.inv_date'),
                DB::raw('investments.investment_amount'),
                DB::raw('sum(investment_amount) as tot_investment_amount'),
                DB::raw('topups.topped_at'),
                DB::raw('topups.topup_amount'),
                DB::raw('sum(topup_amount) as tot_topup_amount'),
                DB::raw('payments.user_pay_date'),
                DB::raw('payments.payment_amount'),
                DB::raw('sum(payment_amount) as tot_payment_amount'),
                DB::raw('terminations.ter_date'),
                DB::raw('terminations.amount_ter'),
                DB::raw('sum(amount_ter) as tot_ter_amount'),
            )
            ->RIGHTJOIN('topups', 'investments.inv_date', '=', 'topups.topped_at')
            ->RIGHTJOIN('payments', 'investments.inv_date', '=', 'payments.user_pay_date')
            ->RIGHTJOIN('terminations', 'investments.inv_date', '=', 'terminations.ter_date')
            ->groupBy('investments.inv_date', 'topups.topped_at', 'payments.user_pay_date', 'terminations.ter_date')
            ->orderBy('topups.topped_at')
            ->unionAll($investments)
            ->get();

        $investments1 = (array) $investments1;
        $investments1 = json_encode($investments1);
        $investments1 = json_decode($investments1, true);

        foreach ($investments1 as $key => $row) { }

        $final_inv = array_unique($row, SORT_REGULAR);

        foreach ($final_inv as $key => $value) {
            $inv_date = $value['inv_date'];
            $topup_date = $value['topped_at'];
            $pay_date = $value['user_pay_date'];
            $ter_date = $value['ter_date'];

            $tot_investments = $value['tot_investment_amount'];
            $tot_topups = $value['tot_topup_amount'];
            $tot_payments = $value['tot_payment_amount'];
            $tot_terminations = $value['tot_ter_amount'];

            if (empty($inv_date)) {
                //  echo "FRed";
                $date = $ter_date or $topup_date or $pay_date;
            } else {
                $date = $inv_date;
            }

            if (empty($tot_investments)) {
                $tot_investments = 0;
            } else {
                $tot_investments = $tot_investments;
            }

            if (empty($tot_topups)) {
                $tot_topups = 0;
            } else {
                $tot_topups = $tot_topups;
            }

            if (empty($tot_payments)) {
                $tot_payments = 0;
            } else {
                $tot_payments = $tot_payments;
            }

            if (empty($tot_terminations)) {
                $tot_terminations = 0;
            } else {
                $tot_terminations = $tot_terminations;
            }



            DB::table('transactions_summary')->upsert(
                [
                    'date' => $date, 'tot_investments' => $tot_topups, 'tot_topups' => $tot_topups,
                    'tot_payments' => $tot_payments, 'tot_terminations' => $tot_terminations
                ],
                ['date'],
                ['tot_investments', 'tot_topups', 'tot_payments', 'tot_terminations', 'updated_at']
            );
        }
    }
}