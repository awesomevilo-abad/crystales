<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POBatch extends Model
{
    use HasFactory;
    protected $table = 'p_o_batches';

    public function transaction(){
        
    }
}
