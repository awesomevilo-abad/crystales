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

        if (!$users) {
            return "No Data Found";
        }

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

        // ___________DOCUMENT CATEGORY TRANSFORMATION___________________

        $created_document_categories = [];
        $document = [];
        $document_types_transformed = [];

        $document_categories = $fields['document_types'];

        foreach ($document_categories as $specific_document_categories) {
            $document_ids = array_unique(array_column($document_categories, "document_id"));
        }
        foreach ($document_ids as $specific_doc_id) {
            $categories = [];

            foreach ($document_categories as $doc_category_id) {
                if ($specific_doc_id == $doc_category_id["document_id"]) {
                    array_push($categories, $doc_category_id["category_id"]);
                }
            }
            array_push($created_document_categories, array("document_id" => $specific_doc_id, "categories" => $categories));
            $fields['document_types'] = $created_document_categories;
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
            , 'username' => $fields['username']
            , 'password' => bcrypt($fields['password'])

            , 'is_active' => $fields['is_active'],
        ]);

        $response = [
            "user_details" => $new_user,
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
        // $result = User::find($id);
        $result = DB::table('users')
            ->where('id', $id)
            ->get();

        if (!$result) {
            return "No Data Found";
        }

        // $document_category_details = $result->document_types[0];
        // return $document_category_details["categories"];

        $document_types = $result[0]->document_types;
        $json_document_types = json_decode($document_types, true);
        print_r($ss);
        // foreach ($document_types as $document_type_details) {
        //     $categories = $document_type_details['categories'];
        //     echo 'SAVED CAT';
        //     // print_r($categories); //saved categories

        //     $category_masterlist = DB::table('categories')
        //         ->get();

        //     foreach ($category_masterlist as $specific_category) {
        //         $specific_category_id = $specific_category->id; //Masterlist All Categories

        //         foreach ($categories as $specific_category) {

        //             if ($categories) {

        //             }
        //             echo ($specific_category) . '-';

        //         }

        //     }
        // }

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

        if (!$specific_user) {
            return "No Data Found";
        }

        // ___________DOCUMENT CATEGORY TRANSFORMATION___________________

        $created_document_categories = [];
        $document = [];
        $document_types_transformed = [];

        $document_categories = $request['document_types'];

        foreach ($document_categories as $specific_document_categories) {
            $document_ids = array_unique(array_column($document_categories, "document_id"));
        }
        foreach ($document_ids as $specific_doc_id) {
            $categories = [];

            foreach ($document_categories as $doc_category_id) {
                if ($specific_doc_id == $doc_category_id["document_id"]) {
                    array_push($categories, $doc_category_id["category_id"]);
                }
            }
            array_push($created_document_categories, array("document_id" => $specific_doc_id, "categories" => $categories));
            $request['document_types'] = $created_document_categories;
        }

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

        if (!$specific_user) {
            return "No Data Found";
        }
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

        if (!$specific_user) {
            return "No Data Found";
        }

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

        if ($result->isEmpty()) {
            return "No Data Found";
        }

        return response()->json([
            'search_result' => $result,
        ]);

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
        $fields = $request->validate([
            'password' => 'required|confirmed',
            'old_password' => 'required',
        ]);
        $specific_user = User::find($id);

        if (!$specific_user) {
            return "No Data Found";
        }
        $get_old_password = DB::table('users')
            ->select('password')
            ->where('id', $id)
            ->get();

        // Check Password
        $old_password = $get_old_password[0]->password;
        if (Hash::check($fields['old_password'], $old_password)) {

            $specific_user->password = bcrypt($fields['password']);
            $specific_user->save();

            return "Password Changed Succesfully!";

        } else {
            return "No Data Found";
        }

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
                'message' => 'Invalid Username or Password',
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
