<?php

namespace App\Methods;

use App\Methods\GenericMethod;

use App\Models\Transaction;
use App\Models\POBatch;
use App\Models\RRBatch;
use App\Models\ReferrenceBatch;
use App\Models\ReferrenceGroupBatches;
use App\Models\POGroupBatches;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionValidationMethod
 {
    // public static

    //  MAIN PAD Transactions
   public static function padValidation($fields,$tag_id,$date_requested,$status,$transaction_id){

        if(isset($fields['document_no'])){


            if (GenericMethod::validateIfDocumentNoExist($fields['document_no']) > 0){
                $response = [
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


                    if (GenericMethod::validateIfPONoExist($fields['payment_type'],$fields['company_id'],$fields['supplier_id'],$po_no) > 0){
                        $duplicate_po_array[] =  $po_no;
                    }
                }
                // Compile existed PO Number
                if(isset($duplicate_po_array)){
                    $response = [
                        "code" => 403,
                        "message" => "PO No. already exist in the company and supplier",
                            "data" => $duplicate_po_array,
                    ];
                }else{
                    $response = 'Validated';
                }

                if($fields['document_amount'] != $po_validation_total_amount){
                    $response =  [
                        "code" => 400,
                        "message" => "Document amount must be equal to total PO Amount",
                        "data" => null,
                    ];
                }else{

                    if($response == 'Validated'){

                        // INSERT PO DETAILS

                        for($i=0;$i<$po_count;$i++){
                            $po_object = $fields['po_group'][$i];
                            $po_object = (object) $po_object;

                            $po_no =  $po_object->po_no;
                            $po_amount =(float) str_replace(',', '', $po_object->po_amount);
                            $po_qty =(float) str_replace(',', '', $po_object->po_qty);
                            $po_total_amount = $po_total_amount+$po_amount;
                            $po_total_qty = $po_total_qty+$po_qty;

                            $insert_po_group = POGroupBatches::create([
                                'tag_id' => $tag_id
                                , "po_no" => $po_no
                            ]);

                            $insert_po_batch = POBatch::create([
                                'tag_id' => $tag_id,
                                'po_no' => $po_no
                                , "po_amount" => $po_amount
                                , "po_qty" => $po_qty
                            ]);

                        }

                        // INSERT TRANSACTION DETAILS
                        $new_transaction = Transaction::create([
                        'transaction_id' => $transaction_id
                        , "users_id" => $fields['users_id']
                        , "id_prefix" => $fields['id_prefix']
                        , "id_no" => $fields['id_no']
                        , "first_name" => $fields['first_name']
                        , "middle_name" => $fields['middle_name']
                        , "last_name" => $fields['last_name']
                        , "suffix" => $fields['suffix']
                        , "department" => $fields['department']
                        , "document_id" => $fields['document_id']
                        , "document_type" => $fields['document_type']
                        , "payment_type" => $fields['payment_type']
                        , "category_id" => $fields['category_id']
                        , "category" => $fields['category']
                        , "company_id" => $fields['company_id']
                        , "company" => $fields['company']
                        , "document_no" => $fields['document_no']
                        , "supplier_id" => $fields['supplier_id']
                        , "supplier" => $fields['supplier']
                        , "document_date" => $fields['document_date']
                        , "document_amount" => $fields['document_amount']
                        , "remarks" => $fields['remarks']
                        , "po_total_amount" => $po_total_amount
                        , "po_total_qty" => $po_total_qty
                        , "tag_id" => $tag_id
                        , "date_requested" => $date_requested
                        , "status" => $status,
                        ]);

                        $response = [
                            "code" => 200,
                            "message" => "Succefully Created",
                            "data" => $new_transaction,
                        ];

                    }else{
                        $response = $response;
                    }
                }

            }
            $response = $response;
        }else{
            $response = [
                "code" => 404,
                "message" => "Document number is null",
                "data" => null,
            ];
        }
        return $response;
   }

   public static function prmValidation($fields,$tag_id,$date_requested,$status,$transaction_id){

    if(isset($fields['document_no'])){

        if (GenericMethod::validateIfDocumentNoExist($fields['document_no']) > 0){
            $response = [
                "code" => 403,
                "message" => "Document No. already exist",
                    "data" => null,
            ];
        }else{

            if($fields['payment_type'] == 'FULL'){

                $po = $fields['po_group'];
                $referrence_group = $fields['referrence_group'];

                if((!empty($po)) && ((empty($referrence_group) )) ){

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

                        if (GenericMethod::validateIfPONoExist($fields['payment_type'],$fields['company_id'],$fields['supplier_id'],$po_no) > 0){
                            $duplicate_po_array[] =  $po_no;
                        }else if (GenericMethod::validateIfPONoExistInDifferentSupplier($fields['payment_type'],$fields['company_id'],$fields['supplier_id'],$po_no) > 0){
                            $duplicate_po_array[] =  $po_no;
                        }
                    }

                        $response = $duplicate_ref_array;

                }else if((empty($po)) && ((!empty($referrence_group) )) ){
                    // $response = 'REFERRENCE FULL';

                        $ref_count = count($referrence_group);
                        $ref_validation_count = count($referrence_group);

                        $ref_validation_total_amount = 0;
                        $ref_validation_total_qty = 0;
                        $ref_validation_total_qty = 0;

                        $ref_total_amount = 0;
                        $ref_total_qty = 0;
                        $ref_total_qty = 0;

                        for($i=0;$i<$ref_validation_count;$i++){

                            $ref_validation_object = $fields['referrence_group'][$i];
                            $ref_validation_object = (object) $ref_validation_object;
                            $ref_no =  $ref_validation_object->referrence_no;

                            $ref_validation_amount =(float) str_replace(',', '', $ref_validation_object->referrence_amount);
                            $ref_validation_qty =(float) str_replace(',', '', $ref_validation_object->referrence_qty);
                            $ref_validation_total_amount = $ref_validation_total_amount+$ref_validation_amount;
                            $ref_validation_total_qty = $ref_validation_total_qty+$ref_validation_qty;

                            if (GenericMethod::validateIfRefNoExist($fields['payment_type'],$fields['company_id'],$fields['supplier_id'],$ref_no) > 0){
                                $duplicate_ref_array[] =  $ref_no;
                            }
                        }

                        $response = $duplicate_ref_array;


                }else if((!empty($po)) && ((!empty($referrence_group) )) ){
                    $response = 'PO & REFERRENCE FULL';
                }else{
                    $response = [
                        "code" => 404,
                        "message" => "PO & Referrence number is empty",
                        "data" => null,
                    ];
                }




            }else if($fields['payment_type'] == 'PARTIAL'){
                $response = 'PARTIAL';
            }else{
                $response =  [
                    "code" => 404,
                    "message" => "Payment Type does not exist",
                    "data" => null,
                ];
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
