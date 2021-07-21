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

    public static function validateIfPONoExist($company_id,$supplier_id,$po_no){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('company_id',$company_id)
        ->where('supplier_id',$supplier_id)
        ->where('po_no',$po_no);
        return $transactions->count();
    }

    public static function validateIfRefNoExist($company_id,$supplier_id,$ref_no){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('company_id',$company_id)
        ->where('supplier_id',$supplier_id)
        ->where('po_no',$po_no);
        return $transactions->count();
    }
    public static function validateIfRefNoExist($company_id,$supplier_id,$ref_no){
        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        ->where('company_id',$company_id)
        ->where('supplier_id',$supplier_id)
        ->where('po_no',$po_no);
        return $transactions->count();
    }



}
