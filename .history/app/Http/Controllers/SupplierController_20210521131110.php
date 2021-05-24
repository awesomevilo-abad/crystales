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
        $suppliers = DB::table('suppliers')
            ->where('is_active', '=', 1)
            ->orderBy('id')
            ->paginate(10);

        if (!$suppliers || $suppliers->isEmpty()) {
            $response = [
                "code" => 404,
                "message" => "Data Not Found!",
                "data" => $result,
            ];

            return $response;

        } else {
            return $suppliers;
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

        $new_suppliers = Supplier::create([
            'supplier_code' => $fields['supplier_code']
            , 'supplier_name' => $fields['supplier_name']
            , 'terms' => $fields['terms']
            , 'supplier_type_id' => $fields['supplier_type_id']
            , 'is_active' => $fields['is_active'],
        ]);

        $category_ids = $request['categories'];
        $new_document->categories()->attach($category_ids);

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
        $specific_document = Document::find($id);

        if (!$specific_document) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_document->is_active = 0;
        $specific_document->save();

        return [
            'success_message' => 'Succesfully Archived!',
        ];

    }

    public function search(Request $request)
    {

        $value = $request['value'];

        $category_ids = collect();
        $result = DB::table('documents AS d')
            ->select('d.id AS did', 'c.id AS categories')
            ->leftjoin('document_categories AS dc', 'd.id', '=', 'dc.document_id')
            ->leftjoin('categories AS c', 'dc.category_id', '=', 'c.id')
            ->where('d.is_active', '=', 1)
            ->where(function ($query) use ($value) {
                $query->where('d.document_type', 'like', '%' . $value . '%')
                    ->orWhere('d.document_description', 'like', '%' . $value . '%');
            })
            ->get();

        if ($result->isEmpty()) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        foreach ($result as $specific_result) {
            $category_ids->push(['doc_id' => $specific_result->did, 'cat_id' => $result->pluck('categories')]);
        }

        $document_categories = [];
        $document_categories_no_duplicates = [];
        foreach ($category_ids as $specific_document) {
            $doc_id = $specific_document['doc_id'];
            $cat_id = $specific_document['cat_id'];

            $document_details = DB::table('documents AS d')
                ->select('id', 'document_type', 'document_description', 'is_active')
                ->where('is_active', '=', 1)
                ->where('id', '=', $doc_id)
                ->get();

            // dd($cat_id);
            $document_details['categories'] = $cat_id;
            array_push($document_categories, $document_details);

        }
        $document_categories_no_duplicates = array_unique($document_categories);
        return response()->json([
            'search_result' => $document_categories_no_duplicates,
        ]);
    }
}
