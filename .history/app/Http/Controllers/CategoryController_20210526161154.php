<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $is_active = $request->get('is_active');

        if ($is_active == 'active') {
            $categories = DB::table('categories')
                ->where('is_active', '=', 1)
                ->latest()
                ->paginate(10);

        } elseif ($is_active == 'inactive') {
            $categories = DB::table('categories')
                ->where('is_active', '=', 0)
                ->latest()
                ->paginate(10);

        } else {
            $categories = DB::table('categories')
                ->latest()
                ->paginate(10);
        }

        if (!$categories || $categories->isEmpty()) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }
        return $categories;
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
            'name' => 'required|string',
            'is_active' => 'required',

        ]);

        $duplicate_category = DB::table('categories')
            ->where('name', $fields['name'])
            ->where('is_active', 1)
            ->get();

        $duplicate_category_inactive = DB::table('categories')
            ->where('name', $fields['name'])
            ->where('is_active', 0)
            ->get();

        if ($duplicate_category->count()) {
            throw ValidationException::withMessages([
                'error_message' => ['Category already registered'],
            ]);
        } elseif ($duplicate_category_inactive->count()) {
            throw ValidationException::withMessages([
                'error_message' => ['Category already registered but inactive'],
            ]);
        }

        $new_category = Category::create([
            'name' => $fields['name']
            , 'is_active' => $fields['is_active'],
        ]);

        return [
            'success_message' => 'Succesfully Created!',
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
        $result = Category::find($id);
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
        $specific_category = Category::find($id);

        $fields = $request->validate([
            'name' => ['unique:categories,name,' . $id],

        ]);

        if (!$specific_category) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        $specific_category->name = $request->get('name');
        $specific_category->save();

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
        // $specific_category = Category::find($id);

        // if (!$specific_category) {
        //     return [
        //         'error_message' => 'Data Not Found',
        //     ];
        // }

        // $specific_category->is_active = 0;
        // $specific_category->save();

        $users = User::all();
        foreach ($users as $specific_user) {
            $document_types = json_decode($specific_user->document_types);
            $document_types = $document_types;
            // Search

            foreach ($document_types as $specific_document_type) {
                //     $categories_per_doc_id = $specific_document_type->categories;
                //     $untag_id_position = array_search($id, $categories_per_doc_id);

                //     unset($categories_per_doc_id[$untag_id_position]);

                //     $specific_document_type->categories = $categories_per_doc_id;
                //     $specific_document_type->categories = array_values($specific_document_type->categories);

            }

            // $specific_user->document_types = json_encode($document_types);
            // $specific_user->save();

            // return $users;

        }
        // return $users;

        // return [
        //     'success_message' => 'Succesfully Archived!',
        // ];

    }

    public function search(Request $request)
    {
        $value = $request['value'];

        $result = Category::where('name', 'like', '%' . $value . '%')
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

    public function categories()
    {
        $categories = DB::table('categories')
            ->where('is_active', '=', 1)
            ->get();

        if (!$categories || $categories->isEmpty()) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }
        return $categories;
    }

}
