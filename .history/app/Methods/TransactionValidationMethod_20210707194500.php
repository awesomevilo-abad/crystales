<?php

namespace App\Methods;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionValidationMethod
 {
    //  PAD Transactions
    public static function validateExistingTransaction($document_type,$payment_type,
    $document_no,$document_date,$document_amount,$company_id,$supplier_id,$po_no
    ){
        $transaction = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('document_type', $document_type)
        ->where('payment_type', $payment_type)
        ->where('document_no', $document_no)
        ->where('document_date', $document_date)
        ->where('document_amount', $document_amount)
        ->where('company_id', $company_id)
        ->where('supplier_id', $supplier_id)
        ->where('po_no','=',$po_no);

        return $transaction->count();
    }
    public static function validateExistingPOInCompany( $payment_type, $company_id,$po_no){
        $transaction = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('payment_type', $payment_type)
        ->where('company_id', $company_id)
        ->where('po_no','=',$po_no);

        return $transaction->count();
    }
    public static function validateExistingPOInSupplier( $payment_type, $supplier_id,$po_no){
        $transaction = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('payment_type', $payment_type)
        ->where('supplier_id', $supplier_id)
        ->where('po_no','=',$po_no);

        return $transaction->count();
    }
    public static function validateExistingDocNoAndDocDate( $payment_type, $document_no,$document_date){
        $transaction = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('payment_type', $payment_type)
        ->where('document_no', $document_no)
        ->where('document_date', $document_date);

        return $transaction->count();
    }
    public static function validateExistingDocNoAndPONo( $payment_type, $document_no,$po_no){
        $transaction = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('payment_type', $payment_type)
        ->where('document_no', $document_no)
        ->where('po_no', $po_no);

        return $transaction->count();
    }


    // PRM Transaction
    public static function validateExistingPRMTransaction($document_type,$payment_type,
    $document_no,$document_date,$document_amount,$company_id,$supplier_id,$po_no
    ){
        $transaction = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('document_type', $document_type)
        ->where('payment_type', $payment_type)
        ->where('document_no', $document_no)
        ->where('document_date', $document_date)
        ->where('document_amount', $document_amount)
        ->where('company_id', $company_id)
        ->where('supplier_id', $supplier_id)
        ->where('po_no','=',$po_no);

        return $transaction->count();
    }
 }
