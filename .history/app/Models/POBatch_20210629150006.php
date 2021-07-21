<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POBatch extends Model
{
    use HasFactory;

    protected $table = 'p_o_batches';

    protected $fillable = [

    ];

    public function transactions(){
        return $this->belongsToMany(Transaction::class, 'p_o_group_batches');
    }
}
