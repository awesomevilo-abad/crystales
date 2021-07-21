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


        if (GenericMethod::validateIfDocumentNoExist($fields['document_no']) > 0){
            $validation_result = [
                "code" => 403,
                "message" => "Document No. already exist",
                    "data" => null,
            ];
        }else{
            $po = $fields['po_group'];

            $po_count = count($po);
            $po_validation_count = count($po);

            $po_validation_total_amount = 0;
            $po_validation_total_qty = 0;
            $rr_validation_total_qty = 0;

            $po_total_amount = 0;
            $po_total_qty = 0;
            $rr_total_qty = 0;

            for($i=0;$i<$po_validation_count;$i++){
                $po_validation_object = $fields['po_group'][$i];
                $po_validation_object = (object) $po_validation_object;
                $po_no =  $po_validation_object->po_no;

                $po_validation_amount =(float) str_replace(',', '', $po_validation_object->po_amount);
                $po_validation_qty =(float) str_replace(',', '', $po_validation_object->po_qty);
                $po_validation_total_amount = $po_validation_total_amount+$po_validation_amount;
                $po_validation_total_qty = $po_validation_total_qty+$po_validation_qty;

                if (GenericMethod::validateIfPONoExist($fields['company_id'],$fields['supplier_id'],$po_no) > 0){
                    $historical_validation_result = [
                        "code" => 403,
                        "message" => "PO No. already exist in the company and supplier",
                            "data" => null,
                    ];
                }else{

                    $historical_validation_result = 'Validated';
                }
            }

            if($fields['document_amount'] > $po_validation_total_amount){
                $response =  [
                    "code" => 400,
                    "message" => "Document Amount is higher than PO Amount",
                    "data" => null,
                ];
            }else{

            }

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
