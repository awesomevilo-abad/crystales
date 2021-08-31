<?php

namespace App\Methods;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
// For Pagination with Collection
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use App\Models\User;

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

    public static function unique_values_in_array_based_on_key($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach($array as $val) {
            if (!in_array($val->$key, $key_array)) {
                $key_array[$i] = $val->$key;
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return array_values($temp_array);
    }

    public static function paginateme($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public static function getUserDetailsById($id){
        $user_details = User::find($id);

        $document_details = DB::select( DB::raw('SELECT documents.id AS "masterlist_document_id",
        categories.is_active,
        documents.document_type AS "document_name",
        IFNULL(categories.id,"no category")  AS "masterlist_category_id",
        categories.name AS "category_name",
        user_document_category.user_id AS "user_id" ,
        user_document_category.document_id AS "user_document_id",
        user_document_category.category_id AS "user_category_id"
        FROM documents
        LEFT JOIN document_categories
        ON documents.id = document_categories.document_id
        LEFT JOIN categories
        ON document_categories.category_id = categories.id
        LEFT JOIN user_document_category
        ON document_categories.document_id = user_document_category.document_id AND document_categories.category_id = user_document_category.category_id
        LEFT JOIN users
        ON user_document_category.user_id = users.id
        -- WHERE  documents.is_active = 1
        -- AND document_categories.is_active = 1
        WHERE  IFNULL(categories.is_active,0) = (IF((IFNULL(categories.id,"no category")) = "no category",0, 1))
        -- AND user_document_category.is_active = 1
        -- AND users.is_active = 1
        ORDER by documents.id,categories.id') );

        $user_document_details = collect();
        $document_types = collect();
        $categories = collect();
        $categories_per_doc = array();

        // return $document_details;
        foreach($document_details as $specific_document_details){


            if(($specific_document_details->masterlist_document_id == $specific_document_details->user_document_id) AND
                ($specific_document_details->masterlist_category_id == $specific_document_details->user_category_id)){
                    $document_status = true;
                    $category_status = true;

            }else if(($specific_document_details->masterlist_document_id == $specific_document_details->user_document_id) AND
            ($specific_document_details->masterlist_category_id != $specific_document_details->user_category_id)){
                $document_status = true;
                $category_status = false;

            }else if(($specific_document_details->masterlist_document_id != $specific_document_details->user_document_id) AND
            ($specific_document_details->masterlist_category_id != $specific_document_details->user_category_id)){
                $document_status = true;
                $category_status = false;

            }else{
                $document_status = false;
            }

            $categories->push([
                "user_document_id"=>$specific_document_details->user_document_id,
                "user_category_id"=>$specific_document_details->user_category_id,
                "document_id"=>$specific_document_details->masterlist_document_id,
                "category_id"=>$specific_document_details->masterlist_category_id,
                "category_name"=>$specific_document_details->category_name,
                "category_status"=>$category_status,
            ]);

        }



        $final_document_details = GenericMethod::unique_values_in_array_based_on_key($document_details,'masterlist_document_id');

        // return $final_document_details;
        return $final_document_details;
        foreach($final_document_details as $final_specific_document_details){

            if(($specific_document_details->masterlist_document_id == $specific_document_details->user_document_id)
                $document_status = true;
                foreach($categories as $specific_categories){

                    if($specific_categories['document_id'] == $final_specific_document_details->masterlist_document_id){
                        array_push($categories_per_doc, array(
                            "category_id"=>$specific_categories['category_id'],
                            "category_name"=>$specific_categories['category_name'],
                            "category_status"=>$specific_categories['category_status']),
                        );
                    }else{
                    }
                }
            $document_types->push([
                "document_id"=>$final_specific_document_details->masterlist_document_id,
                "document_name"=>$final_specific_document_details->document_name,
                "document_status"=>$document_status,
                "document_categories"=>$categories_per_doc

            ]);

            $categories_per_doc = array();

        }

        $user_document_details->push([
            "id"=> $user_details->id,
            "id_prefix"=> $user_details->id_prefix,
            "id_no"=> $user_details->id_prefix,
            "role"=> $user_details->role,
            "first_name"=> $user_details->first_name,
            "middle_name"=> $user_details->middle_name,
            "last_name"=> $user_details->last_name,
            "suffix"=> $user_details->suffix,
            "department"=> $user_details->department,
            "position"=> $user_details->position,
            "permissions"=> $user_details->permissions,
            "document_types"=> $document_types,
            "username"=> $user_details->username,
            "is_active"=> 1,
        ]);

        $result = $user_document_details;


        if (!$result) {
            return [
                'error_message' => 'Data Not Found',
            ];
        }

        return $result;
    }
}
