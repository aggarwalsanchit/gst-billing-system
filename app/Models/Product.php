<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';
    protected $primaryKey = 'database_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'pnumber',
        'qty',
        'unit',
        'price',
        'nsn_code'
    ];

    // Relationships
    public function billItems()
    {
        return $this->hasMany(BillItem::class, 'database_id', 'database_id');
    }
}