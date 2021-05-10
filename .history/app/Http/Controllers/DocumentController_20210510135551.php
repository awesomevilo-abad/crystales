<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

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
        $fields = $request->validate([
            'document_type' => 'required|string',
            'document_description' => 'required|string',
            'categories'=>'string'
            'is_active' => 'required',

        ]);

        // $new_document = Document::create([
        //     'document_type' => $fields['document_type']
        //     , 'document_description' => $fields['document_description']
        //     , 'is_active' => $fields['is_active'],
        // ]);

        $document = new Document();
        $document->document_type = $fields['document_type'];
        $document->document_description = $fields['document_description'];
        $document->is_active = $fields['is_active'];
        $document->save();

        $roleids = $fields['categories'];
        $document->categories()->attach($roleids);
        return "Document Succesfully Created";

        return $new_document;
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
