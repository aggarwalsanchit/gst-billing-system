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

        // ---- Compute discounted page total (same basis as Amount column) ----
        $overallPct = (float) ($bill->discount ?? 0);
        $factor     = 1 - ($overallPct / 100);

        $pageDiscountedTotal = 0;
        foreach ($pageItems as $pi) {
            $g = $pi['qty'] * $pi['price'];
            $d = $g * ((float)($pi['discount'] ?? 0) / 100);
            $baseNet = $g - $d;
            $pageDiscountedTotal += $baseNet * $factor;
        }
    @endphp

    <div class="invoice-page"
     style="page-break-after: always; padding: 0px; max-width: 1000px; margin: 0 auto;
            font-family: Arial, sans-serif;
            background-image: url('{{ asset('images/loga.png') }}');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: initial;">

        <!-- ===== TAX INVOICE HEADER ===== -->
        <div style="text-align: center; margin: 0; padding: 0;">
            <h2 style="font-weight: bold; margin: 0; padding: 0; font-size: 16px; text-decoration: underline; line-height: 1;">TAX INVOICE</h2>
        </div>

        <!-- ===== HEADER BOX ===== -->
        <div style="border: 2px solid #000; padding: 6px 8px; margin-bottom: 0px; overflow: hidden;">

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 10%; vertical-align: top; text-align: left;">
                        <div style="font-size: 11px; font-weight: bold; line-height: 1.2; margin-bottom: 6px;">
                            GST No. {{ $settings->company_gst ?? '' }}
                        </div>
                        <img src="{{ asset('images/lakshmi.jpg') }}"
                             alt=""
                             style="width: 55px; height: 55px; display: block; object-fit: contain;">
                    </td>

                    <td style="width: 70%; vertical-align: middle; text-align: center; padding: 0 6px;">
                        <div style="font-family: 'Arial Black', Arial, sans-serif; font-weight: 900; font-size: 42px; line-height: 1; color: #000; letter-spacing: 1px;">
                            {{ $settings->company_name ?? '' }}
                        </div>

                        @if(!empty($settings->tagline))
                        <div style="margin-top: 3px;">
                            <span style="display: inline-block; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 2px 8px; font-family: Arial, sans-serif; font-weight: 900; font-size: 11px; color: #000; line-height: 1.2;">
                                {{ $settings->tagline }}
                            </span>
                        </div>
                        @endif

                        @if(!empty($settings->company_address))
                        <div style="font-family: Arial, sans-serif; font-weight: 900; font-size: 12px; color: #000; margin-top: 2px; line-height: 1.2;">
                            {{ $settings->company_address }}
                        </div>
                        @endif
                    </td>

                    <td style="width: 15%; vertical-align: top; text-align: right;">
                        <div style="font-size: 11px; font-weight: bold; line-height: 1.3; margin-bottom: 6px;">
                            @if(!empty($settings->company_phone))
                                <div><span style="color: #1a4a8a;">&#128222;</span> {{ $settings->company_phone }}</div>
                            @endif
                            @if(!empty($settings->company_phone_2))
                                <div><span style="color: #1a4a8a;">&#128222;</span> {{ $settings->company_phone_2 }}</div>
                            @endif
                        </div>
                        <img src="{{ asset('images/ganesh.jpg') }}"
                             alt=""
                             style="width: 55px; height: 55px; display: block; object-fit: contain; margin-left: auto;">
                    </td>
                </tr>
            </table>

        </div>

        <!-- ===== INVOICE DETAILS ===== -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border: 1px solid #000; border-top: 0; padding: 6px 8px; margin-bottom: 0px;">

            <div style="line-height: 1.4;">
                <div>
                    <strong>Invoice No.:-</strong> {{ $bill->bill_id }}
                </div>
                <div>
                    <strong>Dated:-</strong> {{ \Carbon\Carbon::parse($bill->bill_date)->format('d/m/Y') }}
                </div>
            </div>

            <div style="line-height: 1.4; text-align: right;">
                @if(!empty($settings->company_email))
                    <div>
                        <span style="color: #c0392b;">&#9993;</span>
                        <strong>Email:-</strong> {{ $settings->company_email }}
                    </div>
                @endif
                @if(!empty($settings->company_website))
                    <div>
                        <span style="color: #1a4a8a;">&#127760;</span>
                        <strong>Website:-</strong> {{ $settings->company_website }}
                    </div>
                @endif
            </div>

        </div>

        <!-- ===== CUSTOMER DETAILS ===== -->
        <div style="border: 1px solid #000; border-top: 0; padding: 6px 8px; margin-bottom: 0px;">

            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <strong>Buyer Details:-</strong>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $bill->customer->name ?? 'N/A' }}</span>
                    @unless($bill->customer->gstnumber)
                        <span style="font-weight: bold;"> (Unauthorised)</span>
                    @endunless
                </div>
                @if($bill->customer->gstnumber)
                    <div style="text-align: right;">
                        <strong>GSTIN/UIN:-</strong> {{ $bill->customer->gstnumber }}
                    </div>
                @endif
            </div>

            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 2px;">
                <div style="flex: 1 1 auto;">
                    <strong>Address:-</strong> {{ $bill->customer->address ?? '' }}
                </div>
                @if(!empty($bill->customer->phone))
                <div style="flex: 0 0 auto; text-align: right; margin-left: 10px;">
                    <span style="color: #1a4a8a;">&#128222;</span>
                    +91-{{ $bill->customer->phone }}
                </div>
                @endif
            </div>

        </div>

        <!-- ===== ITEMS TABLE ===== -->
        @php
            $anyItemDiscount = collect($bill->items)->contains(fn($i) => ((float)($i->discount ?? 0)) > 0);
            $overallDiscPct = (float) ($bill->discount ?? 0);
            $showDiscountCols = $anyItemDiscount || $overallDiscPct > 0;
            $colCount = $showDiscountCols ? 8 : 6;
        @endphp

        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-bottom: 0; font-size: 11px; margin-bottom: 0px;">
            <thead>
                <tr style="background: #f0f0f0; border-bottom: 2px solid #000;">
                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: center; {{ $showDiscountCols ? 'width: 6%;' : 'width: 8%;' }}">Qty</th>
                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: center; {{ $showDiscountCols ? 'width: 6%;' : 'width: 8%;' }}">Unit</th>
                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: left; {{ $showDiscountCols ? 'width: 30%;' : 'width: 40%;' }}">Particular</th>
                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: center; {{ $showDiscountCols ? 'width: 10%;' : 'width: 12%;' }}">HSN/SAC</th>
                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: right; {{ $showDiscountCols ? 'width: 10%;' : 'width: 12%;' }}">Item Price (Rs.)</th>

                    @if($showDiscountCols)
                        <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: center; width: 8%;">Disc %</th>
                        <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: right; width: 12%;">Disc Amt</th>
                    @endif

                    <th style="border: 1px solid #000; border-top: 0; padding: 4px 3px; text-align: right; {{ $showDiscountCols ? 'width: 18%;' : 'width: 20%;' }}">Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pageItems as $index => $item)
                    @php
                        $gross       = $item['qty'] * $item['price'];
                        $itemDiscPct = (float) ($item['discount'] ?? 0);

                        $combinedDiscPct = (1 - ((1 - $itemDiscPct / 100) * (1 - $overallDiscPct / 100))) * 100;

                        $discAmt = $gross * ($combinedDiscPct / 100);
                        $net     = $gross - $discAmt;

                        if ($itemDiscPct > 0 && $overallDiscPct > 0) {
                            $displayDiscPct = $combinedDiscPct;
                        } elseif ($itemDiscPct > 0) {
                            $displayDiscPct = $itemDiscPct;
                        } elseif ($overallDiscPct > 0) {
                            $displayDiscPct = $overallDiscPct;
                        } else {
                            $displayDiscPct = 0;
                        }
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

                        @if($showDiscountCols)
                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: center;">
                                {{ $displayDiscPct > 0 ? number_format($displayDiscPct, 2) : '-' }}
                            </td>
                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                {{ $discAmt > 0 ? number_format($discAmt, 2) : '-' }}
                            </td>
                        @endif

                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                            {{ number_format($showDiscountCols ? $net : $gross, 2) }}
                        </td>
                    </tr>
                @endforeach

                {{-- ============================================================ --}}
                {{-- ===== DYNAMIC FILLER ROWS ===== --}}
                {{-- Single-page bill: bigger budget (26) --}}
                {{-- Multi-page bill: smaller budgets for non-last and last pages --}}
                {{-- ============================================================ --}}
                @php
                    $isSinglePage = ($totalPages === 1);

                    if ($showSummary) {
                        // ---- Single-page bill OR last page of a multi-page bill ----
                        // Single page gets a big budget (26) so short bills look full
                        // Last page of multi-page uses a smaller total (22) so it doesn't overflow
                        $basePageSlots = $isSinglePage ? 26 : 22;

                        $isPunjabBill = $bill->customer
                            && trim(strtolower($bill->customer->state ?? '')) == 'punjab';

                        $summarySlots = 4; // Total, Gross Total, Grand Total, Tax table baseline
                        $summarySlots += ($bill->package > 0) ? 1 : 0;
                        $summarySlots += ($bill->transport > 0) ? 1 : 0;
                        $summarySlots += $isPunjabBill ? 2 : 1; // CGST+SGST or IGST
                        $summarySlots += 2; // Notes + Bank + Footer baseline

                        $pageSlotBudget = max(6, $basePageSlots - $summarySlots);
                    } else {
                        // ---- Non-last pages of a multi-page bill ----
                        // No summary on these pages → smaller budget keeps them compact
                        $pageSlotBudget = 20;
                    }

                    $remainingRows = max(0, $pageSlotBudget - count($pageItems));
                @endphp

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

        {{-- ============================================================ --}}
        {{-- ===== PAGE TOTAL / PAGE NUMBER — only if multi-page ===== --}}
        {{-- ============================================================ --}}
        @if($totalPages > 1)
        <div style="margin-top: 0px; text-align: right; border: 1px solid #000; border-top: 0; padding: 3px 5px;">
            @unless($showSummary)
                <strong>Page Total: Rs. {{ number_format($pageDiscountedTotal, 2) }}</strong>
            @endunless
            <span style="margin-left: 20px;">Page {{ $pageIndex + 1 }}/{{ $totalPages }}</span>
        </div>
        @endif

        {{-- ============================================================ --}}
        {{-- ========== SUMMARY — LAST PAGE ONLY ========== --}}
        {{-- ============================================================ --}}
        @if($showSummary)
        @php
            // ---- Per-page discounted totals for all prior pages ----
            $perPageTotals = [];
            for ($i = 0; $i < $pageIndex; $i++) {
                $pgSum = 0;
                foreach ($itemPages[$i] as $pi) {
                    $g = $pi['qty'] * $pi['price'];
                    $d = $g * ((float)($pi['discount'] ?? 0) / 100);
                    $baseNet = $g - $d;
                    $pgSum += $baseNet * $factor;
                }
                $perPageTotals[$i] = $pgSum;
            }

            // ---- Previous pages total = sum of per-page totals ----
            $previousPagesTotal = array_sum($perPageTotals);

            // ---- Current page total ----
            $currentPageTotal = $pageDiscountedTotal;

            // ---- Combined total ----
            $totalAfterDiscount = $previousPagesTotal + $currentPageTotal;

            // ---- Taxable base = Total + Packaging ----
            $taxableBase = $totalAfterDiscount + ($bill->package ?? 0);

            $isPunjab = $bill->customer
                && trim(strtolower($bill->customer->state ?? '')) == 'punjab';
            $gstRate = \App\Models\GstSetting::getRate();

            if ($isPunjab) {
                $cgst = $taxableBase * ($gstRate / 200);
                $sgst = $taxableBase * ($gstRate / 200);
                $igst = 0;
                $taxType = 'cgst_sgst';
            } else {
                $cgst = 0;
                $sgst = 0;
                $igst = $taxableBase * ($gstRate / 100);
                $taxType = 'igst';
            }

            $grossTotal = $taxableBase;
            $grandTotal = $taxableBase + $cgst + $sgst + $igst + ($bill->transport ?? 0);
        @endphp

        <div style="margin-top: 0px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px; font-family: Arial, Helvetica, sans-serif; border: 1px solid #000;">
                <tbody>
                    {{-- One row per previous page (multi-page only) --}}
                    @if($pageIndex > 0)
                        @foreach($perPageTotals as $i => $pgTotal)
                        <tr>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">
                                Page {{ $i + 1 }} Total
                            </td>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                                Rs. {{ number_format($pgTotal, 2) }}
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td style="border-right: 1px solid #000;"></td>
                            <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000; border-top: 1px solid #000;">
                                Current Page {{ $pageIndex + 1 }} Total
                            </td>
                            <td style="border-right: 1px solid #000; border-top: 1px solid #000;"></td>
                            <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px; border-top: 1px solid #000;">
                                Rs. {{ number_format($currentPageTotal, 2) }}
                            </td>
                        </tr>
                    @endif

                    {{-- Total --}}
                    <tr>
                        <td style="width: 20%; text-align: center; padding: 2px 5px; font-weight: 700; background: #f0f0f0; border-bottom: 1px solid #000; border-right: 1px solid #000;">
                            {{ $bill->items->sum('qty') }}
                        </td>
                        <td style="width: 30%; padding: 2px 5px; font-weight: 700; background: #f0f0f0; border-bottom: 1px solid #000; border-right: 1px solid #000;">
                            Total
                        </td>
                        <td style="width: 20%; background: #f0f0f0; border-bottom: 1px solid #000; border-right: 1px solid #000;"></td>
                        <td style="width: 30%; text-align: right; padding: 2px 5px; font-weight: 700; background: #f0f0f0; border-bottom: 1px solid #000;">
                            Rs. {{ number_format($totalAfterDiscount, 2) }}
                        </td>
                    </tr>

                    {{-- Packaging --}}
                    @if($bill->package > 0)
                    <tr>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">Packaging</td>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                            + Rs. {{ number_format($bill->package, 2) }}
                        </td>
                    </tr>
                    @endif

                    {{-- Gross Total --}}
                    <tr>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">Gross Total</td>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                            Rs. {{ number_format($grossTotal, 2) }}
                        </td>
                    </tr>

                    {{-- GST --}}
                    @if($taxType == 'cgst_sgst')
                    <tr>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">CGST --- {{ $gstRate/2 }}%</td>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                            + Rs. {{ number_format($cgst, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">SGST --- {{ $gstRate/2 }}%</td>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                            + Rs. {{ number_format($sgst, 2) }}
                        </td>
                    </tr>
                    @elseif($taxType == 'igst')
                    <tr>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">IGST --- {{ $gstRate }}%</td>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                            + Rs. {{ number_format($igst, 2) }}
                        </td>
                    </tr>
                    @endif

                    {{-- Transportation --}}
                    @if($bill->transport > 0)
                    <tr>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="padding: 2px 5px; font-weight: 700; font-size: 12px; border-right: 1px solid #000;">Transportation</td>
                        <td style="border-right: 1px solid #000;"></td>
                        <td style="text-align: right; padding: 2px 5px; font-weight: 700; font-size: 12px;">
                            + Rs. {{ number_format($bill->transport, 2) }}
                        </td>
                    </tr>
                    @endif

                    {{-- Grand Total --}}
                    <tr style="font-weight: 700;">
                        <td style="border-right: 1px solid #000; border-top: 2px solid #000;"></td>
                        <td style="padding: 5px; font-size: 16px; font-weight: 700; border-right: 1px solid #000; border-top: 2px solid #000;">Grand Total</td>
                        <td style="border-right: 1px solid #000; border-top: 2px solid #000;"></td>
                        <td style="text-align: right; padding: 5px; font-size: 16px; font-weight: 700; border-top: 2px solid #000;">
                            Rs. {{ number_format($grandTotal, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ===== TAX BREAKDOWN (last page only) ===== -->
        <div style="margin-top: 0px; border: 1px solid #000; border-top: 0; padding: 4px 5px;">
            <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
                <thead>
                    @if($taxType == 'cgst_sgst')
                        <tr>
                            <td rowspan="2" style="font-weight: bold; border: 1px solid; vertical-align: middle; text-align: center;">Total</td>
                            <td colspan="2" style="font-weight: bold; border: 1px solid; text-align: center;">CGST</td>
                            <td colspan="2" style="font-weight: bold; border: 1px solid; text-align: center;">SGST</td>
                            <td rowspan="2" style="font-weight: bold; border: 1px solid; vertical-align: middle; text-align: center;">Total Tax</td>
                            <td rowspan="2" style="font-weight: bold; border: 1px solid; vertical-align: middle; text-align: center;">Total (In Words)</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; border: 1px solid; text-align: center;">Rate</td>
                            <td style="font-weight: bold; border: 1px solid; text-align: center;">Amount</td>
                            <td style="font-weight: bold; border: 1px solid; text-align: center;">Rate</td>
                            <td style="font-weight: bold; border: 1px solid; text-align: center;">Amount</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 14px; border: 1px solid; text-align: center; vertical-align: middle;">Rs. {{ number_format($grandTotal, 2) }}</td>
                            <td style="font-weight: bold; font-size: 13px; border: 1px solid; text-align: center;">{{ $gstRate/2 }}%</td>
                            <td style="font-weight: bold; font-size: 13px; border: 1px solid; text-align: center;">Rs. {{ number_format($cgst, 2) }}</td>
                            <td style="font-weight: bold; font-size: 13px; border: 1px solid; text-align: center;">{{ $gstRate/2 }}%</td>
                            <td style="font-weight: bold; font-size: 13px; border: 1px solid; text-align: center;">Rs. {{ number_format($sgst, 2) }}</td>
                            <td style="font-weight: bold; font-size: 13px; border: 1px solid; text-align: center; vertical-align: middle;">Rs. {{ number_format($cgst + $sgst, 2) }}</td>
                            <td style="font-size: 12px; border: 1px solid; vertical-align: middle;">{{ \App\Helpers\InvoiceHelper::numberToWords($grandTotal) }}</td>
                        </tr>
                    @elseif($taxType == 'igst')
                        <tr>
                            <td rowspan="2" style="font-weight: bold; border: 1px solid; vertical-align: middle; text-align: center;">Total</td>
                            <td colspan="2" style="font-weight: bold; border: 1px solid; text-align: center;">IGST</td>
                            <td rowspan="2" style="font-weight: bold; border: 1px solid; vertical-align: middle; text-align: center;">Total Tax</td>
                            <td rowspan="2" style="font-weight: bold; border: 1px solid; vertical-align: middle; text-align: center;">Total (In Words)</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; border: 1px solid; text-align: center;">Rate</td>
                            <td style="font-weight: bold; border: 1px solid; text-align: center;">Amount</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 14px; border: 1px solid; text-align: center; vertical-align: middle;">Rs. {{ number_format($grandTotal, 2) }}</td>
                            <td style="font-weight: bold; font-size: 13px; border: 1px solid; text-align: center;">{{ $gstRate }}%</td>
                            <td style="font-weight: bold; font-size: 13px; border: 1px solid; text-align: center;">Rs. {{ number_format($igst, 2) }}</td>
                            <td style="font-weight: bold; font-size: 13px; border: 1px solid; text-align: center; vertical-align: middle;">Rs. {{ number_format($igst, 2) }}</td>
                            <td style="font-size: 12px; border: 1px solid; vertical-align: middle;">{{ \App\Helpers\InvoiceHelper::numberToWords($grandTotal) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td style="font-weight: bold; border: 1px solid;">Total</td>
                            <td style="font-weight: bold; border: 1px solid;">Total (In Words)</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 14px; border: 1px solid;">Rs. {{ number_format($grandTotal, 2) }}</td>
                            <td style="font-size: 12px; border: 1px solid;">{{ \App\Helpers\InvoiceHelper::numberToWords($grandTotal) }}</td>
                        </tr>
                    @endif
                </thead>
            </table>
        </div>
        @endif
        {{-- ===== END SUMMARY ===== --}}

        {{-- ============================================================ --}}
        {{-- ===== NOTES / BANK / FOOTER — every page ===== --}}
        {{-- ============================================================ --}}

        <!-- NOTES -->
        <div style="margin-top: 0px; border: 1px solid #000; border-top: 0; padding: 4px 5px;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; font-size: 13px;">
                <div>
                    <strong>Despatched Thru:</strong> {{ $bill->note->despatch ?? '' }}
                </div>
                <div>
                    <strong>GR No.:</strong> {{ $bill->note->grno ?? '' }}
                </div>
                <div>
                    <strong>Delivery Note:-</strong> {{ $bill->note->deliverynote ?? '' }}
                </div>
            </div>
        </div>

        <!-- BANK -->
        <div style="margin-top: 0px; border: 1px solid #000; border-top: 0; padding: 4px 5px;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; font-size: 13px;">
                <div><strong>Bank Name:- {{ $settings->bank_name ?? 'BANK OF BARODA' }}</strong></div>
                <div><strong>Account No.:- {{ $settings->bank_account ?? '70940200002257' }}</strong></div>
                <div><strong>IFSC Code:- {{ $settings->bank_ifsc ?? 'BARB0DBAMRI' }}</strong></div>
            </div>
        </div>

        <!-- FOOTER -->
        <div style="margin-top: 0px; border: 1px solid #000; border-top: 0; padding: 4px 5px; display: flex; justify-content: space-between; flex-wrap: wrap;">
            <div style="font-size: 11px;">
                @php $terms = $settings->getTermsArray(); @endphp
                @foreach($terms as $term)
                    {{ $term }}<br>
                @endforeach
            </div>
            <div style="text-align: right;">
                <br>
                <strong>{{ $settings->footer_text ?? 'For A.B Shawls' }}</strong>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- ===== CONTINUED NOTICE — non-last pages, at the very end ===== --}}
        {{-- ============================================================ --}}
        @unless($showSummary)
            <div style="margin-top: 0px; text-align: center; font-style: italic; font-size: 12px; border: 1px dashed #000; border-top: 0; padding: 3px;">
                Continued on next page...
            </div>
        @endunless

    </div>
@endforeach