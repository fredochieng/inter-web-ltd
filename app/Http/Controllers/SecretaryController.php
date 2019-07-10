<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use DB;
use App\User;

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
        $id_no = $request->input('id_no');
        $telephone = $request->input('telephone');
        $password = "1234.abc";
        $user->password = Hash::make($password);

        $user->save();
        DB::beginTransaction();

        $saved_user_id = $user->id;

        $sec_details_data = array(
            'user_id' => $saved_user_id,
            'telephone' =>  $telephone,
            'id_no' =>  $id_no

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
        $id_no = $request->input('id_no');
        $telephone = $request->input('telephone');

        DB::beginTransaction();
        $user->save();

        $updated_user_id = $user->id;

        $sec_data = array(
            'id_no' => $id_no,
            'telephone' => $telephone
        );
        $save_details_data = DB::table('users_details')->where('user_id', $updated_user_id)->update($sec_data);

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
        //
    }
}