<?php

namespace App\Http\Controllers;

use App\Model\Blacklist;
use Illuminate\Http\Request;

class BlacklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['blacklists'] = Blacklist::getBlacklists();

        return view('blacklists.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $blacklist = new Blacklist();
        $blacklist->id_no = $request->input('id_no');
        $blacklist->phone = $request->input('phone');
        $blacklist->save();

        toast('New blacklist added successfully', 'success', 'top-right');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Blacklist  $blacklist
     * @return \Illuminate\Http\Response
     */
    public function show(Blacklist $blacklist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Blacklist  $blacklist
     * @return \Illuminate\Http\Response
     */
    public function edit(Blacklist $blacklist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Blacklist  $blacklist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blacklist $blacklist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Blacklist  $blacklist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blacklist $blacklist)
    {
        //
    }
}