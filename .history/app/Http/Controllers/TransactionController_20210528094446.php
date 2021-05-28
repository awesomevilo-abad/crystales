<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
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
        $fields = $request->validate([

            // AUTOMATIC USER DETAILS BASED ON LOGIN
            "users_id"=> 'required'
            // ,"id_prefix"=> 'required'
            // ,"id_no"=> 'required'
            // ,"first_name"=> 'required'
            // ,"middle_name"=> 'required'
            // ,"last_name"=> 'required'
            // ,"suffix"=> 'required'
            // ,"department"=> 'required'
            ,"document_id"=> 'required'
            ,"document_type"=> 'required'
            ,"category_id"=> 'required'
            ,"category"=> 'required'

            // PAYMENT TYPE BASED ON FE SELECTED
            ,"payment_type"=> 'required'

            // SELECTED COMPANY
            ,"company_id"=> 'required'
            ,"company"=> 'required'

            // INPUTTED DOCUMENT NO
            ,"document_no"=> 'required'

            ,"supplier_id"=> 'required'
            ,"supplier"=> 'required'


            ,"transaction_id"=> 'required'
            ,"tag_id"=> 'required'
            ,"document_date"=> 'required'
            ,"po_id"=> 'required'
            ,"referrence_id"=> 'required'
            ,"referrence_type"=> 'required'
            ,"date_requested"=> 'required'
            ,"remarks"=> 'required'
            ,"status"=> 'required'
            ,"reason_id"=> 'required'
            ,"reason"=> 'required'
            ,"document_amount"=> 'required'
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
