<?php

namespace App\Methods;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MasterlistMethod{

    public static function restore($table,$id){
        $specific_data = DB::table('categories')
        ->where('id',$id)
        // ->where('is_active',0)
        ->get();

        return $specific_data;
        if ($specific_data->isEmpty()) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        return [
            'success_message' => 'Succesfully Restored!',
        ];
    }
}
