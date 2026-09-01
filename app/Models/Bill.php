<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use NumberToWords\NumberToWords;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'billdate';
    protected $primaryKey = 'dateid';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'bill_id',
        'customer_id',
        'bill_date',
        'discount',
        'size',
        'transport',
        'package'
    ];

    protected $casts = [
        'bill_date' => 'date',
        'discount' => 'decimal:2',
        'transport' => 'decimal:2',
        'package' => 'decimal:2'
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(BillItem::class, 'bill_id', 'bill_id');
    }

    public function note()
    {
        return $this->hasOne(Note::class, 'bill_id', 'bill_id');
    }

    // Accessors for calculated fields
    public function getSubtotalAttribute()
    {
        return $this->items->sum('total');
    }

    public function getDiscountAmountAttribute()
    {
        return $this->subtotal * ($this->discount / 100);
    }

    public function getAfterDiscountAttribute()
    {
        return $this->subtotal - $this->discount_amount;
    }

    public function getGstAmountAttribute()
    {
        // Check if Punjab with GST or other state with GST
        $customer = $this->customer;
        if ($customer && $customer->gstnumber) {
            $isPunjab = strtolower($customer->state) == 'punjab';
            $rate = $isPunjab ? 2.5 : 5; // CGST+SGST = 2.5% each or IGST = 5%
            return $this->after_discount * ($rate / 100);
        }
        return 0;
    }

    public function getGrandTotalAttribute()
    {
        $total = $this->after_discount;
        
        // Add GST
        $type = $this->getTaxType();
        if ($type == 'cgst_sgst') {
            $total += ($this->cgst + $this->sgst);
        } elseif ($type == 'igst') {
            $total += $this->igst;
        }
        
        // Add transport and packaging
        $total += $this->transport + $this->package;
        
        return $total;
    }

    public function getTaxType()
    {
        $customer = $this->customer;
        if (!$customer || !$customer->gstnumber) {
            return 'no_gst';
        }
        return strtolower($customer->state) == 'punjab' ? 'cgst_sgst' : 'igst';
    }

    public function getTaxRate()
    {
        $type = $this->getTaxType();
        if ($type == 'no_gst') return 0;
        if ($type == 'igst') return 5;
        return 2.5; // CGST/SGST each
    }

    public function getCgstAttribute()
    {
        if ($this->getTaxType() != 'cgst_sgst') return 0;
        return $this->after_discount * ($this->getTaxRate() / 100);
    }

    public function getSgstAttribute()
    {
        if ($this->getTaxType() != 'cgst_sgst') return 0;
        return $this->after_discount * ($this->getTaxRate() / 100);
    }

    public function getIgstAttribute()
    {
        if ($this->getTaxType() != 'igst') return 0;
        return $this->after_discount * ($this->getTaxRate() / 100);
    }

    public function getAmountInWordsAttribute()
    {
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('en');
        
        // Get the whole number and decimal separately
        $amount = $this->grand_total;
        $whole = floor($amount);
        $decimal = round(($amount - $whole) * 100);
        
        $words = ucfirst($numberTransformer->toWords($whole));
        
        if ($decimal > 0) {
            $words .= ' Rupees ' . $numberTransformer->toWords($decimal) . ' Paise Only';
        } else {
            $words .= ' Rupees Only';
        }
        
        return $words;
    }

    private function convertNumberToWords($number)
    {
        // You can copy your existing PHP function here
        // or use a package like: composer require kwn/number-to-words
    }
}