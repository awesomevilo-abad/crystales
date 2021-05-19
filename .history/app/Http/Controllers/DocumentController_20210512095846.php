<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $documents = DB::table('documents AS d')
        // ->select('d.id AS did', 'd.document_type', 'd.document_description', 'd.is_active AS dis_active', 'c.id AS categories')
        // ->join('document_categories AS dc', 'd.id', '=', 'dc.document_id')
        // ->join('categories AS c', 'dc.category_id', '=', 'c.id')
        // ->where('d.is_active', '=', 1)
        // ->groupBy('did',)
        // ->get();

        $documents = DB::table('documents AS d')
            ->select('d.id AS did')
            ->where('d.is_active', '=', 1)
            ->groupBy('did')
            ->get();

        $categories = $documents->pluck('categories');

        $category_ids = collect();
        foreach ($documents as $doc_id) {
            $category_ids = DB::table('documents AS d')
                ->select('d.id AS did', 'd.document_type', 'd.document_description', 'd.is_active AS dis_active', 'c.id AS categories')
                ->join('document_categories AS dc', 'd.id', '=', 'dc.document_id')
                ->join('categories AS c', 'dc.category_id', '=', 'c.id')
                ->where('d.is_active', '=', 1)
                ->where('d.id', '=', $doc_id)
                ->get();



        }

        return $documents;

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
            'document_type' => 'required|string|unique:documents,document_type',
            'document_description' => 'required|string|unique:documents,document_description',
            'is_active' => 'required',

        ]);

        // VALIDATION IF CATEGORY IS EXISTING OR NOT

        $document_categories = DB::table('categories AS c')
            ->where('c.is_active', '1')
            ->select('c.name', 'c.id AS cid')
            ->get();

        $active_categories = $document_categories->pluck('cid');
        $category_details = collect();

        foreach ($request['categories'] as $cat) {
            if ($active_categories->contains($cat)) {
                $category_details->push(['category_is_active' => 1, 'cat_id' => $cat]);
            } else {
                $category_details->push(['category_is_active' => 0, 'cat_id' => $cat]);
            }

        }

        $unregistered_category_detail = [];
        foreach ($category_details as $specific_category_detail) {
            if ($specific_category_detail['category_is_active'] == 0) {

                array_push($unregistered_category_detail, $specific_category_detail['cat_id']);
            }

        }

        if ($unregistered_category_detail) {

            $unregistered_category = implode(',', $unregistered_category_detail);
            echo $unregistered_category . ' is not existing in the category table or disabled';
        } else {

            // INSERT DOCUMENT

            $new_document = Document::create([
                'document_type' => $fields['document_type']
                , 'document_description' => $fields['document_description']
                , 'is_active' => $fields['is_active'],
            ]);

            $category_ids = $request['categories'];
            $new_document->categories()->sync($category_ids);

            return "Document Succesfully Created";
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
}
