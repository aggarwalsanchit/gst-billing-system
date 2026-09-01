<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GstSetting extends Model
{
    use HasFactory;

    protected $table = 'gst';
    protected $primaryKey = 'gid';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'gst1'
    ];

    // Get the current GST rate
    public static function getRate()
    {
        $setting = self::first();
        return $setting ? $setting->gst1 : 5;
    }
}