<?php

namespace App\Http\Controllers;

use App\Helpers\FinancialYearHelper;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\BillId;
use App\Models\Customer;
use App\Models\Product;
use App\Models\AllProduct;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    /**
     * Display a listing of bills.
     */
    public function index()
    {
        $bills = Bill::with(['customer', 'items'])
            ->orderBy('dateid', 'desc')
            ->get();

        return view('bills.index', compact('bills'));
    }

    /**
     * Show the form for creating a new bill.
     */
    public function create()
    {
        $nextBillId = $this->getNextBillId();

        return view('bills.create', compact('nextBillId'));
    }

    /**
     * Compute next bill number for the current financial year.
     */
    private function getNextBillId(): int
    {
        $fy = FinancialYearHelper::getFinancialYear();

        $lastBillThisFY = BillId::where('financial_year', $fy)
            ->orderBy('bill_id', 'desc')
            ->first();

        return $lastBillThisFY ? ((int) $lastBillThisFY->bill_id + 1) : 1;
    }

    /**
     * Store a new bill.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $fy = FinancialYearHelper::getFinancialYear($request->bill_date);

            $validator = Validator::make($request->all(), [
                'bill_id'       => 'required|string',
                'bill_date'     => 'required|date',
                'customer_id'   => 'required|exists:customer,customer_id',
                'products_json' => 'required|json',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Duplicate check scoped by FY
            $duplicate = BillId::where('financial_year', $fy)
                ->where('bill_id', $request->bill_id)
                ->exists();

            if ($duplicate) {
                return redirect()->back()
                    ->with('error', "Bill No. {$request->bill_id} already exists for FY {$fy}")
                    ->withInput();
            }

            $products = json_decode($request->products_json, true);

            if (empty($products)) {
                return redirect()->back()
                    ->with('error', 'Please add at least one product')
                    ->withInput();
            }

            $customer = Customer::find($request->customer_id);
            if (!$customer) {
                return redirect()->back()
                    ->with('error', 'Customer not found')
                    ->withInput();
            }

            // ========== CREATE BILL ==========
            $size = $request->input('size', 15);

            $bill = Bill::create([
                'bill_id'        => $request->bill_id,
                'customer_id'    => $customer->customer_id,
                'bill_date'      => $request->bill_date,
                'financial_year' => $fy,
                'discount'       => $request->discount ?? 0,
                'size'           => $size,
                'transport'      => $request->transport ?? 0,
                'package'        => $request->package ?? 0,
            ]);

            // ========== BILL ID TRACKING ==========
            BillId::create([
                'bill_id'        => $request->bill_id,
                'financial_year' => $fy,
            ]);

            // ========== NOTE ==========
            Note::create([
                'bill_id'      => $request->bill_id,
                'customer_id'  => $customer->customer_id,
                'bill_date'    => $request->bill_date,
                'despatch'     => $request->despatch ?? '',
                'deliverynote' => $request->deliverynote ?? '',
                'grno'         => $request->grno ?? '',
            ]);

            // ========== PRODUCTS ==========
            foreach ($products as $product) {
                $productId = null;

                if (empty($product['product_id'])) {
                    $existingProduct = AllProduct::where('pnumber', $product['pnumber'] ?? '')->first();

                    if ($existingProduct) {
                        $productId = $existingProduct->product_id;
                    } else {
                        $newProduct = AllProduct::create([
                            'name'     => $product['name'],
                            'pnumber'  => $product['pnumber'] ?? '',
                            'unit'     => $product['unit'] ?? 'PCS',
                            'price'    => $product['price'],
                            'hsn_code' => $product['nsn'] ?? '',
                        ]);
                        $productId = $newProduct->product_id;
                    }
                } else {
                    $productId = $product['product_id'];
                }

                $qty     = (float) $product['qty'];
                $price   = (float) $product['price'];
                $discPct = (float) ($product['discount'] ?? 0);

                $gross    = $qty * $price;
                $discAmt  = $gross * ($discPct / 100);
                $netTotal = $gross - $discAmt;

                BillItem::create([
                    'bill_id'     => $request->bill_id,
                    'Product'     => $product['name'],
                    'pnumber'     => $product['pnumber'] ?? '',
                    'qty'         => $qty,
                    'unit'        => $product['unit'] ?? 'PCS',
                    'price'       => $price,
                    'discount'    => $discPct,
                    'total'       => $gross,
                    'net_total'   => $netTotal,
                    'nsn_code'    => $product['nsn'] ?? '',
                    'database_id' => null,
                    'product_id'  => $productId,
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.show', $request->bill_id)
                ->with('success', 'Bill #' . $request->bill_id . ' created successfully with ' . count($products) . ' products!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating bill: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified bill.
     * Uses latest bill_date fallback if bill_id exists in multiple FYs.
     */
    public function show($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->orderBy('bill_date', 'desc')
            ->firstOrFail();

        return view('bills.show', compact('bill'));
    }

    /**
     * Show the bill editing page.
     * Uses latest bill_date fallback if bill_id exists in multiple FYs.
     */
    public function edit($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->orderBy('bill_date', 'desc')
            ->firstOrFail();

        $products    = Product::all();
        $allProducts = AllProduct::all();

        return view('bills.edit', compact('bill', 'products', 'allProducts'));
    }

    /**
     * Add item to bill.
     */
    public function addItem(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'bill_id'     => 'required|string',
            'customer_id' => 'required|exists:customer,customer_id',
            'item'        => 'required|string',
            'qty'         => 'required|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'pnumber'     => 'nullable|string',
            'unit'        => 'nullable|string',
            'nsn_code'    => 'nullable|string',
            'database_id' => 'nullable|string',
            'discount'    => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $name    = trim($request->item);
        $pnumber = trim($request->pnumber ?? '');

        // ===== CHECK DUPLICATE IN SAME BILL =====
        $duplicateQuery = BillItem::where('bill_id', $request->bill_id)
            ->where('Product', $name);

        if ($pnumber !== '') {
            $duplicateQuery->where('pnumber', $pnumber);
        }

        if ($duplicateQuery->exists()) {
            return redirect()->back()
                ->with('error', 'Item "' . $name . '" already exists in this bill. Use edit to change quantity.')
                ->withInput();
        }

        $qty     = (float) $request->qty;
        $price   = (float) $request->price;
        $discPct = (float) ($request->discount ?? 0);

        $gross   = $qty * $price;
        $discAmt = $gross * ($discPct / 100);
        $net     = $gross - $discAmt;

        // ===== FIND OR CREATE IN ALLPRODUCTS =====
        $productId = $this->resolveCatalogProduct($name, $pnumber, $request, $isNew);

        if ($isNew) {
            session()->flash('info', 'New product "' . $name . '" has been added to the catalog.');
        }

        // ===== CREATE BILL ITEM =====
        if ($request->filled('database_id')) {
            $product = Product::find($request->database_id);
            if (!$product) {
                return redirect()->back()->with('error', 'Product not found in inventory!');
            }

            if ($product->qty < $qty) {
                return redirect()->back()
                    ->with('error', 'Insufficient stock! Available: ' . $product->qty);
            }

            BillItem::create([
                'bill_id'     => $request->bill_id,
                'Product'     => $name,
                'pnumber'     => $pnumber,
                'qty'         => $qty,
                'unit'        => $request->unit ?? 'PCS',
                'price'       => $price,
                'discount'    => $discPct,
                'total'       => $gross,
                'net_total'   => $net,
                'nsn_code'    => $request->nsn_code ?? '',
                'database_id' => $request->database_id,
                'product_id'  => $productId,
            ]);

            $product->decrement('qty', $qty);
        } else {
            BillItem::create([
                'bill_id'     => $request->bill_id,
                'Product'     => $name,
                'pnumber'     => $pnumber,
                'qty'         => $qty,
                'unit'        => $request->unit ?? 'PCS',
                'price'       => $price,
                'discount'    => $discPct,
                'total'       => $gross,
                'net_total'   => $net,
                'nsn_code'    => $request->nsn_code ?? '',
                'database_id' => null,
                'product_id'  => $productId,
            ]);
        }

        return redirect()->route('bills.edit', $request->bill_id)
            ->with('success', 'Item added successfully!');

    } catch (\Exception $e) {
        \Log::error('addItem error', ['error' => $e->getMessage()]);
        return redirect()->back()
            ->with('error', 'Error adding item: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Remove item from bill.
     */
    public function removeItem($demo_id, Request $request)
    {
        $item = BillItem::findOrFail($demo_id);
        $bill_id = $item->bill_id;

        if ($item->database_id) {
            $product = Product::find($item->database_id);
            if ($product) {
                $product->increment('qty', $item->qty);
            }
        }

        $item->delete();

        return redirect()->route('bills.edit', $bill_id)
            ->with('success', 'Item removed successfully!');
    }

    /**
     * Update bill header (discount, transport, packaging).
     */
    public function updateHeader(Request $request, $bill_id)
    {
        $bill = Bill::where('bill_id', $bill_id)
            ->orderBy('bill_date', 'desc')
            ->firstOrFail();

        $bill->update([
            'discount'  => $request->discount ?? 0,
            'transport' => $request->transport ?? 0,
            'package'   => $request->package ?? 0,
            'size'      => $request->size ?? $bill->size,
        ]);

        return redirect()->route('bills.edit', $bill_id)
            ->with('success', 'Bill updated successfully!');
    }

    /**
     * Update bill notes.
     */
    public function updateNotes(Request $request, $bill_id)
    {
        $bill = Bill::where('bill_id', $bill_id)
            ->orderBy('bill_date', 'desc')
            ->firstOrFail();

        Note::updateOrCreate(
            ['bill_id' => $bill_id],
            [
                'customer_id'  => $bill->customer_id,
                'bill_date'    => $bill->bill_date,
                'despatch'     => $request->despatch ?? '',
                'deliverynote' => $request->deliverynote ?? '',
                'grno'         => $request->grno ?? '',
            ]
        );

        return redirect()->route('bills.edit', $bill_id)
            ->with('success', 'Notes updated successfully!');
    }

    /**
     * Update bill date.
     * Handles FY crossings.
     */
    public function updateDate(Request $request, $bill_id)
    {
        $bill = Bill::where('bill_id', $bill_id)
            ->orderBy('bill_date', 'desc')
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'bill_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $newFy = FinancialYearHelper::getFinancialYear($request->bill_date);
        $oldFy = $bill->financial_year;

        // If FY changes, ensure no collision with another bill
        if ($newFy !== $oldFy) {
            $collision = BillId::where('financial_year', $newFy)
                ->where('bill_id', $bill_id)
                ->exists();

            if ($collision) {
                return redirect()->back()
                    ->with('error', "Cannot move this bill to FY {$newFy} — bill No. {$bill_id} already exists in that year.");
            }
        }

        DB::transaction(function () use ($bill, $request, $newFy, $oldFy, $bill_id) {
            $bill->update([
                'bill_date'      => $request->bill_date,
                'financial_year' => $newFy,
            ]);

            BillId::where('bill_id', $bill_id)
                ->where('financial_year', $oldFy)
                ->update(['financial_year' => $newFy]);
        });

        return redirect()->route('bills.edit', $bill_id)
            ->with('success', 'Date updated successfully!');
    }

    /**
     * Delete bill completely.
     */
    public function destroy($bill_id)
    {
        $bill = Bill::where('bill_id', $bill_id)
            ->orderBy('bill_date', 'desc')
            ->firstOrFail();

        foreach ($bill->items as $item) {
            if ($item->database_id) {
                $product = Product::find($item->database_id);
                if ($product) {
                    $product->increment('qty', $item->qty);
                }
            }
        }

        DB::transaction(function () use ($bill, $bill_id) {
            $bill->items()->delete();
            $bill->note()->delete();

            BillId::where('bill_id', $bill_id)
                ->where('financial_year', $bill->financial_year)
                ->delete();

            $bill->delete();
        });

        return redirect()->route('bills.index')
            ->with('success', 'Bill deleted successfully!');
    }

    /**
     * Get bill for invoice.
     */
    public function invoice($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->orderBy('bill_date', 'desc')
            ->firstOrFail();

        return view('invoices.show', compact('bill'));
    }

    /**
     * Get bills by date.
     */
    public function byDate(Request $request)
    {
        $date = $request->query('date');

        $bills = Bill::with(['customer'])
            ->where('bill_date', $date)
            ->orderBy('bill_id', 'desc')
            ->get();

        return view('bills.by-date', compact('bills', 'date'));
    }

    /**
     * Get bills by customer.
     */
    public function byCustomer($customer_id)
    {
        $customer = Customer::findOrFail($customer_id);

        $bills = Bill::with(['customer'])
            ->where('customer_id', $customer_id)
            ->orderBy('bill_date', 'desc')
            ->get();

        return view('bills.by-customer', compact('customer', 'bills'));
    }

    /**
     * Update bill customer.
     */
    public function updateCustomer(Request $request, $bill_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customer,customer_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $bill = Bill::where('bill_id', $bill_id)
                ->orderBy('bill_date', 'desc')
                ->first();

            if (!$bill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bill not found with ID: ' . $bill_id,
                ], 404);
            }

            $customer = Customer::find($request->customer_id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            $bill->update(['customer_id' => $customer->customer_id]);

            if ($bill->note) {
                $bill->note->update(['customer_id' => $customer->customer_id]);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'Customer updated successfully!',
                'customer' => [
                    'customer_id' => $customer->customer_id,
                    'name'        => $customer->name,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('updateCustomer error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get bill item for editing.
     */
    public function getItem($demo_id)
    {
        try {
            $item = BillItem::findOrFail($demo_id);
            return response()->json($item);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Item not found'], 404);
        }
    }

    /**
     * Update bill item.
     */
    public function updateItem(Request $request, $demo_id)
{
    try {
        $validator = Validator::make($request->all(), [
            'item'     => 'required|string',
            'qty'      => 'required|integer|min:1',
            'price'    => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $item = BillItem::findOrFail($demo_id);

        $name    = trim($request->item);
        $pnumber = trim($request->pnumber ?? '');

        // ===== CHECK DUPLICATE IN SAME BILL (excluding current item) =====
        $duplicateQuery = BillItem::where('bill_id', $item->bill_id)
            ->where('Product', $name)
            ->where('demo_id', '!=', $demo_id);

        if ($pnumber !== '') {
            $duplicateQuery->where('pnumber', $pnumber);
        }

        if ($duplicateQuery->exists()) {
            return redirect()->back()
                ->with('error', 'Item "' . $name . '" already exists in this bill.')
                ->withInput();
        }

        $qty     = (float) $request->qty;
        $price   = (float) $request->price;
        $discPct = (float) ($request->discount ?? 0);

        $gross   = $qty * $price;
        $discAmt = $gross * ($discPct / 100);
        $net     = $gross - $discAmt;

        // ===== RESOLVE / UPDATE CATALOG PRODUCT =====
        if ($item->product_id) {
            // Update the existing linked catalog product
            $catalog = AllProduct::find($item->product_id);

            if ($catalog) {
                $catalog->update([
                    'name'     => $name,
                    'pnumber'  => $pnumber,
                    'unit'     => $request->unit ?? $catalog->unit,
                    'price'    => $price,
                    'hsn_code' => $request->nsn_code ?? $catalog->hsn_code,
                ]);
                $productId = $catalog->product_id;
            } else {
                // Linked product was deleted — recreate
                $productId = $this->resolveCatalogProduct($name, $pnumber, $request, $isNew);
            }
        } else {
            $productId = $this->resolveCatalogProduct($name, $pnumber, $request, $isNew);
        }

        $item->update([
            'Product'    => $name,
            'pnumber'    => $pnumber,
            'qty'        => $qty,
            'unit'       => $request->unit ?? 'PCS',
            'price'      => $price,
            'discount'   => $discPct,
            'total'      => $gross,
            'net_total'  => $net,
            'nsn_code'   => $request->nsn_code ?? '',
            'product_id' => $productId,
        ]);

        return redirect()->route('bills.edit', $item->bill_id)
            ->with('success', 'Item updated successfully!');

    } catch (\Exception $e) {
        \Log::error('updateItem error', ['error' => $e->getMessage()]);
        return redirect()->back()
            ->with('error', 'Error updating item: ' . $e->getMessage());
    }
}

/**
 * Find existing AllProduct or create a new one.
 * Returns the product_id. Sets $isNew to true if created.
 */
private function resolveCatalogProduct(string $name, string $pnumber, Request $request, &$isNew = false): int
{
    $isNew = false;

    // Match on name + pnumber to avoid false matches
    $query = AllProduct::where('name', $name);

    if ($pnumber !== '') {
        $query->where('pnumber', $pnumber);
    } else {
        $query->where(function ($q) {
            $q->whereNull('pnumber')->orWhere('pnumber', '');
        });
    }

    $existing = $query->first();

    if ($existing) {
        return $existing->product_id;
    }

    $new = AllProduct::create([
        'name'     => $name,
        'pnumber'  => $pnumber,
        'unit'     => $request->unit ?? 'PCS',
        'price'    => $request->price,
        'hsn_code' => $request->nsn_code ?? '',
    ]);

    $isNew = true;
    return $new->product_id;
}
}