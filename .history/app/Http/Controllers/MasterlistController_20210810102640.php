<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App

class MasterlistController extends Controller
{
    public function restore(Request $request){
        $tableName = $request->table;
        $tableid = $request->id;
       return MasterlistMethod::restore('categories',$tableid);
    }
}
