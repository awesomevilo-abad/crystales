<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = DB::table('companies')
            ->where('is_active', '=', 1)
            ->paginate(10);

        if (!$company || $company->isEmpty()) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }
        return $company;
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
            'company_code' => 'required|string|unique:companies,company_code',
            'company_description' => 'required|string|unique:companies,company_description',
            'is_active' => 'required',

        ]);

        $new_company = Company::create([
            'company_code' => $fields['company_code']
            , 'company_description' => $fields['company_description']
            , 'is_active' => $fields['is_active'],
        ]);

        return "Company Succesfully Created";

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = Company::find($id);

        if (empty($result)) {
            return "Not Found";
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
        $specific_company = Company::find($id);

        if (!$specific_company) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_company->company_code = $request->get('company_code');
        $specific_company->company_description = $request->get('company_description');
        $specific_company->save();

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
        $specific_company = Company::find($id);

        if (!$specific_company) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_company->is_active = 0;
        $specific_company->save();

        return "Succesfully Archived!";

    }

    public function search(Request $request)
    {
        $value = $request['value'];

        $result = Company::where('company_code', 'like', '%' . $value . '%')
            ->orWhere('company_description', 'like', '%' . $value . '%')
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
