<?php

namespace App\Methods;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GenericMethod{

    public function countTableById($table){
        $table = DB::table('".."')->where('id', $doc_id)->where('is_active', 1);
        return $table->count();
    }



}
