<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Methods\GenericMethod;

class UserController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::check()) {
            echo Auth::id();
        }

        $is_active = $request->get('is_active');

        if ($is_active == 'active') {
            $users = DB::table('users')
                ->where('is_active', '=', 1)
                ->orderBy('id')
                ->paginate(10);

        } elseif ($is_active == 'inactive') {
            $users = DB::table('users')
                ->where('is_active', '=', 0)
                ->orderBy('id')
                ->paginate(10);

        } else {
            $users = DB::table('users')
                ->orderBy('id')
                ->paginate(10);
        }

        if (!$users || $users->isEmpty()) {
            return [
                'error_message' => 'Data Not Found',
            ];
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

        // // ___________DOCUMENT CATEGORY TRANSFORMATION IN JSON___________________

        $created_document_categories = [];
        $document = [];
        $document_types_transformed = [];

        $document_categories = $fields['document_types'];

        if (empty($document_categories)) {
            // echo "Wala Laman";
        } else {
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


        $user =  User::orderBy('id', 'DESC')->get('id')->first();
        $user_id = $user->id;


        // INSERT THIRD TABLE LOG FOR USER DOCUMENT CATEGORY
        foreach($fields['document_types'] as $specific_document_type){

            if(!isset($specific_document_type['document_id'])){
                $document_id = 0;
            }else{
                $document_id = ($specific_document_type['document_id']);
            }

            if(!isset($specific_document_type['categories'])){
                $categories = 0;
            }else{
                $categories = ($specific_document_type['categories']);
            }

            foreach($categories as $category_id){
                $new_user_document_category = UserDocumentCategory::create([
                    'user_id' =>$user_id,
                    'document_id' =>$document_id,
                    'category_id' =>$category_id,
                    'is_active' => 1,
                ]);
            }
        }

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
        $user_details = User::find($id);

        $document_details = DB::select( DB::raw('SELECT documents.id AS "masterlist_document_id",
        documents.document_type AS "document_name",
        categories.id AS "masterlist_category_id",
        categories.name AS "category_name",
        user_document_category.user_id AS "user_id" ,
        user_document_category.document_id AS "user_document_id",
        user_document_category.category_id AS "user_category_id"
        FROM documents
        LEFT JOIN document_categories
        ON documents.id = document_categories.document_id
        LEFT JOIN categories
        ON document_categories.category_id = categories.id
        LEFT JOIN user_document_category
        ON document_categories.document_id = user_document_category.document_id AND document_categories.category_id = user_document_category.category_id
        LEFT JOIN users
        ON user_document_category.user_id = users.id
        WHERE  documents.is_active = 1
        -- AND document_categories.is_active = 1
        -- AND categories.is_active = 1
        -- AND user_document_category.is_active = 1
        -- AND users.is_active = 1
        ORDER by documents.id,categories.id') );

        $user_document_details = collect();
        $document_types = collect();
        $categories = collect();
        $categories_per_doc = collect();

        foreach($document_details as $specific_document_details){

            if(($specific_document_details->masterlist_document_id == $specific_document_details->user_document_id) AND
                ($specific_document_details->masterlist_category_id == $specific_document_details->user_category_id)){
                    $document_status = true;
                    $category_status = true;

            }else if(($specific_document_details->masterlist_document_id == $specific_document_details->user_document_id) AND
            ($specific_document_details->masterlist_category_id != $specific_document_details->user_category_id)){
                $document_status = true;
                $category_status = false;

            }else if(($specific_document_details->masterlist_document_id != $specific_document_details->user_document_id) AND
            ($specific_document_details->masterlist_category_id != $specific_document_details->user_category_id)){
                $document_status = true;
                $category_status = false;

            }else{
                $document_status = false;
            }

            $categories->push([
                "document_id"=>$specific_document_details->masterlist_document_id,
                "category_id"=>$specific_document_details->masterlist_category_id,
                "category_name"=>$specific_document_details->category_name,
                "category_status"=>$category_status,
            ]);

        }


        $final_document_details = GenericMethod::unique_values_in_array_based_on_key($document_details,'masterlist_document_id');

        foreach($final_document_details as $final_specific_document_details){

            if($final_specific_document_details->masterlist_document_id == $final_specific_document_details->user_document_id){
                $document_status = true;
                foreach($categories as $specific_categories){
                    if($specific_categories['document_id'] == $final_specific_document_details->user_document_id){

                        array_push($categories_per_doc, array(
                            "category_id"=>$specific_categories['category_id'],
                            "category_name"=>$specific_categories['category_name'],
                            "category_status"=>$specific_categories['category_status']),
                        );
                    }else{
                    }
                }

            }else{
                $document_status = false;
                $categories_per_doc = [];
            }

            $document_types->push([
                "document_id"=>$final_specific_document_details->masterlist_document_id,
                "document_name"=>$final_specific_document_details->document_name,
                "document_status"=>$document_status,
                "document_categories"=>$categories_per_doc

            ]);

        }

        $user_document_details->push([
            "id"=> $user_details->id,
            "id_prefix"=> $user_details->id_prefix,
            "id_no"=> $user_details->id_prefix,
            "role"=> $user_details->role,
            "first_name"=> $user_details->first_name,
            "middle_name"=> $user_details->middle_name,
            "last_name"=> $user_details->last_name,
            "suffix"=> $user_details->suffix,
            "department"=> $user_details->department,
            "position"=> $user_details->position,
            "permissions"=> $user_details->permissions,
            "document_types"=> $document_types,
            "username"=> $user_details->username,
            "is_active"=> 1,
        ]);

        $result = $user_document_details;


        if (!$result) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        return $result;
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
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        // ___________DOCUMENT CATEGORY TRANSFORMATION___________________

        $created_document_categories = [];
        $document = [];
        $document_types_transformed = [];

        // $document_categories = $request['document_types'];

        // foreach ($document_categories as $specific_document_categories) {
        //     $document_ids = array_unique(array_column($document_categories, "document_id"));
        // }
        // foreach ($document_ids as $specific_doc_id) {
        //     $categories = [];

        //     foreach ($document_categories as $doc_category_id) {
        //         if ($specific_doc_id == $doc_category_id["document_id"]) {
        //             array_push($categories, $doc_category_id["category_id"]);
        //         }
        //     }
        //     array_push($created_document_categories, array("document_id" => $specific_doc_id, "categories" => $categories));
        //     $request['document_types'] = $created_document_categories;
        // }

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
        $specific_user->save();

        return [
            'success_message' => 'Succesfully Updated!',
        ];

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
            return [
                'error_message' => 'Data Not Found',
            ];
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
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_user->is_active = 0;
        $specific_user->save();

        return [
            'success_message' => 'Succesfully Archived!',
        ];

    }

    public function search(Request $request)
    {
        $value = $request['value'];

        if (isset($request['is_active'])) {
            if ($request['is_active'] == 'active') {

                $is_active = 1;
            } else {

                $is_active = 0;
            }
        } else {
            $is_active = 1;
        }

        $result = User::where('is_active', $is_active)
            ->where(function ($query) use ($value) {

                $query->where('id_prefix', 'like', '%' . $value . '%')
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
                    ->orWhere('is_active', 'like', '%' . $value . '%');
            })

            ->get();

        if ($result->isEmpty()) {
            return [
                'error_message' => 'Data Not Found',
            ];
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
            return [
                'error_message' => 'Data Not Found',
            ];
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
            return [
                'error_message' => 'Data Not Found',
            ];
        }

    }

    public function login(Request $request)
    {
        if(Auth::attempt($request->only('username', 'password'))){
            $user = Auth::user();
            $user= User::where('username', $request->username)->first();
            $token = $user->createToken('my-app-token')->plainTextToken;
            $response = [
                'user' => $user,
                'token' => $token,
            ];

            $cookie = \cookie('sanctum', $token, 3600);

             return \response($response, 200)->withCookie($cookie);

        }
        return response ([
            'error' => 'Invalid Credentials',
        ], Response::HTTP_UNAUTHORIZED);

    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out',
        ];
    }

    public function username_validation(Request $request)
    {
        $username = $request->get('username');
        // $id_prefix = $request->get('id_prefix');
        // $id_no = $request->get('id_no');

        $result = DB::table('users')
            ->where('username', '=', $username)
            ->get();

        // if ($result->isEmpty()) {
        //     return [
        //         'error_message' => 'Data Not Found',
        //     ];
        // }

        return $result;

    }

    public function id_validation(Request $request)
    {
        // $id_prefix = $request->get('id_prefix');
        $id_no = $request->get('id_no');

        $result = DB::table('users')
            ->where('id_no', '=', $id_no)
            ->get();

        // if ($result->isEmpty()) {
        //     return [
        //         'error_message' => 'Data Not Found',
        //     ];
        // }

        return $result;

    }

}
