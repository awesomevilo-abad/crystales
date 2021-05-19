<?php

namespace App\Http\Controllers;

use App\Models\SupplierType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplier_type = DB::table('supplier_types')
            ->where('is_active', '=', 1)
            ->paginate(10);

        if (!$supplier_type || $supplier_type->isEmpty()) {
            return "No Data Found";
        }
        return $supplier_type;
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
            'type' => 'required|string|unique:companies,supplier_type_code',
            'supplier_type_description' => 'required|string|unique:companies,supplier_type_description',
            'is_active' => 'required',

        ]);

        $new_supplier_type = supplier_type::create([
            'supplier_type_code' => $fields['supplier_type_code']
            , 'supplier_type_description' => $fields['supplier_type_description']
            , 'is_active' => $fields['is_active'],
        ]);

        return "supplier_type Succesfully Created";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return SupplierType::find($id);
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
