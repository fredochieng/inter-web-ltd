<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use DB;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SecretaryRegistration;

class SecretaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('secretaries.manage')) {
            abort(401, 'Unauthorized action.');
        }
        $data['secretaries'] = User::getSecretaries();
        return view('users.secretaries.index')->with($data);
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
        $user = new User();
        $user->name = strtoupper($request->input('name'));
        $user->email = $request->input('email');
        $password = strtolower(str_random(8));;
        $user->password = Hash::make($password);

        $user->save();
        DB::beginTransaction();

        $saved_user_id = $user->id;

        $sec_details_data = array(
            'user_id' => $saved_user_id

        );
        $save_sec_details_data = DB::table('users_details')->insertGetId($sec_details_data);

        //SAVE SECROLE DATA
        $role_id = 2;
        $sec_role_data = array(
            'role_id' => $role_id,
            'model_id' => $saved_user_id
        );

        $save_user_role_data = DB::table('model_has_roles')->insert($sec_role_data);
        DB::commit();

        // Send welcome mail to the secretary containg the password

        $objDemo = new \stdClass();
        $objDemo->subject = 'Successful Registration';
        $company = "Inter-Web Global Fortune Limited";
        $objDemo->company = $company;

        //1. Send to the to the sec
        $message = "You have been added as a secretary at Interweb Global Fortune Limited";
        $objDemo->name = $user->name;
        $objDemo->email = $user->email;
        $objDemo->message = $message;
        $objDemo->password = $password;

        Mail::to($user->email)->send(new SecretaryRegistration($objDemo));

        toast('New secretary added successfully', 'success', 'top-right');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $user->name = strtoupper($request->input('name'));
        // $user->email = $request->input('email');
        DB::beginTransaction();
        $user->save();

        DB::commit();
        toast('Secreatary details updated successfully', 'success', 'top-right');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('users')->where('id', $id)->delete();
        toast('Secretary deleted successfully', 'success', 'top-right');

        return back();
    }
}