<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->groupBy('id')
            ->latest()
            ->get('id');

        $supplier_ids = $suppliers->pluck('id');

        $referrence_ids = collect();
        foreach ($supplier_ids as $sup_id) {
            $result = DB::table('suppliers as s')
                ->select('r.id as referrences')
                ->leftJoin('supplier_referrences AS sr', 's.id', '=', 'sr.supplier_id')
                ->leftJoin('referrences AS r', 'sr.referrence_id', '=', 'r.id')
                ->where('s.is_active', '=', 1)
                ->where('s.id', '=', 10)
                ->get();

            $referrence_ids->push(['sup_id' => $sup_id,
                'referrences' => $result->pluck('referrences')]);
        }

        $final_supplier_details = collect();
        foreach ($referrence_ids as $specific_referrence) {
            $sup_id = $specific_referrence['sup_id'];

            $supplier_details = DB::table('suppliers as s')
                ->select('s.supplier_name', 's.supplier_code', 's.terms', 'st.type', 'st.transaction_days')
                ->leftJoin('supplier_types as st', 's.supplier_type_id', '=', 'st.id')
                ->where('s.id', $sup_id)
                ->get();

            foreach ($supplier_details as $sd) {

                $final_supplier_details->push([
                    'supplier_name' => $sd->supplier_name,
                    'supplier_code' => $sd->supplier_code,
                    'terms' => $sd->terms,
                    'type' => $sd->type,
                    'transaction_days' => $sd->transaction_days,
                    'referrences' => $specific_referrence['referrences'],

                ]);
            }
        }
        // return $final_supplier_details;

        if (!$final_supplier_details || $final_supplier_details->isEmpty()) {
            $response = [
                "code" => 404,
                "message" => "Data Not Found!",
                "data" => $final_supplier_details,
            ];

        } else {
            $response = [
                "code" => 200,
                "message" => "Succefully Retrieved",
                "data" => $final_supplier_details,
            ];

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
        // $result = Supplier::find($id);
        $suppliers = DB::table('suppliers')
            ->where('is_active', '=', 1)
            ->where('id', '=', $id)
            ->groupBy('id')
            ->latest()
            ->get('id');

        $supplier_ids = $suppliers->pluck('id');

        $referrence_ids = collect();
        foreach ($supplier_ids as $sup_id) {
            $result = DB::table('suppliers as s')
                ->select('r.id as referrences')
                ->leftJoin('supplier_referrences AS sr', 's.id', '=', 'sr.supplier_id')
                ->leftJoin('referrences AS r', 'sr.referrence_id', '=', 'r.id')
                ->where('s.is_active', '=', 1)
                ->where('s.id', '=', 10)
                ->get();

            $referrence_ids->push(['sup_id' => $sup_id,
                'referrences' => $result->pluck('referrences')]);
        }

        $final_supplier_details = collect();
        foreach ($referrence_ids as $specific_referrence) {
            $sup_id = $specific_referrence['sup_id'];

            $supplier_details = DB::table('suppliers as s')
                ->select('s.supplier_name', 's.supplier_code', 's.terms', 'st.type', 'st.transaction_days')
                ->leftJoin('supplier_types as st', 's.supplier_type_id', '=', 'st.id')
                ->where('s.id', $sup_id)
                ->get();

            foreach ($supplier_details as $sd) {

                $final_supplier_details->push([
                    'supplier_name' => $sd->supplier_name,
                    'supplier_code' => $sd->supplier_code,
                    'terms' => $sd->terms,
                    'type' => $sd->type,
                    'transaction_days' => $sd->transaction_days,
                    'referrences' => $specific_referrence['referrences'],

                ]);
            }
        }
        // return $final_supplier_details;

        if (!$final_supplier_details || $final_supplier_details->isEmpty()) {
            $response = [
                "code" => 404,
                "message" => "Data Not Found!",
                "data" => $final_supplier_details,
            ];

        } else {
            $response = [
                "code" => 200,
                "message" => "Succefully Retrieved",
                "data" => $final_supplier_details,
            ];

        }

        return response($response);
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
     * supplier the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function supplier(Request $request, $id)
    {
        $specific_supplier = Supplier::find($id);

        if (!$specific_supplier) {
            $response = [
                "code" => 404,
                "message" => "Data Not Found!",
                "data" => $specific_supplier,
            ];
        }

        $specific_supplier->supplier_code = $request->get('supplier_code');
        $specific_supplier->supplier_name = $request->get('supplier_name');
        $specific_supplier->terms = $request->get('terms');
        $specific_supplier->supplier_type_id = $request->get('supplier_type_id');

        $referrence_ids = $request['referrences'];
        $specific_supplier->referrences()->detach();
        $specific_supplier->referrences()->attach($referrence_ids);

        $specific_supplier->save();

        return [
            $response = [
                "code" => 200,
                "message" => "Succefully Added",
                "data" => $final_supplier_details,
            ],
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
        //
    }

    #_________________________SPECIAL CASE________________________________
    /**
     * supplier the specified resource in storage.
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
