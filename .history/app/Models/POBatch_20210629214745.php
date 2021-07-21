<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POBatch extends Model
{
    use HasFactory;

    protected $table = 'p_o_batches';

    protected $fillable = [
        'po_no',
        'po_no',
        'po_amount',
        'po_qty'
    ];

}
