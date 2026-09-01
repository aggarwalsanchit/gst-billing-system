<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllProduct extends Model
{
    use HasFactory;

    protected $table = 'allproducts';
    protected $primaryKey = 'product_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'pnumber',
        'unit',
        'price',
        'hsn_code'
    ];

    // Relationships
    public function billItems()
    {
        return $this->hasMany(BillItem::class, 'product_id', 'product_id');
    }
}