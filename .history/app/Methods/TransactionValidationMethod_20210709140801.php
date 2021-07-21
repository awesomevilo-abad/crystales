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

        for($i=0;$i<$po_validation_count;$i++){
            $po_validation_object = $fields['po_group'][$i];
            $po_validation_object = (object) $po_validation_object;
            $po_no =  $po_validation_object->po_no;
            $po_validation_amount =(float) str_replace(',', '', $po_validation_object->po_amount);
            $po_validation_qty =(float) str_replace(',', '', $po_validation_object->po_qty);
            $po_validation_total_amount = $po_validation_total_amount+$po_validation_amount;
            $po_validation_total_qty = $po_validation_total_qty+$po_validation_qty;

            if (validateIfDocumentNoExist::validateExistingDocNo($fields['document_type'],$fields['payment_type'],
                $fields['document_no']) > 0){

                $po_document_response = [
                    "code" => 403,
                    "message" => "Document No. already exist",
                        "data" => null,
                    ];
            }else if(TransactionValidationMethod::validateExistingPOInCompany($fields['payment_type'], $fields['company_id'],$po_no) > 0){
                $po_document_response = [
                    "code" => 403,
                    "message" => "PO Already exist in the company",
                    "data" => null,
                ];
            }else if(TransactionValidationMethod::validateExistingPOInSupplier($fields['payment_type'], $fields['supplier_id'],$po_no) > 0){
                $po_document_response = [
                    "code" => 403,
                    "message" => "PO Already exist in the supplier",
                    "data" => null,
                ];
            }else if(TransactionValidationMethod::validateExistingDocNoAndDocDate($fields['payment_type'], $fields['document_no'],$fields['document_date']) > 0){
                $po_document_response = [
                    "code" => 403,
                    "message" => "Document number and date are already exist",
                    "data" => null,
                ];
            }else if(TransactionValidationMethod::validateExistingDocNoAndPONo($fields['payment_type'], $fields['document_no'],$po_no) > 0){
                $po_document_response = [
                    "code" => 403,
                    "message" => "Document number and PO number are already exist",
                    "data" => null,
                ];
            }else{

                $po_document_response = "Validated";
            }

        }

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
