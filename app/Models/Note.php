<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $table = 'note';
    protected $primaryKey = 'note_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'despatch',
        'bill_id',
        'customer_id',
        'bill_date',
        'deliverynote',
        'grno'
    ];

    protected $casts = [
        'bill_date' => 'date'
    ];

    // Relationships
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'bill_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}