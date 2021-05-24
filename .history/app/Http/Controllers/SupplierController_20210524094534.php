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
        $suppliers = Supplier::with('referrences')
            ->where('id', 10)->latest()->get();
        // $suppliers = Supplier::where('is_active', 1)->latest()->get();
        // return $this->supplier_type()->type;
        // ---------------------------------------------------

        foreach ($suppliers as $specific_supplier) {
            $supplier_code = $specific_referrence['supplier_code'];
            //     $supplier_name = $specific_referrence['supplier_name'];
            //     $terms = $specific_referrence['terms'];
            //     $supplier_type_id = $specific_referrence['supplier_type_id'];

            //     echo $supplier_type_id;
        }

        // ---------------------------------------------------
        // $suppliers = DB::table('suppliers')
        //     ->where('is_active', '=', 1)
        //     ->groupBy('id')
        //     ->get('id');

        // $supplier_ids = $suppliers->pluck('id');

        // $referrence_ids = collect();
        // foreach ($supplier_ids as $sup_id) {
        //     $result = DB::table('suppliers as s')
        //     // ->select('r.id as referrences,')
        //         ->join('supplier_referrences AS sr', 's.id', '=', 'sr.supplier_id')
        //         ->join('referrences AS r', 'sr.referrence_id', '=', 'r.id')
        //         ->where('s.is_active', '=', 1)
        //         ->where('s.id', '=', $sup_id)
        //         ->get();

        //     $referrence_ids->push(['sup_id' => $sup_id, 'referrences' => $result->pluck('referrences')]);
        // }
        // foreach ($referrence_ids as $specific_referrence) {
        //     $supplier_code = $specific_referrence['supplier_code'];
        //     $supplier_name = $specific_referrence['supplier_name'];
        //     $terms = $specific_referrence['terms'];
        //     $supplier_type_id = $specific_referrence['supplier_type_id'];

        //     echo $supplier_type_id;
        // }
        // if (!$suppliers || $suppliers->isEmpty()) {
        //     $response = [
        //         "code" => 404,
        //         "message" => "Data Not Found!",
        //         "data" => $result,
        //     ];

        //     return $response;

        // } else {
        //     return $suppliers;
        // }

        // return response($result);
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
            'supplier_name' => 'required|string|unique:suppliers,supplier_name',
            'terms' => 'required',
            'supplier_type_id' => 'required',
            'is_active' => 'required',

        ]);

        // print_r($request['referrences']);
        $new_suppliers = Supplier::create([
            'supplier_code' => $fields['supplier_code']
            , 'supplier_name' => $fields['supplier_name']
            , 'terms' => $fields['terms']
            , 'supplier_type_id' => $fields['supplier_type_id']
            , 'is_active' => $fields['is_active'],
        ]);

        $referrence_ids = $request['referrences'];
        $new_suppliers->referrences()->attach($referrence_ids);

        if (!$new_suppliers->count() == 0) {
            $response = [
                "code" => 201,
                "message" => "Succesfully Created!",
                "data" => $new_suppliers,

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
        $specific_supplier = supplier::find($id);

        if (!$specific_supplier) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_supplier->is_active = 0;
        $specific_supplier->save();

        return [
            'success_message' => 'Succesfully Archived!',
        ];

    }

    public function search(Request $request)
    {

    }
}
