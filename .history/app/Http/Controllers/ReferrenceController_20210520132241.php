<?php

namespace App\Http\Controllers;

use App\Models\Referrence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferrenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $referrences = DB::table('referrences')
            ->where('is_active', '=', 1)
            ->orderBy('id')
            ->paginate(10);

        if (!$referrence || $referrences->isEmpty()) {
            return "No Data Found";
        }

        return $referrences;
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
            'referrence_type' => 'required|string|unique:referrences,referrence_type',
            'referrence_description' => 'required|string|unique:referrences,referrence_description',
            'document_id' => 'required',
            'is_active' => 'required',

        ]);

        $new_supplier_type = SupplierType::create([
            'type' => $fields['type']
            , 'transaction_days' => $fields['transaction_days']
            , 'is_active' => $fields['is_active'],
        ]);

        return "Supplier Type Succesfully Created";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = Referrence::find($id);

        if (!$result) {
            return "No Data Found";
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
