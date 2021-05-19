<?php

namespace App\Http\Controllers;

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
        //
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

        $document_categories = DB::table('documents AS d')
            ->join('document_categories AS dc', 'd.id', '=', 'dc.document_id')
            ->join('categories AS c', 'dc.category_id', 'c.id')
            ->where('c.is_active', '1')
            ->select('d.document_type', 'c.name', 'c.id AS cid', 'd.id AS did')
            ->get();

        $active_categories = $document_categories->pluck('cid');

        foreach ($request[''] as $cat) {
            if ($active_categories->contains($cat)) {
                echo 'Wala' . $cat;
            } else {
                echo 'Meron' . $cat;
            }
        }
        return $active_categories;

        // foreach ($cat as $active_categories) {
        //     return $cat;
        // }

        // foreach()
        // {

        // }
        // $fields = $request->validate([
        //     'document_type' => 'required|string|unique:documents,document_type',
        //     'document_description' => 'required|string|unique:documents,document_description',
        //     'is_active' => 'required',

        // ]);

        // $new_document = Document::create([
        //     'document_type' => $fields['document_type']
        //     , 'document_description' => $fields['document_description']
        //     , 'is_active' => $fields['is_active'],
        // ]);

        // $category_ids = $request['categories'];
        // $new_document->categories()->sync($category_ids);

        // return "Document Succesfully Created";

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
