<?php

namespace App\Methods;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionValidationMethod
 {
    //  PAD Transactions
   public static function padValidation($fields){

    if(isset($fields['document_no'])){

    }
    $response = [
        "code" => 404,
        "message" => "Either document is not registered or document id and document type does not match",
        "data" => null,
    ];
    print_r($fields);
   }
 }
