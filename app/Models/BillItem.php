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
        'discount',      // per-item discount %
        'net_total',     // qty × price − item discount
        'nsn_code',
        'total',         // gross = qty × price
        'database_id',
        'product_id',
    ];

    protected $casts = [
        'qty'       => 'float',
        'price'     => 'float',
        'discount'  => 'float',
        'total'     => 'float',
        'net_total' => 'float',
    ];

    // ---------- Relationships ----------

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

    // ---------- Computed Accessors ----------

    /**
     * Gross total = qty × price. Same as `total` column.
     */
    public function getGrossTotalAttribute()
    {
        return (float) $this->qty * (float) $this->price;
    }

    /**
     * Discount amount for this item (in rupees).
     */
    public function getDiscountAmountAttribute()
    {
        return $this->gross_total * ((float) $this->discount / 100);
    }

    /**
     * Net total = gross − item discount.
     * Prefer stored `net_total` if present; otherwise compute.
     */
    public function getNetTotalCalculatedAttribute()
    {
        if (!empty($this->attributes['net_total'])) {
            return (float) $this->attributes['net_total'];
        }
        return $this->gross_total - $this->discount_amount;
    }

    // ---------- Auto-compute gross and net on every save ----------

    protected static function booted()
    {
        static::saving(function ($item) {
            $qty   = (float) ($item->qty ?? 0);
            $price = (float) ($item->price ?? 0);
            $disc  = (float) ($item->discount ?? 0);

            $gross = $qty * $price;
            $net   = $gross - ($gross * $disc / 100);

            $item->total     = $gross;
            $item->net_total = $net;
        });
    }
}