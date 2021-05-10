<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $new_category = User::create([
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
        //
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
