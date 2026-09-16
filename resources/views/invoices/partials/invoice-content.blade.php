<style>
    .invoice-page {
        background-image: url('{{ asset('images/loga.png') }}') !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        background-size: initial !important;
    }

    @media print {
        .invoice-page {
            background-image: url('{{ asset('images/loga.png') }}') !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

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

    <div class="invoice-page"
     style="page-break-after: always; padding: 0px; max-width: 1000px; margin: 0 auto;
            font-family: Arial, sans-serif;
            background-image: url('{{ asset('images/loga.png') }}');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: initial;">

        <!-- ===== TAX INVOICE HEADER ===== -->
        <div style="text-align: center; margin-bottom: 0px;">
            <h2 style="font-weight: bold; margin: 0; font-size: 16px; text-decoration: underline;">TAX INVOICE</h2>
        </div>

        <!-- ===== HEADER BOX WITH IMAGE ===== -->
        <div style="border: 2px solid #000; padding: 4px; text-align: center; margin-bottom: 0px; overflow: hidden;">
            <img src="{{ asset('images/topi.jpg') }}"
                 alt="{{ $settings->company_name ?? 'A.B Shawls' }}"
                 style="max-height: 100px; max-width: 100%; width: auto; height: auto; display: block; margin: 0 auto;">
        </div>

        <!-- ===== INVOICE DETAILS ===== -->
        <div style="display: flex; justify-content: space-between; border: 1px solid #000; border-top: 0; padding: 6px 8px; margin-bottom: 0px;">
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
        <div style="border: 1px solid #000; border-top: 0; padding: 6px 8px; margin-bottom: 0px;">
            <div>
                <strong>Party Details:-</strong>
                <span style="font-weight: bold; text-decoration: underline;">{{ $bill->customer->name ?? 'N/A' }}</span>
                @unless($bill->customer->gstnumber)
                    <span style="font-weight: bold;"> (Unauthorised)</span>
                @endunless
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
        @php
            $anyItemDiscount = collect($bill->items)->contains(fn($i) => ((float)($i->discount ?? 0)) > 0);
            $colCount = $anyItemDiscount ? 9 : 6;
        @endphp

        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-bottom: 0; font-size: 11px; margin-bottom: 0px;">
            <thead>
                <tr style="background: #f0f0f0; border-bottom: 2px solid #000;">
                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: center; {{ $anyItemDiscount ? 'width: 6%;' : 'width: 8%;' }}">Qty</th>
                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: center; {{ $anyItemDiscount ? 'width: 6%;' : 'width: 8%;' }}">Unit</th>
                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: left; {{ $anyItemDiscount ? 'width: 30%;' : 'width: 40%;' }}">Particular</th>
                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: center; {{ $anyItemDiscount ? 'width: 10%;' : 'width: 12%;' }}">HSN/SAC</th>
                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: right; {{ $anyItemDiscount ? 'width: 10%;' : 'width: 12%;' }}">Price (Rs.)</th>
                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: right; {{ $anyItemDiscount ? 'width: 11%;' : 'width: 20%;' }}">Amount (Rs.)</th>

                    @if($anyItemDiscount)
                        <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: center; width: 7%;">Disc %</th>
                        <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: right; width: 10%;">Disc Amt</th>
                        <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: right; width: 10%;">Net (Rs.)</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($pageItems as $index => $item)
                    @php
                        $gross   = $item['qty'] * $item['price'];
                        $discPct = $item['discount'] ?? 0;
                        $discAmt = $gross * ($discPct / 100);
                        $net     = $gross - $discAmt;
                    @endphp
                    <tr>
                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: center;">{{ $item['qty'] }}</td>
                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: center;">{{ $item['unit'] ?? 'PCS' }}</td>
                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: left;">
                            {{ $item['Product'] }}
                            @if(!empty($item['pnumber'])) NO. {{ $item['pnumber'] }} @endif
                        </td>
                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: center;">{{ $item['nsn_code'] ?? '' }}</td>
                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ number_format($item['price'], 2) }}</td>
                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ number_format($gross, 2) }}</td>

                        @if($anyItemDiscount)
                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: center;">
                                {{ $discPct > 0 ? number_format($discPct, 2) : '-' }}
                            </td>
                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                {{ $discAmt > 0 ? number_format($discAmt, 2) : '-' }}
                            </td>
                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                {{ number_format($net, 2) }}
                            </td>
                        @endif
                    </tr>
                @endforeach

                @php $remainingRows = $bill->size - count($pageItems); @endphp
                @for($i = 0; $i < $remainingRows; $i++)
                    <tr>
                        @for($c = 0; $c < $colCount; $c++)
                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">&nbsp;</td>
                        @endfor
                    </tr>
                @endfor

                <tr>
                    <td colspan="{{ $colCount }}" style="border-bottom: 1px solid #000; padding: 0;"></td>
                </tr>
            </tbody>
        </table>

        <!-- ===== SUMMARY (Only on last page) ===== -->
        @if($showSummary)
        @php
            $hasOverallDiscount = ($totals['overall_discount_pct'] ?? 0) > 0;
            $totalRowValue = $totals['net_subtotal'];
            $showSubTotal = $hasOverallDiscount;
        @endphp

        <div style="margin-top: 0px;">
    <table style="width: 100%; border-collapse: collapse; font-size: 12px; font-family: Arial, Helvetica, sans-serif; border: 1px solid #000;">
        <tbody>
            {{-- ===== TOTAL ===== --}}
            <tr>
                <td style="width: 20%; text-align: center; padding: 2px 5px; font-weight: 700; background: #f0f0f0; border-bottom: 1px solid #000; border-right: 1px solid #000;">
                    {{ $bill->items->sum('qty') }}
                </td>
                <td style="width: 30%; padding: 2px 5px; font-weight: 700; background: #f0f0f0; border-bottom: 1px solid #000; border-right: 1px solid #000;">
                    Total
                </td>
                <td style="width: 20%; background: #f0f0f0; border-bottom: 1px solid #000; border-right: 1px solid #000;"></td>
                <td style="width: 30%; text-align: right; padding: 2px 5px; font-weight: 700; background: #f0f0f0; border-bottom: 1px solid #000;">
                    Rs. {{ number_format($totalRowValue, 2) }}
                </td>
            </tr>

            {{-- ===== OVERALL DISCOUNT ===== --}}
            @if($hasOverallDiscount)
            <tr>
                <td style="border-right: 1px solid #000;"></td>
                <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">
                    Discount --- {{ $totals['overall_discount_pct'] }}%
                </td>
                <td style="border-right: 1px solid #000;"></td>
                <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                    - Rs. {{ number_format($totals['overall_discount_amount'], 2) }}
                </td>
            </tr>
            @endif

            {{-- ===== SUB TOTAL ===== --}}
            @if($showSubTotal)
            <tr>
                <td style="border-right: 1px solid #000;"></td>
                <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">
                    Sub Total (After Discount)
                </td>
                <td style="border-right: 1px solid #000;"></td>
                <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                    Rs. {{ number_format($totals['after_discount'], 2) }}
                </td>
            </tr>
            @endif

            {{-- ===== TRANSPORTATION ===== --}}
            @if($bill->transport > 0)
            <tr>
                <td style="border-right: 1px solid #000;"></td>
                <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">
                    Transportation
                </td>
                <td style="border-right: 1px solid #000;"></td>
                <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                    + Rs. {{ number_format($bill->transport, 2) }}
                </td>
            </tr>
            @endif

            {{-- ===== PACKAGING ===== --}}
            @if($bill->package > 0)
            <tr>
                <td style="border-right: 1px solid #000;"></td>
                <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">
                    Packaging
                </td>
                <td style="border-right: 1px solid #000;"></td>
                <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                    + Rs. {{ number_format($bill->package, 2) }}
                </td>
            </tr>
            @endif

            {{-- ===== GROSS TOTAL ===== --}}
            <tr>
                <td style="border-right: 1px solid #000;"></td>
                <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">
                    Gross Total
                </td>
                <td style="border-right: 1px solid #000;"></td>
                <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                    Rs. {{ number_format($totals['gross_total'], 2) }}
                </td>
            </tr>

            {{-- ===== GST ===== --}}
            @if($totals['tax_type'] == 'cgst_sgst')
            <tr>
                <td style="border-right: 1px solid #000;"></td>
                <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">
                    CGST --- {{ $totals['gst_rate']/2 }}%
                </td>
                <td style="border-right: 1px solid #000;"></td>
                <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                    + Rs. {{ number_format($totals['cgst'], 2) }}
                </td>
            </tr>
            <tr>
                <td style="border-right: 1px solid #000;"></td>
                <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">
                    SGST --- {{ $totals['gst_rate']/2 }}%
                </td>
                <td style="border-right: 1px solid #000;"></td>
                <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                    + Rs. {{ number_format($totals['sgst'], 2) }}
                </td>
            </tr>
            @elseif($totals['tax_type'] == 'igst')
            <tr>
                <td style="border-right: 1px solid #000;"></td>
                <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">
                    IGST --- {{ $totals['gst_rate'] }}%
                </td>
                <td style="border-right: 1px solid #000;"></td>
                <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                    + Rs. {{ number_format($totals['igst'], 2) }}
                </td>
            </tr>
            @endif

            {{-- ===== GRAND TOTAL ===== --}}
            <tr style="font-weight: 700;">
                <td style="border-right: 1px solid #000; border-top: 2px solid #000;"></td>
                <td style="padding: 5px 5px; font-size: 16px; font-weight: 700; border-right: 1px solid #000; border-top: 2px solid #000;">
                    Grand Total
                </td>
                <td style="border-right: 1px solid #000; border-top: 2px solid #000;"></td>
                <td style="text-align: right; padding: 5px 5px; font-size: 16px; font-weight: 700; border-top: 2px solid #000;">
                    Rs. {{ number_format($totals['grand_total'], 2) }}
                </td>
            </tr>
        </tbody>
    </table>
</div>

        <!-- ===== TOTALS ROW ===== -->
        <div style="margin-top: 0px; border: 1px solid #000; border-top: 0; padding: 4px 5px;">
            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                <tr>
                    <td style="width: 20%; font-weight: bold; padding: 3px 5px; font-size: 14px; border: 1px solid;">Total</td>
                    <td style="width: 25%; font-weight: bold; padding: 3px 5px; font-size: 14px; border: 1px solid;">Total Tax</td>
                    <td style="width: 55%; font-weight: bold; padding: 3px 5px; font-size: 14px;">Total (In Words)</td>
                </tr>
                <tr>
                    <td style="padding: 3px 5px; font-weight: bold; font-size: 15px; border: 1px solid;">
                        Rs. {{ number_format($totals['grand_total'], 2) }}
                    </td>
                    <td style="padding: 3px 5px; font-weight: bold; font-size: 15px; border: 1px solid;">
                        Rs. {{ number_format($totals['cgst'] + $totals['sgst'] + $totals['igst'], 2) }}
                    </td>
                    <td style="padding: 3px 5px; font-size: 13px;">
                        {{ $totals['amount_in_words'] }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- ===== NOTES ===== -->
        <div style="margin-top: 0px; border: 1px solid #000; border-top: 0; padding: 4px 5px;">
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
        <div style="margin-top: 0px; border: 1px solid #000; border-top: 0; padding: 4px 5px;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                <div>
                    <strong>Bank Name:- {{ $settings->bank_name ?? 'BANK OF BARODA' }}</strong>
                </div>
                <div>
                    <strong>Account No.:- {{ $settings->bank_account ?? '70940200002257' }}</strong>
                </div>
                <div>
                    <strong>IFSC Code:- {{ $settings->bank_ifsc ?? 'BARB0DBAMRI' }}</strong>
                </div>
            </div>
        </div>

        <!-- ===== FOOTER ===== -->
        <div style="margin-top: 0px; border: 1px solid #000; border-top: 0; padding: 4px 5px; display: flex; justify-content: space-between; flex-wrap: wrap;">
            <div style="font-size: 11px;">
                @php
                    $terms = $settings->getTermsArray();
                @endphp
                @foreach($terms as $term)
                    {{ $term }}<br>
                @endforeach
            </div>
            <div style="text-align: right;">
                <br>
                <strong>{{ $settings->footer_text ?? 'For A.B Shawls' }}</strong>
            </div>
        </div>

        @else
        <!-- ===== PAGE TOTAL FOR NON-LAST PAGES ===== -->
        <div style="margin-top: 0px; text-align: right; border: 1px solid #000; border-top: 0; padding: 3px 5px;">
            <strong>Page Total: Rs. {{ number_format($pageSubtotal, 2) }}</strong>
            <span style="margin-left: 20px;">Page {{ $pageIndex + 1 }}/{{ $totalPages }}</span>
        </div>

        <!-- ===== CONTINUED NOTICE ===== -->
        <div style="margin-top: 0px; text-align: center; font-style: italic; font-size: 12px; border: 1px dashed #000; border-top: 0; padding: 3px;">
            Continued on next page...
        </div>
        @endif
    </div>
@endforeach