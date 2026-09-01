<?php

namespace App\Helpers;

use App\Models\Bill;

class InvoiceHelper
{
    /**
     * Convert number to words
     */
    public static function numberToWords($number)
    {
        $no = round($number);
        $point = round(($number - $no) * 100);
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        
        $words = array(
            '0' => '', '1' => 'One', '2' => 'Two', '3' => 'Three', 
            '4' => 'Four', '5' => 'Five', '6' => 'Six', '7' => 'Seven',
            '8' => 'Eight', '9' => 'Nine', '10' => 'Ten', '11' => 'Eleven',
            '12' => 'Twelve', '13' => 'Thirteen', '14' => 'Fourteen',
            '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
            '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
            '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
            '60' => 'Sixty', '70' => 'Seventy', '80' => 'Eighty',
            '90' => 'Ninety'
        );
        
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str[] = ($number < 21) ? 
                    $words[$number] . " " . $digits[$counter] . $plural . " " . $hundred :
                    $words[floor($number / 10) * 10] . " " . $words[$number % 10] . " " . 
                    $digits[$counter] . $plural . " " . $hundred;
            } else {
                $str[] = null;
            }
        }
        
        $str = array_reverse($str);
        $result = implode('', $str);
        
        if ($point > 0) {
            $result .= " Rupees " . $words[$point] . " Paise Only";
        } else {
            $result .= " Rupees Only";
        }
        
        return $result;
    }

    /**
     * Paginate items for multi-page invoice
     */
    public static function paginateItems($items, $perPage = 18)
    {
        $pages = [];
        $totalItems = count($items);
        $pageCount = ceil($totalItems / $perPage);
        
        for ($i = 0; $i < $pageCount; $i++) {
            $pages[] = array_slice($items, $i * $perPage, $perPage);
        }
        
        return $pages;
    }

    /**
     * Calculate totals
     */
    public static function calculateTotals($bill)
    {
        $subtotal = $bill->items->sum('total');
        $discountAmount = $subtotal * ($bill->discount / 100);
        $afterDiscount = $subtotal - $discountAmount;
        
        // Determine tax type
        $customer = $bill->customer;
        $isPunjab = $customer && strtolower($customer->state) == 'punjab';
        $gstRate = \App\Models\GstSetting::getRate();
        
        if ($customer && $customer->gstnumber) {
            if ($isPunjab) {
                // CGST + SGST
                $cgst = $afterDiscount * ($gstRate / 200);
                $sgst = $afterDiscount * ($gstRate / 200);
                $igst = 0;
                $taxType = 'cgst_sgst';
            } else {
                // IGST
                $cgst = 0;
                $sgst = 0;
                $igst = $afterDiscount * ($gstRate / 100);
                $taxType = 'igst';
            }
        } else {
            $cgst = 0;
            $sgst = 0;
            $igst = 0;
            $taxType = 'no_gst';
        }
        
        $grandTotal = $afterDiscount + $cgst + $sgst + $igst + $bill->transport + $bill->package;
        
        return [
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'after_discount' => $afterDiscount,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'igst' => $igst,
            'grand_total' => $grandTotal,
            'tax_type' => $taxType,
            'gst_rate' => $gstRate,
            'amount_in_words' => self::numberToWords($grandTotal),
            'gross_total' => $afterDiscount + $bill->package // Gross Total = After Discount + Packaging
        ];
    }
}