<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = DB::table('users')
            ->where('is_active', '=', 1)->get();
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
            , 'document_types' => 'nullable'
            , 'categories' => 'nullable'
            , 'username' => 'required|string'
            , 'password' => 'required|string|confirmed'
            , 'is_active' => 'required',
        ]);

        $duplicate_users = DB::table('users')
            ->where('first_name', $fields['first_name'])
            ->where('middle_name', $fields['middle_name'])
            ->where('last_name', $fields['last_name'])
            ->where('suffix', $fields['suffix'])
            ->orWhere(function ($query) {
                $query->where('id_prefix', $fields['id_prefix'])
                    ->where('id_no', $fields['id_no']);
            })
            ->get();

        if($duplicate_users->count())
        {
            throw ValidationException::withMessages([
                ''
            ]);
        }

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
        $specific_user->suffix = $request->get('suffix');

        $specific_user->department = $request->get('department');
        $specific_user->position = $request->get('position');
        $specific_user->permissions = $request->get('permissions');
        $specific_user->document_types = $request->get('document_types');
        $specific_user->categories = $request->get('categories');
        $specific_user->username = $request->get('username');
        $specific_user->password = $request->get('password');
        $specific_user->is_active = $request->get('is_active');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $specific_user = User::find($id);
        $specific_user->destroy();

        return "Succesfully Deleted";

    }

    #_________________________SPECIAL CASE__________________________________
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function archive(Request $request, $id)
    {
        $specific_user = User::find($id);
        $specific_user->is_active = 0;

    }
}
