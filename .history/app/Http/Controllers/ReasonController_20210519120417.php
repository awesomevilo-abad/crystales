<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            return "No Data Found";
        }
        return $reason;
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

        return "Reason Succesfully Created";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Reason::find($id);
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
            return "No Data Found";
        }

        $specific_reason->reason = $request->get('reason');
        $specific_reason->remarks = $request->get('remarks');
        $specific_reason->save();

        return "Succesfully Updated!";
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
            return "No Data Found";
        }

        $specific_reason->is_active = 0;
        $specific_reason->save();

        return "Succesfully Archived!";

    }

    public function search(Request $request)
    {
        $value = $request['value'];

        $result = reason::where('reason_code', 'like', '%' . $value . '%')
            ->orWhere('reason_description', 'like', '%' . $value . '%')
            ->get();

        if ($result->isEmpty()) {
            return "No Data Found";
        }
        return response()->json([
            'search_result' => $result,
        ]);

        // return $result;
    }

}
