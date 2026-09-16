<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    use HasFactory;

    protected $table = 'invoice_settings';

    protected $fillable = [
        'company_name',
        'company_address',
        'company_gst',
        'company_phone',
        'company_phone_2',
        'company_logo',
        'bank_name',
        'bank_account',
        'bank_ifsc',
        'bank_branch',
        'terms_conditions',
        'terms_list',
        'footer_text',
        'default_gst_rate',
        'invoice_notes'
    ];

    protected $casts = [
        'terms_list' => 'array',
        'default_gst_rate' => 'decimal:2'
    ];

    /**
     * Get the current invoice settings
     */
    public static function getSettings()
    {
        $settings = self::first();
        if (!$settings) {
            $settings = self::createDefault();
        }
        return $settings;
    }

    /**
     * Create default settings
     */
    public static function createDefault()
    {
        return self::create([
            'company_name' => 'A.B SHAWLS',
            'company_address' => '10, Kamla Market, Shastri Market, Amritsar, Punjab',
            'company_gst' => '03AEIPA3719A1ZD',
            'company_phone' => '+91-9417807792',
            'company_phone2' => '+91-6280845993',
            'bank_name' => 'BANK OF BARODA',
            'bank_account' => '70940200002257',
            'bank_ifsc' => 'BARB0DBAMRI',
            'terms_list' => [
                'E. & O.E.',
                '1. Subject to \'Amritsar\' Jurisdiction only.',
                '2. Interest @24% P.A will be charged. If the payment is not made within stipulated time.'
            ],
            'footer_text' => 'For A.B Shawls',
            'default_gst_rate' => 5.00,
            'invoice_notes' => null
        ]);
    }

    /**
     * Get terms as array
     */
    public function getTermsArray()
    {
        if ($this->terms_list) {
            return $this->terms_list;
        }
        return [
            'E. & O.E.',
            '1. Subject to \'Amritsar\' Jurisdiction only.',
            '2. Interest @24% P.A will be charged. If the payment is not made within stipulated time.'
        ];
    }

    /**
     * Get terms as HTML
     */
    public function getTermsHtml()
    {
        $terms = $this->getTermsArray();
        return implode('<br>', $terms);
    }
}