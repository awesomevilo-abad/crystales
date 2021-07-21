<?php

namespace App\Methods;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionValidationMethod
 {
    //  PAD Transactions
   public static function padValidation($fields){

    if(isset($fields['document_no'])){

        $po_group = $fields['po_group'];
        $po_count = $po_group;

    }else{
        $response = [
            "code" => 404,
            "message" => "Document number is null",
            "data" => null,
        ];
    }


    return $response;
    }

 }
