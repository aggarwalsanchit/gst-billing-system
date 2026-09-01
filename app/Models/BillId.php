<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillId extends Model
{
    use HasFactory;

    protected $table = 'billid';

    protected $fillable = [
        'bill_id'
    ];
}