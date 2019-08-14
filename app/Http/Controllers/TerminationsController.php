<?php

namespace App\Http\Controllers;

use App\Model\Terminations;

use Illuminate\Http\Request;

class TerminationsController extends Controller
{
    public function index()
    {
        $data['terminations'] = Terminations::getTerminations();
        // dd($data['terminations']);

        return view('terminations.index')->with($data);
    }
}