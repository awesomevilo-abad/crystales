<?php

namespace App\Methods;

use App\Methods\GenericMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionValidationMethod
 {
    // public static

    //  MAIN PAD Transactions
   public static function padValidation($fields){

    if(isset($fields['document_no'])){

        $po = $fields['po_group'];

        $po_count = count($po);
        $po_validation_count = count($po);

        $po_validation_total_amount = 0;
        $po_validation_total_qty = 0;
        $rr_validation_total_qty = 0;

        $po_total_amount = 0;
        $po_total_qty = 0;
        $rr_total_qty = 0;

        if (GenericMethod::validateIfDocumentNoExist($fields['document_no']) > 0){
            $validation_result = [
                "code" => 403,
                "message" => "Document No. already exist",
                    "data" => null,
            ];
        }else{

        }



        $response = $validation_result;

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
