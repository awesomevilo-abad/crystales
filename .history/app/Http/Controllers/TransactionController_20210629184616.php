<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\POBatch;
use App\Models\RRBatch;
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
        return Transaction::all();
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

    public function addZeroPrefix($doc_no)
    {
        return sprintf('%06d', $doc_no);
    }

    public function documentCount($doc_id)
    {
        $documents = DB::table('documents')->where('id', $doc_id)->where('is_active', 1);
        return $documents->count();

    }

    public function categoryCount($cat_id)
    {
        $categories = DB::table('categories')->where('id', $cat_id)->where('is_active', 1);
        return $categories->count();

    }

    public function companyCount($company_id)
    {
        $companies = DB::table('companies')->where('id', $company_id)->where('is_active', 1);
        return $companies->count();

    }

    public function supplierCount($supplier_id)
    {
        $suppliers = DB::table('suppliers')->where('id', $supplier_id)->where('is_active', 1);
        return $suppliers->count();

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

    public function getPoGroupNo(){

    }

    public function store(Request $request)
    {
        $fields = $request->validate([

            // AUTOMATIC USER DETAILS BASED ON LOGIN
            "users_id" => 'required'
            , "id_prefix" => 'required'
            , "id_no" => 'required'
            , "first_name" => 'required'
            , "middle_name" => 'required'
            , "last_name" => 'required'
            , "suffix" => 'nullable'
            , "department" => 'required'

            // SELECTED DOCUMENT TYPE
            , "document_id" => 'required'
            , "document_type" => 'required'

            // SELECTED CATEGORY (CONDITIONAL)
            , "category_id" => 'nullable'
            , "category" => 'nullable'

            // PAYMENT TYPE BASED ON FE SELECTED
            , "payment_type" => 'required'

            // SELECTED COMPANY
            , "company_id" => 'required'
            , "company" => 'required'

            // INPUTTED DOCUMENT NO
            , "document_no" => 'required'

            // SELECTED SUPPLIER
            , "supplier_id" => 'required'
            , "supplier" => 'required'

            // INPUTTED DOCUMENT DATE & AMOUNT
            , "document_date" => 'nullable'
            , "document_amount" => 'nullable'

            // OPTIONAL(ADD REMARKS)
            , "remarks" => 'nullable'

            // CREATE PO GROUP BATCH ID (LINK TO PO BATCHES TABLE WITH AMOUNT)
            , "po_group" => 'nullable'

            // CREATE REF GROUP BATCH ID (LINK TO REF BATCHES TABLE WITH AMOUNT)
            , "referrence_group" => 'nullable',

        ]);

        if ($this->documentCount($fields['document_id']) < 1) {
            $response = [
                "code" => 404,
                "message" => "Document ID is not registered on the masterlist",
                "data" => null,
            ];
        } elseif (isset($fields['category_id']) && $this->categoryCount($fields['category_id']) < 1) {
            $response = [
                "code" => 404,
                "message" => "Category ID is not registered on the masterlist",
                "data" => null,
            ];

        } elseif ($this->companyCount($fields['company_id']) < 1) {
            $response = [
                "code" => 404,
                "message" => "Company ID is not registered on the masterlist",
                "data" => null,
            ];

        } elseif ($this->supplierCount($fields['supplier_id']) < 1) {
            $response = [
                "code" => 404,
                "message" => "Supplier ID is not registered on the masterlist",
                "data" => null,
            ];

        } else {

            $tag_id = $this->getTagID();
            $date_requested = date('Y-m-d H:i:s');
            $status = "Pending";

            $transaction_id = $this->getTransactionID($fields['department']);
            $transaction_id = $this->getTransactionCode($fields['department'], $transaction_id);
            $systemize_doc_no = $this->addZeroPrefix($fields['document_no']);

// ---------------------------------------------------

            $po = $fields['po_group'];
            $po_count = count($po);

            $po_total_amount = 0;
            $po_total_qty = 0;

            for($i=0;$i<$po_count;$i++){
                $po_object = $fields['po_group'][$i];
                $po_object = (object) $po_object;


                $po_no =  $po_object->po_no;
                $po_amount =(float) str_replace(',', '', $po_object->po_amount);
                $po_qty =(float) str_replace(',', '', $po_object->po_qty);
                $po_total_amount = $po_total_amount+$po_amount;
                $po_total_qty = $po_total_qty+$po_qty;

                // $insert_po_group = POGroupBatches::create([
                //     'tag_id' => $tag_id
                //     , "po_no" => $po_no
                // ]);

                // $insert_po_batch = POBatch::create([
                //     'po_no' => $po_no
                //     , "po_amount" => $po_amount
                // ]);

                $rr_nos = $po_object->rr_nos;

                foreach($rr_nos as $rr){


                    // $transaction = DB::table('p_o_batches as PB')
                    // ->leftJoin('p_o_group_batches as PGB','PB.po_no','=','PGB.po_no')
                    // ->where('PB.po_no',$po_no)
                    // ->where('PGB.tag_id',$tag_id)
                    // ->get('PB.id');


                    // $po_batch_no = $transaction[0]->id;

                    // $insert_rr_batch = RRBatch::create([
                    //     'po_batch_no' => $po_batch_no
                    //     , "rr_code" => $rr
                    // ]);

                }

            }
            echo $po_total_qty;
            // $new_transaction = Transaction::create([
            //     'transaction_id' => $transaction_id
            //     , "users_id" => $fields['users_id']
            //     , "id_prefix" => $fields['id_prefix']
            //     , "id_no" => $fields['id_no']
            //     , "first_name" => $fields['first_name']
            //     , "middle_name" => $fields['middle_name']
            //     , "last_name" => $fields['last_name']
            //     , "suffix" => $fields['suffix']
            //     , "department" => $fields['department']
            //     , "document_id" => $fields['document_id']
            //     , "document_type" => $fields['document_type']
            //     , "payment_type" => $fields['payment_type']
            //     , "category_id" => $fields['category_id']
            //     , "category" => $fields['category']
            //     , "company_id" => $fields['company_id']
            //     , "company" => $fields['company']
            //     , "document_no" => $fields['document_no']
            //     , "supplier_id" => $fields['supplier_id']
            //     , "supplier" => $fields['supplier']
            //     , "document_date" => $fields['document_date']
            //     , "document_amount" => $fields['document_amount']
            //     , "remarks" => $fields['remarks']
            //     , "po_total_amount" => $po_total_amount
            //     , "referrence_group" => $request['referrence_group']
            //     , "tag_id" => $tag_id
            //     , "date_requested" => $date_requested
            //     , "status" => $status,
            // ]);
            // $response = 'Succesfully Created!';


            // // PAD VALIDATION
            // $transaction_exist = DB::table('transactions')
            //     ->where('document_type', $fields['document_type'])
            //     ->where('payment_type', $fields['payment_type'])
            //     ->where('document_no', $fields['document_no'])
            //     ->where('document_date', $fields['document_date'])
            //     ->where('document_amount', $fields['document_amount'])
            //     ->where('company', $fields['company'])
            //     ->where('supplier', $fields['supplier'])
            // // ->whereJsonContains('po_group', $fields['po_group'])
            // // ->whereJsonContains('referrence_group', $fields['referrence_group'])
            //     ->get();

            // $po_exist_in_a_company = DB::table('transactions')
            //     ->where('document_type', $fields['document_type'])
            //     ->where('payment_type', $fields['payment_type'])
            //     ->where('company', $fields['company'])
            // // ->whereJsonContains('po_group', $fields['po_group'])
            //     ->get();

            // if ($transaction_exist) {
            //     $result = 'Transaction Exist';
            // } elseif ($po_exist_in_a_company) {

            //     $result = 'PO Exist In a Company';
            // }
            // print_r($result);



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
}
