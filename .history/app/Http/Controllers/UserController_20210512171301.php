<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
            , 'username' => 'required|string'
            , 'password' => 'required|string|confirmed'
            , 'is_active' => 'required',
        ]);

        $duplicate_id = DB::table('users')
            ->where('id_prefix', $fields['id_prefix'])
            ->where('id_no', $fields['id_no'])
            ->get();

        $duplicate_name = DB::table('users')
            ->where('first_name', $fields['first_name'])
            ->where('middle_name', $fields['middle_name'])
            ->where('last_name', $fields['last_name'])
            ->where('suffix', $fields['suffix'])
            ->get();

        $duplicate_username = DB::table('users')
            ->where('username', $fields['username'])
            ->get();

        if ($duplicate_id->count()) {
            throw ValidationException::withMessages([
                'error_message' => ['ID already registered'],
            ]);
        }

        if ($duplicate_name->count()) {
            throw ValidationException::withMessages([
                'error_message' => ['Name already registered'],
            ]);
        }

        if ($duplicate_username->count()) {
            throw ValidationException::withMessages([
                'error_message' => ['Username already registered'],
            ]);
        }
        $created_document_categories = collect();
        $document_categories = $fields['document_types'];
        foreach ($document_categories as $specific_document_categories) {

            $created_document_categories->push([
                'document_id' => $specific_document_categories[0]
                , 'categpry_id' => $specific_document_categories[1],
            ]);
            foreach ($created_document_categories as $specific_doc_categories) {
                if (array_key_exists('document_id', $specific_doc_categories)) {
                    
                }

            }
        }

        print_r($created_document_categories);

        // $new_user = User::create([
        //     'id_prefix' => $fields['id_prefix']
        //     , 'id_no' => $fields['id_no']
        //     , 'role' => $fields['role']
        //     , 'first_name' => $fields['first_name']
        //     , 'middle_name' => $fields['middle_name']
        //     , 'last_name' => $fields['last_name']
        //     , 'suffix' => $fields['suffix']
        //     , 'department' => $fields['department']
        //     , 'position' => $fields['position']
        //     , 'permissions' => $fields['permissions']
        //     , 'document_types' => $fields['document_types']
        //     , 'username' => $fields['username']
        //     , 'password' => bcrypt($fields['password'])

        //     , 'is_active' => $fields['is_active'],
        // ]);

        // $response = [
        //     "user_details" => $new_user,
        // ];

        // return response($response, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $specific_user = User::find($id);

        // return $specific_user;
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

        $duplicate_username = DB::table('users')
            ->where('username', $request['username'])
            ->get();

        $duplicate_name = DB::table('users')
            ->where('first_name', $request['first_name'])
            ->where('middle_name', $request['middle_name'])
            ->where('last_name', $request['last_name'])
            ->where('suffix', $request['suffix'])
            ->get();

        $duplicate_id_details = DB::table('users')
            ->where('id_prefix', $request['id_prefix'])
            ->where('id_no', $request['id_no'])
            ->get();

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
        $specific_user->username = $request->get('username');
        $specific_user->password = $request->get('password');
        $specific_user->is_active = $request->get('is_active');
        $specific_user->save();

        return "Succesfully Updated!";

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

    #_________________________SPECIAL CASE________________________________
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
        $specific_user->save();

        return "Succesfully Archived!";

    }

    public function search(Request $request)
    {
        $value = $request['value'];

        $result = User::where('id_prefix', 'like', '%' . $value . '%')
            ->orWhere('id_no', 'like', '%' . $value . '%')
            ->orWhere('first_name', 'like', '%' . $value . '%')
            ->orWhere('middle_name', 'like', '%' . $value . '%')
            ->orWhere('last_name', 'like', '%' . $value . '%')
            ->orWhere('suffix', 'like', '%' . $value . '%')
            ->orWhere('department', 'like', '%' . $value . '%')
            ->orWhere('position', 'like', '%' . $value . '%')
            ->orWhere('permissions', 'like', '%' . $value . '%')
            ->orWhere('document_types', 'like', '%' . $value . '%')
            ->orWhere('username', 'like', '%' . $value . '%')
            ->orWhere('is_active', 'like', '%' . $value . '%')
            ->get();

        return response()->json([
            'search_result' => $result,
        ]);

        // return $result;
    }

    /**
     * Update the User Password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function change_password(Request $request, $id)
    {
        $specific_user = User::find($id);
        $specific_user->password = bcrypt($request['password']);
        $specific_user->save();

        return "Password Changed Succesfully!";

    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check username
        $user = User::where('username', $fields['username'])->first();

        // Check Password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad Creds',
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken; //Get Token
        $response = [
            'user' => $user,
            'token' => $token,
        ];

        $cookie = \cookie('sanctum', $token, 3600);

        return response($response, 201)->withCookie($cookie);

    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out',
        ];
    }

}
