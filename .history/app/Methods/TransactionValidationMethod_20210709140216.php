<?php

namespace App\Methods;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionValidationMethod
 {
    //  PAD Transactions
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
