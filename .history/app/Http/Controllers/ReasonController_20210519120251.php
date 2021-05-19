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
        $specific_company = Company::find($id);

        if (!$specific_company) {
            return "No Data Found";
        }

        $specific_company->company_code = $request->get('company_code');
        $specific_company->company_description = $request->get('company_description');
        $specific_company->save();

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
}
