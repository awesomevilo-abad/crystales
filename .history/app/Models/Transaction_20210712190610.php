<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'users_id'
        , 'id_prefix'
        , 'id_no'
        , 'first_name'
        , 'middle_name'
        , 'last_name'
        , 'suffix'
        , 'department'
        , 'transaction_id'
        , 'tag_id'
        , 'document_id'
        , 'document_type'
        , 'document_date'
        , 'category_id'
        , 'category'
        , 'company_id'
        , 'company'
        , 'supplier_id'
        , 'supplier'
        , 'po_total_amount'
        , 'po_total_qty'
        , 'rr_total_qty'
        , 'referrence_total_amount'
        , 'referrence_total_qty'
        , 'date_requested'
        , 'remarks'
        , 'payment_type'
        , 'status'
        , 'reason_id'
        , 'reason'
        , 'document_no'
        , 'document_amount'
        , 'pcf_date'
        , 'pcf_letter'
        , 'utilities_from'
        , 'utilities_to'
        
        ,"po_total_amount"
        ,"po_total_qty": null
        ,"rr_total_qty": null
        ,"referrence_total_amount": null
        ,"referrence_total_qty": null
        ,"balance_document_po_amount": 7000
        ,"balance_document_ref_amount": null
        ,"balance_po_ref_amount": null

    ];

    protected $casts = [
        // 'po_group' => 'array',
        'referrence_group' => 'array',
    ];


}
