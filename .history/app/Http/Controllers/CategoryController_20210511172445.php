<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = DB::table('categories')
            ->where('is_active', '=', 1)->get();
        return $categories;
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
        $fields = $request->validate([
            'name' => 'required|string',
            'is_active' => 'required',

        ]);

        $duplicate_category = DB::table('categories')
            ->where('name', $fields['name'])
            ->where('is_active', 1)
            ->get();

        $duplicate_category_inactive = DB::table('categories')
            ->where('name', $fields['name'])
            ->where('is_active', 0)
            ->get();

        if ($duplicate_category->count()) {
            throw ValidationException::withMessages([
                'error_message' => ['Category already registered'],
            ]);
        } elseif ($duplicate_category_inactive->count()) {
            throw ValidationException::withMessages([
                'error_message' => ['Category already registered but inactive'],
            ]);
        }

        $new_category = Category::create([
            'name' => $fields['name']
            , 'is_active' => $fields['is_active'],
        ]);

        return $new_category;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Category::find($id);
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

        // if ($duplicate_id_details->count()) {
        //     throw ValidationException::withMessages([
        //         'error_message' => ['Duplicate ID Details'],
        //     ]);
        // } elseif ($duplicate_name->count()) {
        //     throw ValidationException::withMessages([
        //         'error_message' => ['Duplicate First, Middle or Last Name'],
        //     ]);

        // } elseif ($duplicate_username->count()) {
        //     throw ValidationException::withMessages([
        //         'error_message' => ['Duplicate Username'],
        //     ]);
        // } else {

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
        //
    }
}