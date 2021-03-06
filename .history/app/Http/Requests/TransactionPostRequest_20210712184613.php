<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
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
            , "referrence_group" => 'nullable'
            , "reason_id" => 'nullable'
            , "reason" => 'nullable'
            , "pcf_date" => 'nullable'
            , "pcf_letter" => 'nullable'
            , "utilities_from" => 'nullable'
            , "utilities_to" => 'nullable'

            // Additionals
            ,"po_total_amount"=> 'nullable'
            ,"po_total_qty"=> 'nullable'
            ,"rr_total_qty": null
            ,"referrence_total_amount": null
            ,"referrence_total_qty": null
            ,"balance_document_po_amount": 7000
            ,"balance_document_ref_amount": null
            ,"balance_po_ref_amount": null

        ];
    }
}
