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
        , 'po_group_id'
        , 'referrence_id'
        , 'referrence_type'
        , 'date_requested'
        , 'remarks'
        , 'payment_type'
        , 'status'
        , 'reason_id'
        , 'reason'
        , 'document_no'
        , 'document_amount',

    ];
}
