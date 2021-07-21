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

    public static function validateIfPONoExist($company_id,$supplier,$po_no){
        $transactions = DB::table('transactions')
        ->where('company_id',$company_id);
        ->where('document_no',$doc_no);
        ->where('document_no',$doc_no);
        return $transactions->count();

    }



}
