<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Methods\MasterlistMethod;
use App\Methods\GenericMethod;

class MasterlistController extends Controller
{
    public function restore(Request $request){
        $tableName = $request->table;
        $tableid = $request->id;
        return MasterlistMethod::restore($tableName,$tableid);
    }

    public function categoryPerDocument(Request $request){
        $fields = $request->validate(['document_id'=>'required']);

        $categories = DB::table('categories')
        ->leftJoin('document_categories','categories.id','=','document_categories.category_id')
        ->where('document_categories.document_id',$fields['document_id'])
        ->get(['categories.id','categories.name']);

        $categories_list = [];
        foreach($categories as $specific_categories){
            array_push($categories_list,array($specific_categories->id =>$specific_categories->name));
        }
        return $categories;
    }

    public function getUserDocumentCategory(){
        // $user = auth()->user();
        // $id = $user->id;
        $id = 1;

        $active_categories =collect();

        $user_details= GenericMethod::getUserDetailsById($id);
        $document_type =  $user_details[0]['document_types'];

        foreach($document_type as $specific_dpcument_type){
            $categories = ($specific_dpcument_type['document_categories']);
            $echo 
            foreach($categories as $specific_category){
                print_r($specific_category);
            }
        }
    }

}
