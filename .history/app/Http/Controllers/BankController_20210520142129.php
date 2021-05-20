<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bank = DB::table('banks')
            ->where('is_active', '=', 1)
            ->paginate(10);

        if (!$bank || $bank->isEmpty()) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }
        return $bank;
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
            'bank_code' => 'required|string|unique:banks,bank_code',
            'bank_name' => 'required|string|unique:banks,bank_name',
            'bank_account' => 'required|string|unique:banks,bank_account',
            'bank_location' => 'required|string',
            'is_active' => 'required',

        ]);

        $new_bank = Bank::create([
            'bank_code' => $fields['bank_code']
            , 'bank_name' => $fields['bank_name']
            , 'bank_account' => $fields['bank_account']
            , 'bank_location' => $fields['bank_location']
            , 'is_active' => $fields['is_active'],
        ]);

        return "Bank Succesfully Created";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = Bank::find($id);

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
        $specific_bank = Bank::find($id);

        if (!$specific_bank) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_bank->bank_code = $request->get('bank_code');
        $specific_bank->bank_name = $request->get('bank_name');
        $specific_bank->bank_account = $request->get('bank_account');
        $specific_bank->bank_location = $request->get('bank_location');
        $specific_bank->save();

        return "Succesfully Updated!";
        return [
            'error_message' => 'Data Not Found',
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
        $specific_bank = Bank::find($id);

        if (!$specific_bank) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_bank->is_active = 0;
        $specific_bank->save();

        return "Succesfully Archived!";

    }

    public function search(Request $request)
    {
        $value = $request['value'];

        $result = Bank::where('bank_code', 'like', '%' . $value . '%')
            ->orWhere('bank_name', 'like', '%' . $value . '%')
            ->orWhere('bank_account', 'like', '%' . $value . '%')
            ->orWhere('bank_location', 'like', '%' . $value . '%')
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
