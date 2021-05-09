<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->orWhere(function ($query) use ($fields) {
                $query->where('id_prefix', $fields['id_prefix'])
                    ->where('id_no', $fields['id_no']);
            })
            ->orWhere('username', $fields['username'])
            ->get();

        if ($duplicate_users->count()) {
            throw ValidationException::withMessages([
                'error_message' => ['Error Credentials'],
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

        if ($duplicate_id_details->count()) {
            throw ValidationException::withMessages([
                'error_message' => ['Duplicate ID Details'],
            ]);
        } elseif ($duplicate_name->count()) {
            throw ValidationException::withMessages([
                'error_message' => ['Duplicate First, Middle or Last Name'],
            ]);

        } elseif ($duplicate_username->count()) {
            throw ValidationException::withMessages([
                'error_message' => ['Duplicate Username'],
            ]);
        } else {

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
            $specific_user->save();

            return "Succesfully Updated!";
        }

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
        $specific_user->save();

        return "Succesfully Archived!";

    }

    public function search(Request $request)
    {
        $id_prefix = $request['id_prefix'];
        $id_no = $request['id_no'];
        $first_name = $request['first_name'];
        $middle_name = $request['middle_name'];
        $last_name = $request['last_name'];
        $suffix = $request['suffix'];
        $department = $request['department'];
        $position = $request['position'];
        $permissions = $request['permissions'];
        $document_types = $request['document_types'];
        $categories = $request['categories'];
        $username = $request['username'];
        $is_active = $request['is_active'];

        $query = User::query();
        if($request->has('id_prefix')){
            $query = $query->where('id_prefix','like','%'.$request->id_prefix.'%');
        }
        if($request->has('id_no')){
            $query = $query->where('id_no',$request->id_no);
        }
        if($request->has('first_name')){
            $query = $query->whereIn('first_name',$request->first_name);
        }
        if($request->has('middle_name')){
            $query = $query->whereIn('middle_name',$request->middle_name);
        }
        if($request->has('last_name')){
            $query = $query->whereIn('last_name',$request->last_name);
        }
        if($request->has('language_name')){
            $query = $query->whereHas('jobLanguageIds',function ($q) use($request){
                $q->select('languages.id','languages.name')->where('languages.name',$request->language_name);
            });
        }
        return $query;


        $result = DB::table('users')
            ->where('id_prefix', 'like', '%' . $id_prefix . '%')
            ->where('id_no', 'like', '%' . $id_no . '%')
            ->where('first_name', 'like', '%' . $first_name . '%')
            ->where('middle_name', 'like', '%' . $middle_name . '%')
            ->where('last_name', 'like', '%' . $last_name . '%')
            ->where('suffix', 'like', '%' . $suffix . '%')
            ->where('department', 'like', '%' . $department . '%')
            ->where('position', 'like', '%' . $position . '%')
            ->where('permissions', 'like', '%' . $permissions . '%')
            ->where('document_types', 'like', '%' . $document_types . '%')
            ->where('categories', 'like', '%' . $categories . '%')
            ->where('username', 'like', '%' . $username . '%')
            ->where('is_active', 'like', '%' . $is_active . '%')
            ->get();

        return response()->json([
            'res' => $result,
        ]);

        return $result;

    }

    /**
     * Update the User Password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_password(Request $request, $id)
    {
        $specific_user = User::find($id);
        $specific_user->password = 0;
        $specific_user->save();

        return "Succesfully Archived!";

    }

}
