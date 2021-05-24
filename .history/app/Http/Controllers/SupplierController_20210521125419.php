<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = DB::table('suppliers')
            ->where('is_active', '=', 1)
            ->orderBy('id')
            ->paginate(10);

        if (!$suppliers || $suppliers->isEmpty()) {
            $response = [
                "code" => 404,
                "message" => "Data Not Found!",
                "data" => $result,
            ];

            return $response;

        } else {
            return $suppliers;
        }

        return response($response);
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
            'supplier_code' => 'required|string|unique:suppliers,supplier_code',
            'referrence_description' => 'required|string|unique:referrences,referrence_description',
            'is_active' => 'required',

        ]);

        $new_referrence = Referrence::create([
            'referrence_type' => $fields['referrence_type']
            , 'referrence_description' => $fields['referrence_description']
            , 'is_active' => $fields['is_active'],
        ]);

        if (!$new_referrence->count() == 0) {
            $response = [
                "code" => 201,
                "message" => "Succesfully Created!",
                "data" => $new_referrence,

            ];

        }

        return response($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = Supplier::find($id);

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
