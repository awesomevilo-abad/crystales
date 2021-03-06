<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionPostRequest;
use App\Http\Controllers\GenericController;
use App\Methods\GenericMethod;
use App\Methods\TransactionValidationMethod;
use App\Models\Transaction;
use App\Models\POBatch;
use App\Models\RRBatch;
use App\Models\ReferrenceBatch;
use App\Models\ReferrenceGroupBatches;
use App\Models\POGroupBatches;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::all();

        $result = collect();
        foreach($transactions as $transaction){

            $date_requested = date('Y-m-d',strtotime($transaction->created_at));


            // PO & RR
            $po_group = collect();
            $get_po = DB::table('p_o_batches as PB')
            ->where('PB.tag_id',$transaction->tag_id)
            ->get();

            foreach($get_po as $specific_po){
                $id = $specific_po->id;

                $rr_group = collect();
                $get_rr = DB::table('r_r_batches as RB')
                ->where('RB.po_batch_no',$id)
                ->get();

                foreach($get_rr as $specific_rr){
                    $rr_group->push([
                        "rr_no"=>$specific_rr->rr_code
                        ,"rr_qty"=>$specific_rr->rr_qty

                    ]);
                }
                $po_group->push([
                    "po_no"=>$specific_po->po_no,
                    "rr_group"=>$rr_group,
                    "po_amount"=>$specific_po->po_amount,
                    "po_qty"=>$specific_po->po_qty,
                ]);

            }
           // REFERRENCE
           $referrence_group = collect();
           $get_referrence = DB::table('referrence_batches')
           ->where('tag_id','=',$transaction->tag_id)
           ->get();

           foreach($get_referrence as $specific_refference){
              $referrence_group->push([
                "referrence_type"=>$specific_refference->referrence_type,
                "referrence_no"=>$specific_refference->referrence_no,
                "referrence_amount"=>$specific_refference->referrence_amount,
                "referrence_qty"=>$specific_refference->referrence_qty
              ]);
           }

        //    DOCUMENT CATEGORY



        //    return $transaction;
            $result->push(
                [
                'id'=>$transaction->id,
                'date_requested'=>$date_requested,
                'transaction_id'=>$transaction->transaction_id,
                'tag_id'=>$transaction->tag_id,
                'document_id'=>$transaction->document_id,
                'document_type'=>$transaction->document_type,
                'category_id'=>$transaction->category_id,
                'category'=>$transaction->category,
                'document_no'=>$transaction->document_no,
                'document_amount'=>$transaction->document_amount,
                'company_id'=>$transaction->company_id,
                'company'=>$transaction->company,
                'supplier_id'=>$transaction->supplier_id,
                'supplier'=>$transaction->supplier,
                'po_group'=>$po_group,
                'po_total_amount'=>$transaction->po_total_amount,
                'po_total_qty'=>$transaction->po_total_qty,
                'rr_total_qty'=>$transaction->rr_total_qty,
                "referrence_group"=>$referrence_group,
                'referrence_total_amount'=>$transaction->referrence_total_amount,
                'referrence_total_qty'=>$transaction->referrence_total_qty,
                'payment_type'=>$transaction->payment_type,
                'status'=>$transaction->status,
                'remarks'=>$transaction->remarks,
                'status_group_id'=>null,
                'pcf_date'=>$transaction->pcf_date,
                'pcf_letter'=>$transaction->pcf_letter,
                'date_from'=>$transaction->utilities_from,
                'date_to'=>$transaction->utilities_to,
                'balance_document_po_amount'=>$transaction->balance_document_po_amount,
                'balance_document_ref_amount'=>$transaction->balance_document_ref_amount,
                'balance_po_ref_amount'=>$transaction->balance_po_ref_amount

                ]);
        }
        return $result;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    //  SPECIAL FUNCTIONS

    public function getTransactionID($str)
    {

        $dep_initials = '';
        foreach (explode(' ', $str) as $word) {
            $dep_initials .= strtoupper($word[0]);
        }

        $transactions = DB::table('transactions')->where('transaction_id', 'like', '%' . $dep_initials . '%')
            ->select('transaction_id')->orderBy('id', 'DESC')->first();
        if (empty($transactions)) {
            $transaction_id = 0;
        } else {
            $transaction_id = preg_replace('/[^0-9.]+/', '', ($transactions->transaction_id));

        }
        return ($transaction_id);
    }
    public function getTransactionCode($str, $transaction_id)
    {
        $dep_initials = '';
        $transaction_no = '';
        if ($str == trim($str) && strpos($str, ' ') !== false) {
            // IF MORE THAN 1 WORD AND DEPARTMENT NAME (MANAGEMENT INFORMATION SYSTEMS)
            foreach (explode(' ', $str) as $word) {
                $dep_initials .= strtoupper($word[0]);
            }

            return $dep_initials . sprintf('%03d', ($transaction_id + 1));
        } else {
            // IF 1 WORD AND DEPARTMENT NAME (FINANCE)
            $dep_initials = strtoupper(mb_substr($str, 0, 3));

            $transactions = DB::table('transactions')->where('transaction_id', 'like', '%' . $dep_initials . '%')
                ->select('transaction_id')->orderBy('id', 'desc')->first();

            if (empty($transactions)) {
                // IF WALANG LAMAN ANG KEYWORD DITO IREREGISTER ANG KEYWORD (FIN,MIS,AUD...)
                $transaction_id = 0;
                return $dep_initials . sprintf('%03d', ($transaction_id + 1));
            } else {
                // IF MAY LAMAN ANG EXISTING NA ANG KEYWORD DOON SA TRANSACTION (FIN,MIS,AUD...)
                $transaction_code = preg_replace('/[^0-9.]+/', '', $transactions->transaction_id);

                if (empty($transaction_code)) {
                    return $dep_initials . sprintf('%03d', ($transaction_code + 1));
                } else {
                    $transaction_id = preg_replace('/[^0-9.]+/', '', ($transaction_code + 1));
                }
                return ($dep_initials . sprintf('%03d', ($transaction_id)));

            }

        }

    }
    public function referrenceCount($referrence_no)
    {
        $referrences = DB::table('referrences')->where('id', $referrence_no)->where('is_active', 1);
        return $referrences->count();

    }
    public function reasonCount($reason_id)
    {
        $reasons = DB::table('reasons')->where('id', $reason_id)->where('is_active', 1);
        return $reasons->count();

    }
    public function getTagID()
    {
        $transactions = DB::table('transactions')->select('tag_id')->orderBy('id', 'desc')->first();
        if (empty($transactions)) {
            $tag_id = 0;
        } else {
            $tag_id = $transactions->tag_id;
        }
        return ($tag_id + 1);
    }
    //  END SPECIAL FUNCTIONS

    public function store(TransactionPostRequest $request)
    {
        $fields = $request->validated();

        if (GenericMethod::countTableById('documents',$fields['document_id']) < 1) {
            $response = [
                "code" => 404,
                "message" => "Document ID is not registered on the masterlist",
                "data" => null,
            ];
        } elseif (isset($fields['category_id']) && GenericMethod::countTableById('categories',$fields['category_id']) < 1) {
            $response = [
                "code" => 404,
                "message" => "Category ID is not registered on the masterlist",
                "data" => null,
            ];

        } elseif (GenericMethod::countTableById('companies',$fields['company_id']) < 1) {
            $response = [
                "code" => 404,
                "message" => "Company ID is not registered on the masterlist",
                "data" => null,
            ];

        } elseif (GenericMethod::countTableById('suppliers',$fields['supplier_id']) < 1) {
            $response = [
                "code" => 404,
                "message" => "Supplier ID is not registered on the masterlist",
                "data" => null,
            ];
        }else{

            $tag_id = $this->getTagID();
            $date_requested = date('Y-m-d H:i:s');
            $status = "Pending";

            $transaction_id = $this->getTransactionID($fields['department']);
            $transaction_id = $this->getTransactionCode($fields['department'], $transaction_id);

            // NOTE: You don't really have to use floatval() here, it's just to prove that it's a legitimate float value.
            $fields['document_amount'] = floatval(str_replace(',', '', $fields['document_amount']));


            if(($fields['document_id'] == 7 AND strtoupper( $fields['document_type']) == "PAD")){

                $response =   TransactionValidationMethod::padValidation($fields,$tag_id,$date_requested,$status,$transaction_id);

            }
            else if(($fields['document_id'] == 2 AND strtoupper( $fields['document_type']) == "PRM")){
                $response =   TransactionValidationMethod::prmValidation($fields,$tag_id,$date_requested,$status,$transaction_id);
            }
            else if(($fields['document_id'] == 4 AND strtoupper( $fields['document_type']) == "CONTRACTOR'S BILLING")){
                $response =   TransactionValidationMethod::contractorsBillingValidation($fields,$tag_id,$date_requested,$status,$transaction_id);
            }
            else if(($fields['document_id'] == 5 AND strtoupper( $fields['document_type']) == "RECEIPT")){
                $response =  "RECEIPT TRANSACTION HERE";
            }
            else if(($fields['document_id'] == 6 AND strtoupper( $fields['document_type']) == "PAYROLL")){
                $response =  "PAYROLL TRANSACTION HERE";
            }
            else if(($fields['document_id'] == 8 AND strtoupper( $fields['document_type']) == "PCF")){
                $response =  "PCF TRANSACTION HERE";
            }
            else if(($fields['document_id'] == 9 AND strtoupper( $fields['document_type']) == "UTILITIES")){
                $response =  "UTILITIES TRANSACTION HERE";
            }
            else{
                $response = [
                    "code" => 422,
                    "message" => "Either document is not registered or document id and document type does not match",
                    "data" => null,
                ];
            }


        }

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function status_group(Request $request)
    {
        $status_group = DB::select(DB::raw("SELECT * FROM `transactions`
        LEFT JOIN taggings ON transactions.transaction_id=taggings.transaction_id
        LEFT JOIN gases ON transactions.tag_id=gases.tag_id
        LEFT JOIN filings ON transactions.tag_id=filings.tag_id
        LEFT JOIN associates ON transactions.tag_id=associates.tag_id
        LEFT JOIN specialists ON transactions.tag_id=specialists.tag_id
        LEFT JOIN matches ON transactions.tag_id=matches.tag_id
        LEFT JOIN return_vouchers ON transactions.tag_id=return_vouchers.tag_id
        LEFT JOIN approvers ON transactions.tag_id=approvers.tag_id
        LEFT JOIN cheque_creations ON transactions.tag_id=cheque_creations.tag_id
        LEFT JOIN cheque_tables ON transactions.transaction_id=cheque_tables.transaction_id LEFT JOIN cheque_infos ON cheque_tables.cheque_info_id=cheque_infos.id LEFT JOIN treasuries ON transactions.tag_id=treasuries.tag_id LEFT JOIN cheque_releaseds ON transactions.tag_id=cheque_releaseds.tag_id LEFT JOIN cheque_clearings ON transactions.tag_id=cheque_clearings.tag_id "));

        return $status_group;
    }
}
