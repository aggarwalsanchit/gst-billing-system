<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'gstnumber',
        'adharno',
        'panno',
        'state'
    ];

    // Relationships
    public function bills()
    {
        return $this->hasMany(Bill::class, 'customer_id', 'customer_id');
    }

    public function billItems()
    {
        return $this->hasMany(BillItem::class, 'customer_id', 'customer_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'customer_id', 'customer_id');
    }
}