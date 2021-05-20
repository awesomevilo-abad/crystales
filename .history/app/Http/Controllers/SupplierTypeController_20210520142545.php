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
            return [
                'error_message' => 'Data Not Found',
            ];
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
            'type' => 'required|string|unique:supplier_types,type',
            'transaction_days' => 'required',
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
        $result = SupplierType::find($id);

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
        $specific_supplier_type = SupplierType::find($id);

        if (!$specific_supplier_type) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_supplier_type->type = $request->get('type');
        $specific_supplier_type->transaction_days = $request->get('transaction_days');
        $specific_supplier_type->save();

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
        $specific_supplier_type = SupplierType::find($id);

        if (!$specific_supplier_type) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_supplier_type->is_active = 0;
        $specific_supplier_type->save();

        return [
            'success_message' => 'Succesfully Archived!',
        ];

    }

    public function search(Request $request)
    {
        $value = $request['value'];

        $result = SupplierType::where('type', 'like', '%' . $value . '%')
            ->orWhere('transaction_days', 'like', '%' . $value . '%')
            ->get();

        if ($result->isEmpty()) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }
        return response()->json([
            'search_result' => $result,
        ]);

        // return $result;
    }

}
