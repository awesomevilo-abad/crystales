<?php

namespace App\Methods;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GenericMethod{

    public static function countTableById($table,$id){
        $table = DB::table($table)->where('id', $id)->where('is_active', 1);
        return $table->count();
    }

    public static function validateIfDocumentNoExist($doc_no){
        $transactions = DB::table('transactions')
        ->where('document_no',$doc_no);
        return $transactions->count();

    }

    public static function validateIfPONoExist($payment_type,$company_id,$supplier_id,$po_no){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        // ->where('payment_type',$payment_type)
        ->where('company_id',$company_id)
        ->where('supplier_id',$supplier_id)
        ->where('po_no',$po_no);
        return $transactions->count();
    }

    public static function validateIfPONoExistInDifferentSupplier($payment_type,$company_id,$supplier_id,$po_no){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('payment_type',$payment_type)
        ->where('company_id',$company_id)
        ->where('po_no',$po_no);
        return $transactions->count();
    }
    public static function validateIfRefNoExist($payment_type,$company_id,$supplier_id,$ref_no){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->leftJoin('referrence_batches','transactions.tag_id','=','referrence_batches.tag_id')
        ->where('payment_type',$payment_type)
        ->where('company_id',$company_id)
        ->where('supplier_id',$supplier_id)
        ->where('referrence_no',$ref_no);
        return $transactions->count();
    }

    public static function getBalanceAmountOfDocumentPO($payment_type,$company_id,$supplier_id,$po_no,$document_amount){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('transactions.payment_type',$payment_type)
        ->where('transactions.company_id',$company_id)
        ->where('transactions.supplier_id',$supplier_id)
        ->where('p_o_batches.po_no',$po_no)
        ->orderBy('transactions.id','desc')
        ->take(1)
        ->get('transactions.balance_document_po_amount');

        return $transactions;

    }

    public static function getUsedPO($payment_type,$company_id,$supplier_id,$po_no,$document_amount){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('transactions.payment_type',$payment_type)
        ->where('transactions.company_id',$company_id)
        ->where('transactions.supplier_id',$supplier_id)
        ->where('p_o_batches.po_no',$po_no)
        ->where('transactions.balance_document_po_amount','<',$document_amount);
        return $transactions->count();
    }

    public static function getPOWithInsufficientAmont($payment_type,$company_id,$supplier_id,$po_no,$document_amount){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('payment_type',$payment_type)
        ->where('company_id',$company_id)
        ->where('supplier_id',$supplier_id)
        ->where('po_no',$po_no)
        ->where('balance_document_po_amount','<',$document_amount)->get();
        return $transactions;
    }

    public static function validateIfDocumentAmountIsGreaterThanPO($po_total_amount,$document_amount,$po_additional_pos){
        if ($po_total_amount < $document_amount){
            $response = [
                "code" => 403,
                "message" => "Document amount is higher than the old balance and total amount of additional POs ",
                    "data" => $po_additional_pos,
            ];
        }else{
                $response = "Insert Additional PO";
        }

        return $response;

    }

    public static function validateIfPOExistInOtherDocNo($payment_type,$company_id,$supplier_id,$po_no){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        // ->where('transactions.payment_type',$payment_type)
        ->where('transactions.company_id',$company_id)
        ->where('transactions.supplier_id',$supplier_id)
        ->where('p_o_batches.po_no',$po_no);
        return $transactions->count();
    }

    public static function getTagIDUsingPONo($payment_type,$company_id,$supplier_id,$po_no){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        // ->where('transactions.payment_type',$payment_type)
        ->where('transactions.company_id',$company_id)
        ->where('transactions.supplier_id',$supplier_id)
        ->where('p_o_batches.po_no',$po_no)
        ->orderBy('transactions.tag_id','desc')
        ->select('transactions.tag_id')
        ->get();
        return $transactions;
    }



}
