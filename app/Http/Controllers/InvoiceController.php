<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\GstSetting;
use App\Helpers\InvoiceHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display the invoice.
     */
    public function show($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->firstOrFail();
        
        // Get totals
        $totals = InvoiceHelper::calculateTotals($bill);
        
        // Paginate items (18 per page)
        $itemsPerPage = 18;
        $itemPages = InvoiceHelper::paginateItems($bill->items->toArray(), $itemsPerPage);
        
        // Calculate page totals
        $pageTotals = [];
        foreach ($itemPages as $index => $pageItems) {
            $pageSubtotal = array_sum(array_column($pageItems, 'total'));
            $pageTotals[$index] = $pageSubtotal;
        }
        
        return view('invoices.show', compact(
            'bill',
            'totals',
            'itemPages',
            'pageTotals',
            'itemsPerPage'
        ));
    }

    /**
     * Download PDF invoice.
     */
    public function pdf($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->firstOrFail();
        
        $totals = InvoiceHelper::calculateTotals($bill);
        $itemsPerPage = 18;
        $itemPages = InvoiceHelper::paginateItems($bill->items->toArray(), $itemsPerPage);
        $pageTotals = [];
        foreach ($itemPages as $index => $pageItems) {
            $pageSubtotal = array_sum(array_column($pageItems, 'total'));
            $pageTotals[$index] = $pageSubtotal;
        }
        
        $pdf = Pdf::loadView('invoices.pdf', compact('bill', 'totals', 'itemPages', 'pageTotals', 'itemsPerPage'));
        return $pdf->download('invoice-' . $bill_id . '.pdf');
    }

    /**
     * Print invoice.
     */
    public function print($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->firstOrFail();
        
        $totals = InvoiceHelper::calculateTotals($bill);
        $itemsPerPage = 18;
        $itemPages = InvoiceHelper::paginateItems($bill->items->toArray(), $itemsPerPage);
        $pageTotals = [];
        foreach ($itemPages as $index => $pageItems) {
            $pageSubtotal = array_sum(array_column($pageItems, 'total'));
            $pageTotals[$index] = $pageSubtotal;
        }
        
        return view('invoices.print', compact('bill', 'totals', 'itemPages', 'pageTotals', 'itemsPerPage'));
    }
}