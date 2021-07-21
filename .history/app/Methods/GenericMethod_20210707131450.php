<?php

namespace App\GenericMethod;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GenericMethod{

    public function countTableById($table,$id){
        $table = DB::table($table)->where('id', $id)->where('is_active', 1);
        return $table->count();
    }



}
