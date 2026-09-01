@php
    $totalPages = count($itemPages);
    $isLastPage = false;
@endphp

@foreach($itemPages as $pageIndex => $pageItems)
    @php
        $isLastPage = ($pageIndex + 1) == $totalPages;
        $pageSubtotal = $pageTotals[$pageIndex] ?? 0;
        $showSummary = $isLastPage;
    @endphp

    <div class="invoice-page" style="page-break-after: always; padding: 10px; max-width: 1000px; margin: 0 auto; font-family: Arial, sans-serif;">
        
        <!-- ===== TAX INVOICE HEADER ===== -->
        <div style="text-align: center; margin-bottom: 3px;">
            <h2 style="font-weight: bold; margin: 0; font-size: 22px; text-decoration: underline;">TAX INVOICE</h2>
        </div>

        <!-- ===== HEADER BOX WITH IMAGE ===== -->
        <div style="border: 2px solid #000; padding: 8px; text-align: center; margin-bottom: 0px;">
            <img src="{{ asset('images/topi.jpg') }}" alt="A.B Shawls" style="max-height: 90px; width: auto; display: block; margin: 0 auto;">
        </div>

        <!-- ===== INVOICE DETAILS ===== -->
        <div style="display: flex; justify-content: space-between; border: 1px solid #000; padding: 6px 8px; margin-bottom: 0px;">
            <div>
                <strong>Invoice No.:-</strong> {{ $bill->bill_id }}
            </div>
            <div>
                <strong>Dated:-</strong> {{ \Carbon\Carbon::parse($bill->bill_date)->format('d/m/Y') }}
            </div>
            @if($pageIndex > 0)
                <div>
                    <strong>Page:</strong> {{ $pageIndex + 1 }}/{{ $totalPages }}
                </div>
            @endif
        </div>

        <!-- ===== CUSTOMER DETAILS ===== -->
        <div style="border: 1px solid #000; padding: 6px 8px; margin-bottom: 0px;">
            <div>
                <strong>Party Details:-</strong> 
                <span style="font-weight: bold; text-decoration: underline;">{{ $bill->customer->name ?? 'N/A' }}</span>
            </div>
            <div>
                <strong>Address:-</strong> {{ $bill->customer->address ?? '' }}
            </div>
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                @if($bill->customer->gstnumber)
                    <div>
                        <strong>GSTIN/UIN:-</strong> {{ $bill->customer->gstnumber }}
                    </div>
                @endif
                <div>
                    <strong>PhoneNo.-</strong> {{ $bill->customer->phone ?? '' }}
                </div>
            </div>
        </div>

        <!-- ===== ITEMS TABLE ===== -->
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; font-size: 12px; margin-bottom: 0px;">
            <thead>
                <tr style="background: #f0f0f0; border-bottom: 2px solid #000;">
                    <th style="border: 1px solid #000; padding: 4px 5px; text-align: center; width: 8%;">Qty</th>
                    <th style="border: 1px solid #000; padding: 4px 5px; text-align: center; width: 8%;">Unit</th>
                    <th style="border: 1px solid #000; padding: 4px 5px; text-align: left; width: 40%;">Particular</th>
                    <th style="border: 1px solid #000; padding: 4px 5px; text-align: center; width: 12%;">HSN/SAC Code</th>
                    <th style="border: 1px solid #000; padding: 4px 5px; text-align: right; width: 12%;">Price (Rs.)</th>
                    <th style="border: 1px solid #000; padding: 4px 5px; text-align: right; width: 20%;">Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pageItems as $index => $item)
                <tr>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: center;">{{ $item['qty'] }}</td>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: center;">{{ $item['unit'] ?? 'PCS' }}</td>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: left;">
                        {{ $item['Product'] }} 
                        @if($item['pnumber']) NO. {{ $item['pnumber'] }} @endif
                    </td>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: center;">{{ $item['nsn_code'] ?? '' }}</td>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: right;">{{ number_format($item['price'], 2) }}</td>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: right;">{{ number_format($item['total'], 2) }}</td>
                </tr>
                @endforeach
                
                @php
                    $remainingRows = 18 - count($pageItems);
                @endphp
                @for($i = 0; $i < $remainingRows; $i++)
                <tr>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: center;">&nbsp;</td>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: center;">&nbsp;</td>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: left;">&nbsp;</td>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: center;">&nbsp;</td>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: right;">&nbsp;</td>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px 5px; text-align: right;">&nbsp;</td>
                </tr>
                @endfor
                
                <tr>
                    <td colspan="6" style="border-bottom: 1px solid #000; padding: 0;"></td>
                </tr>
            </tbody>
        </table>

                <!-- ===== SUMMARY (Only on last page) ===== -->
        @if($showSummary)
        <div style="margin-top: 0px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; border: 1px solid #000;">
                <!-- Total Qty -->
                <tr>
                    <td style="width: 20%; text-align: center; border: none; padding: 6px 5px; font-weight: bold; font-size: 14px; background: #f0f0f0; border-bottom: 1px solid #000;">
                        {{ $bill->items->sum('qty') }}
                    </td>
                    <td style="width: 30%; text-align: left; border: none; padding: 6px 5px; font-weight: bold; font-size: 14px; background: #f0f0f0; border-bottom: 1px solid #000;">
                        Total
                    </td>
                    <td style="width: 20%; border: none; padding: 6px 5px; background: #f0f0f0; border-bottom: 1px solid #000;"></td>
                    <td style="width: 30%; text-align: right; border: none; padding: 6px 5px; font-weight: bold; font-size: 14px; background: #f0f0f0; border-bottom: 1px solid #000;">
                        Rs. {{ number_format($totals['subtotal'], 2) }}
                    </td>
                </tr>
                
                <!-- Discount -->
                @if($bill->discount > 0)
                <tr>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        Discount --- {{ $bill->discount }}%
                    </td>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="text-align: right; border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        - Rs. {{ number_format($totals['discount_amount'], 2) }}
                    </td>
                </tr>
                @endif
                
                <!-- Sub Total -->
                <tr>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="border: none; padding: 6px 5px; font-weight: bold; font-size: 15px;">
                        Sub Total (After Discount)
                    </td>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="text-align: right; border: none; padding: 6px 5px; font-weight: bold; font-size: 15px;">
                        Rs. {{ number_format($totals['after_discount'], 2) }}
                    </td>
                </tr>
                
                <!-- Packaging -->
                @if($bill->package > 0)
                <tr>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        Packaging
                    </td>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="text-align: right; border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        + Rs. {{ number_format($bill->package, 2) }}
                    </td>
                </tr>
                @endif

                @if($bill->transport > 0)
                <tr>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        Transport
                    </td>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="text-align: right; border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        + Rs. {{ number_format($bill->transport, 2) }}
                    </td>
                </tr>
                <!-- LINE AFTER TRANSPORT -->
                <tr><td colspan="4" style="border-bottom: 1px solid #000; padding: 0;"></td></tr>
                @endif
                
                <!-- Gross Total -->
                <tr>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="border: none; padding: 6px 5px; font-weight: bold; font-size: 15px;">
                        Gross Total
                    </td>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="text-align: right; border: none; padding: 6px 5px; font-weight: bold; font-size: 15px;">
                        Rs. {{ number_format($totals['after_discount'] + $bill->package, 2) }}
                    </td>
                </tr>
                
                <!-- Transport (moved after Packaging) -->
                
                
                <!-- GST -->
                @if($totals['tax_type'] == 'cgst_sgst')
                <tr>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        CGST --- {{ $totals['gst_rate']/2 }}%
                    </td>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="text-align: right; border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        + Rs. {{ number_format($totals['cgst'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        SGST --- {{ $totals['gst_rate']/2 }}%
                    </td>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="text-align: right; border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        + Rs. {{ number_format($totals['sgst'], 2) }}
                    </td>
                </tr>
                @elseif($totals['tax_type'] == 'igst')
                <tr>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        IGST --- {{ $totals['gst_rate'] }}%
                    </td>
                    <td style="border: none; padding: 6px 5px;"></td>
                    <td style="text-align: right; border: none; padding: 6px 5px; font-weight: bold; font-size: 14px;">
                        + Rs. {{ number_format($totals['igst'], 2) }}
                    </td>
                </tr>
                @endif
                
                <!-- Grand Total -->
                <tr style="font-weight: bold; border-top: 3px solid #000;">
                    <td style="border: none; padding: 8px 5px;"></td>
                    <td style="border: none; padding: 8px 5px; font-size: 18px; font-weight: bold;">
                        Grand Total
                    </td>
                    <td style="border: none; padding: 8px 5px;"></td>
                    <td style="text-align: right; border: none; padding: 8px 5px; font-size: 18px; font-weight: bold;">
                        Rs. {{ number_format($totals['grand_total'], 2) }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- ===== TOTALS ROW ===== -->
        <div style="margin-top: 0px; border: 1px solid #000; padding: 4px 5px;">
            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                <tr>
                    <td style="width: 20%; font-weight: bold; padding: 3px 5px; font-size: 14px;">Total</td>
                    <td style="width: 25%; font-weight: bold; padding: 3px 5px; font-size: 14px;">Total Tax</td>
                    <td style="width: 55%; font-weight: bold; padding: 3px 5px; font-size: 14px;">Total (In Words)</td>
                </tr>
                <tr>
                    <td style="padding: 3px 5px; font-weight: bold; font-size: 15px;">
                        Rs. {{ number_format($totals['grand_total'], 2) }}
                    </td>
                    <td style="padding: 3px 5px; font-weight: bold; font-size: 15px;">
                        Rs. {{ number_format($totals['cgst'] + $totals['sgst'] + $totals['igst'], 2) }}
                    </td>
                    <td style="padding: 3px 5px; font-size: 13px;">
                        {{ $totals['amount_in_words'] }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- ===== NOTES ===== -->
        <div style="margin-top: 0px; border: 1px solid #000; padding: 4px 5px;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                <div>
                    <strong>Despatched Thru:</strong> {{ $bill->note->despatch ?? '' }}
                </div>
                <div>
                    <strong>Delivery Note:-</strong> {{ $bill->note->deliverynote ?? '' }}
                </div>
            </div>
        </div>

        <!-- ===== BANK DETAILS ===== -->
        <div style="margin-top: 0px; border: 1px solid #000; padding: 4px 5px;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                <div>
                    <strong>Bank Name:- BANK OF BARODA</strong>
                </div>
                <div>
                    <strong>Account No.:- 70940200002257</strong>
                </div>
                <div>
                    <strong>IFsc Code:- BARB0DBAMRI</strong>
                </div>
            </div>
        </div>

        <!-- ===== FOOTER ===== -->
        <div style="margin-top: 0px; border: 1px solid #000; padding: 4px 5px; display: flex; justify-content: space-between; flex-wrap: wrap;">
            <div style="font-size: 11px;">
                <strong>E. & O.E.</strong><br>
                1. Subject to 'Amritsar' Jurisdiction only.<br>
                2. Interest @24% P.A will be charged. If the payment is not made within stipulated time.
            </div>
            <div style="text-align: right;">
                <br>
                <strong>For A.B Shawls</strong>
            </div>
        </div>

        @else
        <!-- ===== PAGE TOTAL FOR NON-LAST PAGES ===== -->
        <div style="margin-top: 0px; text-align: right; border: 1px solid #000; padding: 3px 5px;">
            <strong>Page Total: Rs. {{ number_format($pageSubtotal, 2) }}</strong>
            <span style="margin-left: 20px;">Page {{ $pageIndex + 1 }}/{{ $totalPages }}</span>
        </div>

        <!-- ===== CONTINUED NOTICE ===== -->
        <div style="margin-top: 0px; text-align: center; font-style: italic; font-size: 12px; border: 1px dashed #000; padding: 3px;">
            Continued on next page...
        </div>
        @endif
    </div>
@endforeach