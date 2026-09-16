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
     * Display the invoice.
     */
    public function show($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->orderBy('bill_date', 'desc')
            ->firstOrFail();

        $settings = InvoiceSetting::getSettings();
        $totals = InvoiceHelper::calculateTotals($bill);

        $itemsPerPage = 12;
        $itemPages = InvoiceHelper::paginateItems($bill->items->toArray(), $itemsPerPage);

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
            'itemsPerPage',
            'settings'
        ));
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

        $settings = InvoiceSetting::getSettings();
        $totals = InvoiceHelper::calculateTotals($bill);
        $itemsPerPage = 10;
        $itemPages = InvoiceHelper::paginateItems($bill->items->toArray(), $itemsPerPage);

        $pageTotals = [];
        foreach ($itemPages as $index => $pageItems) {
            $pageSubtotal = array_sum(array_column($pageItems, 'total'));
            $pageTotals[$index] = $pageSubtotal;
        }

        $pdf = Pdf::loadView('invoices.pdf', compact(
            'bill',
            'totals',
            'itemPages',
            'pageTotals',
            'itemsPerPage',
            'settings'
        ));

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

        $settings = InvoiceSetting::getSettings();
        $totals = InvoiceHelper::calculateTotals($bill);
        $itemsPerPage = 10;
        $itemPages = InvoiceHelper::paginateItems($bill->items->toArray(), $itemsPerPage);

        $pageTotals = [];
        foreach ($itemPages as $index => $pageItems) {
            $pageSubtotal = array_sum(array_column($pageItems, 'total'));
            $pageTotals[$index] = $pageSubtotal;
        }

        return view('invoices.print', compact(
            'bill',
            'totals',
            'itemPages',
            'pageTotals',
            'itemsPerPage',
            'settings'
        ));
    }
}