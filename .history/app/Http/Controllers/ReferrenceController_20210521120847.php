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

        if (!$referrences || $referrences->isEmpty()) {
            return [
                'error_message' => 'Data Not Found',
            ];
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
            'is_active' => 'required',

        ]);

        $new_referrence = Referrence::create([
            'referrence_type' => $fields['referrence_type']
            , 'referrence_description' => $fields['referrence_description']
            , 'is_active' => $fields['is_active'],
        ]);

        $response = [
            "code" => 201,
            "message" => "Succesfully Created!",
            "data" => $new_referrence,

        ];

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
        $result = Referrence::find($id);

        if (!$result) {

            $response = [
                "code" => 404,
                "message" => "Data Not Found!",
                "data" => $result,

            ];
        }
        $response = [
            "code" => 200,
            "message" => "Ok",
            "data" => $result,

        ];

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $specific_referrence = Referrence::find($id);

        if (!$specific_referrence) {
            $response = [
                "code" => 404,
                "message" => "Data Not Found!",
                "data" => $result,

            ];
        }

        $specific_referrence->type = $request->get('type');
        $specific_referrence->transaction_days = $request->get('transaction_days');
        $specific_referrence->save();

        $response = [
            "code" => 200,
            "message" => "Succesfully Updated",
            "data" => $result,

        ];

        return response($response);
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
        $specific_referrence = Referrence::find($id);

        if (!$specific_referrence) {
            $response = [
                "code" => 404,
                "message" => "Data Not Found!",
                "data" => "Empty",

            ];
        }

        $specific_referrence->is_active = 0;
        $specific_referrence->save();

        $response = [
            "code" => 200,
            "message" => "Succesfully Archived",
            "data" => $result,

        ];
        return response($response);

    }

    public function search(Request $request)
    {
        $value = $request['value'];

        $result = Referrence::where('type', 'like', '%' . $value . '%')
            ->orWhere('transaction_days', 'like', '%' . $value . '%')
            ->get();

        if ($result->isEmpty()) {
            $response = [
                "code" => 404,
                "message" => "Data Not Found!",
                "data" => "Empty",

            ];
        }

        $response = [
            "code" => 200,
            "message" => "Ok",
            "data" => $result,

        ];

        return response($response);

    }
}
