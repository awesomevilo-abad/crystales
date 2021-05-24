<?php

namespace App\Http\Controllers;

use App\Models\Reason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reason = DB::table('reasons')
            ->where('is_active', '=', 1)
            ->paginate(10);

        if (!$reason || $reason->isEmpty()) {
            $response = [
                "code" => 404,
                "message" => "Data Not Found!",
                "data" => $reason,
            ];
        } else {
            $response = [
                "code" => 200,
                "message" => "Succefully Retrieved",
                "data" => $reason,
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
            'reason' => 'required|string|unique:reasons,reason',
            'remarks' => 'required|string|unique:reasons,remarks',
            'is_active' => 'required',

        ]);

        $new_reason = Reason::create([
            'reason' => $fields['reason']
            , 'remarks' => $fields['remarks']
            , 'is_active' => $fields['is_active'],
        ]);

        return [
            $response = [
                "code" => 200,
                "message" => "Succefully Created",
                "data" => $new_company,
            ],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = Reason::find($id);

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
        $specific_reason = Reason::find($id);

        if (!$specific_reason) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_reason->reason = $request->get('reason');
        $specific_reason->remarks = $request->get('remarks');
        $specific_reason->save();

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
        $specific_reason = Reason::find($id);

        if (!$specific_reason) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_reason->is_active = 0;
        $specific_reason->save();

        return [
            'success_message' => 'Succesfully Archived!',
        ];

    }

    public function search(Request $request)
    {
        $value = $request['value'];

        $result = Reason::where('reason', 'like', '%' . $value . '%')
            ->orWhere('remarks', 'like', '%' . $value . '%')
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
