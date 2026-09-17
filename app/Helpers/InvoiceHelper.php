<?php

namespace App\Helpers;

class InvoiceHelper
{
    /**
     * Paginate items for multi-page invoice.
     */
    public static function paginateItems($items, $perPage = 18)
    {
        $pages = [];
        $totalItems = count($items);
        $pageCount = max(1, ceil($totalItems / $perPage));

        for ($i = 0; $i < $pageCount; $i++) {
            $pages[] = array_slice($items, $i * $perPage, $perPage);
        }

        return $pages;
    }

    /**
     * Calculate totals with per-item discount + overall discount.
     *
     * Flow:
     *   1. For each item:  gross = qty × price
     *                      item_discount = gross × (item.discount / 100)
     *                      net = gross − item_discount
     *   2. gross_subtotal = Σ gross
     *      item_discount_total = Σ item_discount
     *      net_subtotal = Σ net
     *   3. overall_discount_amount = net_subtotal × (bill.discount / 100)
     *   4. after_discount = net_subtotal − overall_discount_amount   ← GST base
     *   5. GST on after_discount (Punjab → CGST+SGST; Other → IGST)
     *   6. gross_total = after_discount + transport + package
     *      grand_total = after_discount + tax + transport + package
     */
    public static function calculateTotals($bill)
    {
        // ---------- Step 1: Per-item discounts ----------
        $grossSubtotal     = 0;
        $itemDiscountTotal = 0;
        $netSubtotal       = 0;

        foreach ($bill->items as $item) {
            $gross = (float) $item->qty * (float) $item->price;
            $disc  = $gross * ((float) ($item->discount ?? 0) / 100);
            $net   = $gross - $disc;

            $grossSubtotal     += $gross;
            $itemDiscountTotal += $disc;
            $netSubtotal       += $net;
        }

        // ---------- Step 2: Overall discount ----------
        $overallDiscountPct    = (float) ($bill->discount ?? 0);
        $overallDiscountAmount = $netSubtotal * ($overallDiscountPct / 100);
        $afterDiscount         = $netSubtotal - $overallDiscountAmount;

        // ---------- Step 3: GST ----------
        $customer = $bill->customer;
        $isPunjab = $customer && trim(strtolower($customer->state)) == 'punjab';
        $gstRate  = \App\Models\GstSetting::getRate();

        if ($isPunjab) {
            $cgst    = $afterDiscount * ($gstRate / 200);
            $sgst    = $afterDiscount * ($gstRate / 200);
            $igst    = 0;
            $taxType = 'cgst_sgst';
        } else {
            $cgst    = 0;
            $sgst    = 0;
            $igst    = $afterDiscount * ($gstRate / 100);
            $taxType = 'igst';
        }

        // ---------- Step 4: Totals ----------
        $grossTotal = $afterDiscount + $bill->transport + $bill->package;
        $grandTotal = $afterDiscount + $cgst + $sgst + $igst
                    + $bill->transport + $bill->package;

        return [
            'gross_subtotal'          => $grossSubtotal,
            'item_discount_total'     => $itemDiscountTotal,
            'net_subtotal'            => $netSubtotal,
            'overall_discount_pct'    => $overallDiscountPct,
            'overall_discount_amount' => $overallDiscountAmount,
            'after_discount'          => $afterDiscount,
            'cgst'                    => $cgst,
            'sgst'                    => $sgst,
            'igst'                    => $igst,
            'gross_total'             => $grossTotal,
            'grand_total'             => $grandTotal,
            'tax_type'                => $taxType,
            'gst_rate'                => $gstRate,
            'amount_in_words'         => self::numberToWords($grandTotal),
        ];
    }

    /**
 * Convert number to words.
 */
public static function numberToWords($number)
{
    $no = (int) round($number);
    $point = (int) round(($number - $no) * 100);

    $words = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
        18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty',
        30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty',
        60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety',
    ];

    $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];

    // Helper to convert a 2-digit chunk (0-99) into words
    $twoDigit = function (int $n) use ($words) {
        if ($n < 21) {
            return $words[$n] ?? '';
        }
        $tens = (int) (floor($n / 10) * 10);
        $ones = $n % 10;
        return trim(($words[$tens] ?? '') . ' ' . ($words[$ones] ?? ''));
    };

    // Build the whole-number part
    if ($no === 0) {
        $result = 'Zero';
    } else {
        $chunks = [];
        $n = $no;
        $i = 0;
        while ($n > 0) {
            $divider = ($i === 1) ? 10 : 100;   // 1st chunk: 0-99, then 0-99, etc.
            // Actually: 1st chunk takes last 2 digits, next take 2 digits, then 2, etc.
            if ($i === 0) {
                $divider = 100;
            } else {
                $divider = 100;
            }
            $chunk = $n % 100;
            $n = (int) floor($n / 100);

            if ($chunk > 0) {
                $chunkWords = $twoDigit($chunk);
                $scale = $digits[$i] ?? '';
                if ($i === 1) {
                    // Indian numbering: 100s digit needs "Hundred" suffix in the previous chunk
                    // Actually simpler: after first two digits, next are Thousand, Lakh, Crore
                    $scale = 'Thousand';
                } elseif ($i === 2) {
                    $scale = 'Lakh';
                } elseif ($i === 3) {
                    $scale = 'Crore';
                }
                $chunks[] = trim($chunkWords . ' ' . $scale);
            }
            $i++;
        }
        $result = implode(' ', array_reverse($chunks));
        $result = trim(preg_replace('/\s+/', ' ', $result));
    }

    // Append paise if any
    if ($point > 0) {
        $paiseWords = $twoDigit($point);
        return $result . ' Rupees ' . $paiseWords . ' Paise Only';
    }

    return $result . ' Rupees Only';
}
}