<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    use HasFactory;

    protected $table = 'demo';
    protected $primaryKey = 'demo_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'bill_id',
        'Product',
        'pnumber',
        'qty',
        'unit',
        'price',
        'nsn_code',
        'total',
        'database_id',
        'product_id'
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'database_id', 'database_id');
    }

    public function allProduct()
    {
        return $this->belongsTo(AllProduct::class, 'product_id', 'product_id');
    }

    // Mutator to auto-calculate total
    public function setQtyAttribute($value)
    {
        $this->attributes['qty'] = $value;
        $this->attributes['total'] = $value * $this->price;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value;
        if (isset($this->attributes['qty'])) {
            $this->attributes['total'] = $this->attributes['qty'] * $value;
        }
    }
}