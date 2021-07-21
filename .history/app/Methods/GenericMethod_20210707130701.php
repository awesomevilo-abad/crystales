<?php

namespace App\Methods;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GenericMethod{

    public function countTableById(){
        $table = DB::table('documents')->where('id', $doc_id)->where('is_active', 1);
        return $documents->count();
    }



}
