<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AllProductController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\GstSettingController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Controllers\StickerController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ========== CUSTOMER ROUTES ==========
Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
Route::resource('customers', CustomerController::class);

// ========== PRODUCT ROUTES ==========
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::resource('products', ProductController::class);

// ========== ALL PRODUCTS (CATALOG) ROUTES ==========
Route::get('/all-products/search', [AllProductController::class, 'search'])->name('all-products.search');
Route::resource('all-products', AllProductController::class);

// ========== BILL ROUTES ==========
Route::get('/bills/create', [BillController::class, 'create'])->name('bills.create');
Route::post('/bills/store', [BillController::class, 'store'])->name('bills.store');
Route::get('/bills/{bill_id}/edit', [BillController::class, 'edit'])->name('bills.edit');

// Bill Item routes
Route::post('/bills/{bill_id}/add-item', [BillController::class, 'addItem'])->name('bills.add-item');
Route::get('/bills/remove-item/{demo_id}', [BillController::class, 'removeItem'])->name('bills.remove-item');

// ===== FIX: Use POST with method spoofing for update =====
// This route accepts POST, but we'll use _method=PUT in the form
Route::put('/bills/update-item/{demo_id}', [BillController::class, 'updateItem'])->name('bills.update-item');

// Get bill item for editing
Route::get('/bill-items/{demo_id}', [BillController::class, 'getItem'])->name('bill-items.get');

// Update bill header
Route::put('/bills/{bill_id}/update-header', [BillController::class, 'updateHeader'])->name('bills.update-header');

// Update notes
Route::post('/bills/{bill_id}/update-notes', [BillController::class, 'updateNotes'])->name('bills.update-notes');

// Update date
Route::put('/bills/{bill_id}/update-date', [BillController::class, 'updateDate'])->name('bills.update-date');

// Update customer
Route::post('/bills/{bill_id}/update-customer', [BillController::class, 'updateCustomer'])->name('bills.update-customer');

// Delete bill
Route::delete('/bills/{bill_id}/delete', [BillController::class, 'destroy'])->name('bills.destroy');

// Resource route for bills (must come AFTER specific routes)
Route::resource('bills', BillController::class);

// Invoices
Route::get('/invoices/{bill_id}', [InvoiceController::class, 'show'])->name('invoices.show');
Route::get('/invoices/{bill_id}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
Route::get('/invoices/{bill_id}/print', [InvoiceController::class, 'print'])->name('invoices.print');

// GST Settings
Route::get('/settings/gst', [GstSettingController::class, 'index'])->name('settings.gst');
Route::put('/settings/gst', [GstSettingController::class, 'update'])->name('settings.gst.update');

// Bill filters
Route::get('/bills/by-date', [BillController::class, 'byDate'])->name('bills.by-date');
Route::get('/bills/by-customer/{customer_id}', [BillController::class, 'byCustomer'])->name('bills.by-customer');

// Product Catalog Routes
Route::prefix('catalog')->group(function () {
    Route::get('/', [ProductCatalogController::class, 'index'])->name('catalog.index');
    Route::get('/create', [ProductCatalogController::class, 'create'])->name('catalog.create');
    Route::post('/', [ProductCatalogController::class, 'store'])->name('catalog.store');
    Route::get('/{id}/edit', [ProductCatalogController::class, 'edit'])->name('catalog.edit');
    Route::put('/{id}', [ProductCatalogController::class, 'update'])->name('catalog.update');
    Route::delete('/{id}', [ProductCatalogController::class, 'destroy'])->name('catalog.destroy');
    Route::post('/export-pdf', [ProductCatalogController::class, 'exportPdf'])->name('catalog.export-pdf');
    Route::post('/{id}/toggle-status', [ProductCatalogController::class, 'toggleStatus'])->name('catalog.toggle-status');
});

// Sticker Label Generator
Route::get('/stickers', [StickerController::class, 'index'])->name('stickers.index');
Route::post('/stickers/generate', [StickerController::class, 'generate'])->name('stickers.generate');
Route::get('/stickers/print', [StickerController::class, 'print'])->name('stickers.print');
Route::get('/stickers/export-pdf', [StickerController::class, 'exportPdf'])->name('stickers.export-pdf');