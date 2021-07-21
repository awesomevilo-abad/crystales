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

            $duplicate_po = (array);

            for($i=0;$i<$po_validation_count;$i++){
                $po_validation_object = $fields['po_group'][$i];
                $po_validation_object = (object) $po_validation_object;
                $po_no =  $po_validation_object->po_no;

                $po_validation_amount =(float) str_replace(',', '', $po_validation_object->po_amount);
                $po_validation_qty =(float) str_replace(',', '', $po_validation_object->po_qty);
                $po_validation_total_amount = $po_validation_total_amount+$po_validation_amount;
                $po_validation_total_qty = $po_validation_total_qty+$po_validation_qty;

                if (GenericMethod::validateIfPONoExist($fields['company_id'],$fields['supplier_id'],$po_no) > 0){
                    $duplicate_po = array_push($duplicate_po,$po_no);


                }else{
                    // $duplicate_po = '';

                }
            }

            if(isset($duplicate_po)){
                $duplicate_po = explode(',', $duplicate_po);

                $response = [
                    "code" => 403,
                    "message" => "PO No. already exist in the company and supplier",
                        "data" => $duplicate_po,
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

 }
