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
        ->where('payment_type',$payment_type)
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

    public static function getPartialDataOfPO(){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('payment_type',$payment_type)
        ->where('company_id',$company_id)
        ->where('supplier_id',$supplier_id)
        ->where('po_no',$po_no)
        ->orderBy('balance_document_po_amount')
        ->get();
        return $transactions;
    }



}
