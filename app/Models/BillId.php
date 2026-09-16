<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillId extends Model
{
    use HasFactory;

    protected $table = 'billid';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'bill_id',
        'financial_year',
    ];

    protected $casts = [
        'bill_id' => 'integer',
    ];
}