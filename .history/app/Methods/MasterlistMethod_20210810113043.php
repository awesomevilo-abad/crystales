<?php

namespace App\Methods;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MasterlistMethod{

    public static function restore($table,$id){

        return $table

        $specific_data = DB::table('categories')
        ->where('id',1)
        ->where('is_active',0)
        ->get();

        if ($specific_data->isEmpty()) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        return $specific_data;

        return [
            'success_message' => 'Succesfully Restored!',
        ];
    }
}
