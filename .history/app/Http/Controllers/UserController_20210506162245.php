<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $users;
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
        //Store User
        $fields = $request->validate([
            'id_prefix' => 'string|required'
            , 'id_no' => 'required'
            , 'role' => 'required|string'
            , 'first_name' => 'required|string'
            , 'middle_name' => 'required|string'
            , 'last_name' => 'required|string'
            , 'suffix' => 'nullable'
            , 'department' => 'required|string'
            , 'position' => 'required|string'
            , 'permissions' => 'required'
            , 'document_types' => 'required'
            , 'categories' => 'nullable'
            , 'username' => 'required|string'
            , 'password' => 'required|string|confirmed'
            , 'is_active' => 'required',
        ]);

        $new_user = User::create([
            'id_prefix' => $fields['id_prefix']
            , 'id_no' => $fields['id_no']
            , 'role' => $fields['role']
            , 'first_name' => $fields['first_name']
            , 'middle_name' => $fields['middle_name']
            , 'last_name' => $fields['last_name']
            , 'suffix' => $fields['suffix']
            , 'department' => $fields['department']
            , 'position' => $fields['position']
            , 'permissions' => $fields['permissions']
            , 'document_types' => $fields['document_types']
            , 'categories' => $fields['categories']
            , 'username' => $fields['username']
            , 'password' => $fields['password']
            , 'is_active' => $fields['is_active'],
        ]);

        $UserToken = $new_user->createToken('newusertoken')->plainTextToken;
        $response = [
            "user_details" => $new_user
            , "token" => $UserToken,
        ];

        return response($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $specific_user = User::find($id);
        return $specific_user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $specific_user = User::find($id);
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
        $specific_user = User::find($id);

        $specific_user->id_prefix = $request->get('id_prefix');
        $specific_user->id_no = $request->get('id_no');
        $specific_user->role = $request->get('role');
        $specific_user->first_name = $request->get('first_name');
        $specific_user->middle_name = $request->get('middle_name');
        $specific_user->last_name = $request->get('last_name');
        $specific_user->suffix=$request->get('suffix');

        $specific_user->department = $request->get('department');
        $specific_user->position = $request->get('position');
        $specific_user->role = $request->get('role');
        $specific_user->first_name = $request->get('first_name');
        $specific_user->middle_name = $request->get('middle_name');
        $specific_user->last_name = $request->get('last_name');
        $specific_user->suffix=$request->get('suffix');


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
