<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\User;
// use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromView
{
    public function view(): View
    {
        return view('users.index', [
            'users' => User::all()
        ]);
    }
}