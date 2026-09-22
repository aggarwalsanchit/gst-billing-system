<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\GstSetting;
use App\Models\InvoiceSetting;
use App\Helpers\InvoiceHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Adaptive pagination:
     *   - Single-page bills: max 16 items.
     *   - Multi-page bills:
     *       • page 1 jumps to 19 items whenever there's any surplus
     *       • last page cap = 15 − N  (13 for N=2, 12 for N=3, 11 for N=4, …)
     *       • middle pages (2..N−1) grow from 16 up to 19 only if needed
     *       • deficits reduce the last page first, then earlier pages
     *   - Capacity for N pages is 16 × N.
     */
    private function paginateItemsAdaptive(array $items): array
    {
        $totalItems = count($items);

        if ($totalItems === 0) {
            return [[]];
        }

        // ---- Single page: max 16 ----
        if ($totalItems <= 16) {
            return [$items];
        }

        // ---- Determine page count: capacity = 16 * N for N >= 2 ----
        $pageCount = 2;
        while ($totalItems > 16 * $pageCount) {
            $pageCount++;
        }

        $lastCap = 15 - $pageCount;

        // Start with default distribution
        $chunks = array_fill(0, $pageCount, 16);
        $chunks[$pageCount - 1] = $lastCap;

        $defaultSum = 16 * ($pageCount - 1) + $lastCap;

        if ($totalItems > $defaultSum) {
            // ================= GROW =================
            // Jump page 1 to 19 immediately
            $chunks[0] = 19;
            $newSum = array_sum($chunks);

            if ($newSum > $totalItems) {
                // Overshoot → reduce last page (but keep at least 1)
                $overshoot = $newSum - $totalItems;
                $chunks[$pageCount - 1] = max(1, $chunks[$pageCount - 1] - $overshoot);
            } elseif ($newSum < $totalItems) {
                // Still need more → grow middle pages (2 .. N-1) incrementally up to 19
                $needed = $totalItems - $newSum;
                for ($i = 1; $i < $pageCount - 1 && $needed > 0; $i++) {
                    $canGrow = 19 - $chunks[$i];
                    if ($canGrow <= 0) continue;
                    $grow = min($needed, $canGrow);
                    $chunks[$i] += $grow;
                    $needed -= $grow;
                }
                // Any remainder goes to last page (defensive; shouldn't happen)
                if ($needed > 0) {
                    $chunks[$pageCount - 1] += $needed;
                }
            }
        } elseif ($totalItems < $defaultSum) {
            // ================= SHRINK =================
            $excess = $defaultSum - $totalItems;

            // Reduce last page first (down to 1)
            $remove = min($excess, $chunks[$pageCount - 1] - 1);
            $chunks[$pageCount - 1] -= $remove;
            $excess -= $remove;

            // Then reduce middle pages from N-2 down to 1, but never below 16
            for ($i = $pageCount - 2; $i >= 1 && $excess > 0; $i--) {
                $shrinkable = max(0, $chunks[$i] - 16);
                if ($shrinkable <= 0) continue;
                $remove = min($excess, $shrinkable);
                $chunks[$i] -= $remove;
                $excess -= $remove;
            }

            // Then reduce middle pages below 16 if still needed
            for ($i = $pageCount - 2; $i >= 1 && $excess > 0; $i--) {
                $remove = min($excess, $chunks[$i] - 1);
                $chunks[$i] -= $remove;
                $excess -= $remove;
            }

            // Then page 1 as last resort
            if ($excess > 0) {
                $remove = min($excess, $chunks[0] - 1);
                $chunks[0] -= $remove;
                $excess -= $remove;
            }
        }

        // ---- Slice items into pages ----
        $pages  = [];
        $offset = 0;
        foreach ($chunks as $count) {
            $pages[] = array_slice($items, $offset, $count);
            $offset += $count;
        }

        return $pages;
    }

    /**
     * Build view data shared by show / pdf / print.
     */
    private function buildViewData(Bill $bill): array
    {
        $settings = InvoiceSetting::getSettings();
        $totals   = InvoiceHelper::calculateTotals($bill);

        $itemPages    = $this->paginateItemsAdaptive($bill->items->toArray());
        $itemsPerPage = 16; // kept for backwards compatibility in views

        $pageTotals = [];
        foreach ($itemPages as $index => $pageItems) {
            $pageSubtotal = array_sum(array_column($pageItems, 'total'));
            $pageTotals[$index] = $pageSubtotal;
        }

        return compact('bill', 'totals', 'itemPages', 'pageTotals', 'itemsPerPage', 'settings');
    }

    /**
     * Display the invoice.
     */
    public function show($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->orderBy('bill_date', 'desc')
            ->firstOrFail();

        return view('invoices.show', $this->buildViewData($bill));
    }

    /**
     * Download PDF invoice.
     */
    public function pdf($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->orderBy('bill_date', 'desc')
            ->firstOrFail();

        $pdf = Pdf::loadView('invoices.pdf', $this->buildViewData($bill));

        return $pdf->download('invoice-' . $bill_id . '.pdf');
    }

    /**
     * Print invoice.
     */
    public function print($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->orderBy('bill_date', 'desc')
            ->firstOrFail();

        return view('invoices.print', $this->buildViewData($bill));
    }
}