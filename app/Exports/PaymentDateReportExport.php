<?php

namespace App\Exports;

use App\Model\Payment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PaymentDateReportExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view('payments.index', [
            'payments' => Payment::orderBy('payment_id', 'desc')->take(100)->get()
        ]);
    }
}