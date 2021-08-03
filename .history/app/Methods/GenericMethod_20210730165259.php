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

    public static function validateIfPOExistInOtherDocNo($payment_type,$company_id,$supplier_id,$po_no,$used_tag_id){


        $transactions = DB::table('transactions')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        // ->where('transactions.payment_type',$payment_type)
        ->where('transactions.company_id',$company_id)
        ->where('transactions.supplier_id',$supplier_id)
        ->where('p_o_batches.po_no',$po_no)
        ->whereIn('transactions.tag_id',$used_tag_id);
        return $transactions->count();
    }

    public static function getTagIDUsingPONo($payment_type,$company_id,$supplier_id,$po_no){
        $transactions = DB::table('transactions')
        ->select('transactions.tag_id')
        ->leftJoin('p_o_batches','transactions.tag_id','=','p_o_batches.tag_id')
        // ->where('transactions.payment_type',$payment_type)
        ->where('transactions.company_id',$company_id)
        ->where('transactions.supplier_id',$supplier_id)
        ->where('p_o_batches.po_no',$po_no)
        ->orderBy('transactions.tag_id','desc')
        ->get();
        return $transactions;
    }

    public static function getUsedPOFromDB($payment_type,$company_id,$supplier_id,$po_no,$document_amount){
        $transactions = DB::table('p_o_batches')
        ->where('tag_id', '=', function ($query) use ($po_no) {
            $query->selectRaw('tag_id')->from('p_o_batches')
            ->where('po_no',$po_no)
            ->orderByDesc('id')
            ->limit(1);
        })
        ->get('po_no');

        return $transactions;
    }

    public static function getFullname($fname,$mname,$lname,$suffix){
        $fullname = $fname.' '.strtoupper($mname[0]).'. '.$lname.' '.$suffix;
        return $fullname;
    }

    public static function setGroup($group,$field1,$field2){


        $list = collect();
        $total = 0;
        $group_details = collect();
        foreach($group as $specific_group){
            $list->push($specific_group->$field1);
            $total = $total+$specific_group->$field2;

        }

        $group_details->push([
            "".$field1."_list" => $list,
            "total_".$field2."" =>$total
        ]);


        return $group_details;

    }

}
