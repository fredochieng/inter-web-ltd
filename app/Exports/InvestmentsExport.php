<?php

namespace App\Exports;

use App\Investment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvestmentsExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view('investments.index', [
            'invetsments' => Investment::all()
        ]);
    }
}