<?php

namespace App\Http\Controllers;

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
        // Get the last bill ID
        $lastBill = BillId::orderBy('id', 'desc')->first();
        $nextBillId = $lastBill ? intval($lastBill->bill_id) + 1 : 3683;
        
        return view('bills.create', compact('nextBillId'));
    }

    /**
     * Store a new bill.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate basic
            $validator = Validator::make($request->all(), [
                'bill_id' => 'required|string|unique:billid,bill_id',
                'bill_date' => 'required|date',
                'customer_id' => 'required|exists:customer,customer_id',
                'products_json' => 'required|json'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Parse products
            $products = json_decode($request->products_json, true);
            
            if (empty($products)) {
                return redirect()->back()
                    ->with('error', 'Please add at least one product')
                    ->withInput();
            }

            // Get customer
            $customer = Customer::find($request->customer_id);
            if (!$customer) {
                return redirect()->back()
                    ->with('error', 'Customer not found')
                    ->withInput();
            }

            // ========== CREATE BILL ==========
            // Calculate size based on number of items
            $size = 440 + (count($products) * 20);
            
            $bill = Bill::create([
                'bill_id' => $request->bill_id,
                'customer_id' => $customer->customer_id,
                'bill_date' => $request->bill_date,
                'discount' => $request->discount ?? 0,
                'size' => $size,
                'transport' => $request->transport ?? 0,
                'package' => $request->package ?? 0
            ]);

            // ========== CREATE BILL ID TRACKING ==========
            BillId::create(['bill_id' => $request->bill_id]);

            // ========== CREATE NOTE ==========
            Note::create([
                'bill_id' => $request->bill_id,
                'customer_id' => $customer->customer_id,
                'bill_date' => $request->bill_date,
                'despatch' => $request->despatch ?? '',
                'deliverynote' => $request->deliverynote ?? '',
                'grno' => $request->grno ?? ''
            ]);

            // ========== ADD PRODUCTS TO BILL ==========
            foreach ($products as $product) {
                $productId = null;
                
                // If product has no product_id, it's a new product - save to catalog
                if (empty($product['product_id'])) {
                    // Check if product already exists in catalog by pnumber
                    $existingProduct = AllProduct::where('pnumber', $product['pnumber'])->first();
                    
                    if ($existingProduct) {
                        $productId = $existingProduct->product_id;
                    } else {
                        // Create new product in catalog
                        $newProduct = AllProduct::create([
                            'name' => $product['name'],
                            'pnumber' => $product['pnumber'] ?? '',
                            'unit' => $product['unit'] ?? 'PCS',
                            'price' => $product['price'],
                            'hsn_code' => $product['nsn'] ?? ''
                        ]);
                        $productId = $newProduct->product_id;
                    }
                } else {
                    $productId = $product['product_id'];
                }

                // Create bill item
                BillItem::create([
                    'bill_id' => $request->bill_id,
                    'Product' => $product['name'],
                    'pnumber' => $product['pnumber'] ?? '',
                    'qty' => $product['qty'],
                    'unit' => $product['unit'] ?? 'PCS',
                    'price' => $product['price'],
                    'nsn_code' => $product['nsn'] ?? '',
                    'total' => $product['qty'] * $product['price'],
                    'database_id' => null,
                    'product_id' => $productId
                ]);
            }

            DB::commit();

            // ========== REDIRECT TO INVOICE PAGE ==========
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
     */
    public function show($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->firstOrFail();
        
        return view('bills.show', compact('bill'));
    }

    /**
     * Show the bill editing page.
     */
    public function edit($bill_id)
    {
        $bill = Bill::with(['customer', 'items', 'note'])
            ->where('bill_id', $bill_id)
            ->firstOrFail();
        
        $products = Product::all();
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
                'bill_id' => 'required|string',
                'customer_id' => 'required|exists:customer,customer_id',
                'item' => 'required|string',
                'qty' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'pnumber' => 'nullable|string',
                'unit' => 'nullable|string',
                'nsn_code' => 'nullable|string',
                'database_id' => 'nullable|string',
                'discount' => 'nullable|numeric|min:0|max:100'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $total = $request->qty * $request->price;
            $discount = $request->discount ?? 0;
            
            // Apply discount if any
            if ($discount > 0) {
                $discountAmount = $total * ($discount / 100);
                $total = $total - $discountAmount;
            }

            // ===== CHECK IF PRODUCT EXISTS IN ALL_PRODUCTS =====
            $productId = null;
            $pnumber = $request->pnumber ?? '';
            
            // Try to find product by pnumber first
            if (!empty($pnumber)) {
                $existingProduct = AllProduct::where('pnumber', $pnumber)->first();
                if ($existingProduct) {
                    $productId = $existingProduct->product_id;
                }
            }
            
            // If not found by pnumber, try by name
            if (!$productId) {
                $existingProduct = AllProduct::where('name', $request->item)->first();
                if ($existingProduct) {
                    $productId = $existingProduct->product_id;
                }
            }
            
            // If product still not found, save it to all_products
            if (!$productId) {
                $newProduct = AllProduct::create([
                    'name' => $request->item,
                    'pnumber' => $pnumber,
                    'unit' => $request->unit ?? 'PCS',
                    'price' => $request->price,
                    'hsn_code' => $request->nsn_code ?? ''
                ]);
                $productId = $newProduct->product_id;
                
                // Flash message for new product
                session()->flash('info', 'New product "' . $request->item . '" has been added to the catalog.');
            }

            // Check if adding from inventory (has database_id)
            if ($request->has('database_id') && $request->database_id) {
                $product = Product::find($request->database_id);
                if (!$product) {
                    return redirect()->back()
                        ->with('error', 'Product not found in inventory!');
                }
                
                // Check stock
                if ($product->qty < $request->qty) {
                    return redirect()->back()
                        ->with('error', 'Insufficient stock! Available: ' . $product->qty);
                }
                
                // Add item to bill
                $billItem = BillItem::create([
                    'bill_id' => $request->bill_id,
                    'Product' => $request->item,
                    'pnumber' => $pnumber,
                    'qty' => $request->qty,
                    'unit' => $request->unit ?? 'PCS',
                    'price' => $request->price,
                    'nsn_code' => $request->nsn_code ?? '',
                    'total' => $total,
                    'database_id' => $request->database_id,
                    'product_id' => $productId
                ]);
                
                // Update stock
                $product->decrement('qty', $request->qty);
            } else {
                // Add from catalog or manual entry
                $billItem = BillItem::create([
                    'bill_id' => $request->bill_id,
                    'Product' => $request->item,
                    'pnumber' => $pnumber,
                    'qty' => $request->qty,
                    'unit' => $request->unit ?? 'PCS',
                    'price' => $request->price,
                    'nsn_code' => $request->nsn_code ?? '',
                    'total' => $total,
                    'database_id' => null,
                    'product_id' => $productId
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
        
        // If item is from inventory, restore stock
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
        $bill = Bill::where('bill_id', $bill_id)->firstOrFail();
        
        $bill->update([
            'discount' => $request->discount ?? 0,
            'transport' => $request->transport ?? 0,
            'package' => $request->package ?? 0,
        ]);

        return redirect()->route('bills.edit', $bill_id)
            ->with('success', 'Bill updated successfully!');
    }

    /**
     * Update bill notes.
     */
    public function updateNotes(Request $request, $bill_id)
    {
        $bill = Bill::where('bill_id', $bill_id)->firstOrFail();
        
        $note = Note::updateOrCreate(
            ['bill_id' => $bill_id],
            [
                'customer_id' => $bill->customer_id,
                'bill_date' => $bill->bill_date,
                'despatch' => $request->despatch ?? '',
                'deliverynote' => $request->deliverynote ?? '',
                'grno' => $request->grno ?? '',
            ]
        );

        return redirect()->route('bills.edit', $bill_id)
            ->with('success', 'Notes updated successfully!');
    }

    /**
     * Update bill date.
     */
    public function updateDate(Request $request, $bill_id)
    {
        $bill = Bill::where('bill_id', $bill_id)->firstOrFail();
        
        $bill->update([
            'bill_date' => $request->bill_date,
        ]);

        return redirect()->route('bills.edit', $bill_id)
            ->with('success', 'Date updated successfully!');
    }

    /**
     * Delete bill completely.
     */
    public function destroy($bill_id)
    {
        $bill = Bill::where('bill_id', $bill_id)->firstOrFail();
        
        // Restore stock for all inventory items
        foreach ($bill->items as $item) {
            if ($item->database_id) {
                $product = Product::find($item->database_id);
                if ($product) {
                    $product->increment('qty', $item->qty);
                }
            }
        }
        
        // Delete related records
        $bill->items()->delete();
        $bill->note()->delete();
        BillId::where('bill_id', $bill_id)->delete();
        $bill->delete();

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
            ->get();
        
        return view('bills.by-customer', compact('customer', 'bills'));
    }

    /**
     * Update bill customer.
     */
    public function updateCustomer(Request $request, $bill_id)
    {
        try {
            \Log::info('updateCustomer called', ['bill_id' => $bill_id, 'customer_id' => $request->customer_id]);
            
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customer,customer_id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Find the bill by bill_id (string) not by id
            $bill = Bill::where('bill_id', $bill_id)->first();
            
            if (!$bill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bill not found with ID: ' . $bill_id
                ], 404);
            }
            
            $customer = Customer::find($request->customer_id);
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }
            
            // Update bill customer
            $bill->update(['customer_id' => $customer->customer_id]);
            
            // Update note if exists
            if ($bill->note) {
                $bill->note->update(['customer_id' => $customer->customer_id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully!',
                'customer' => [
                    'customer_id' => $customer->customer_id,
                    'name' => $customer->name
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('updateCustomer error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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
            \Log::info('updateItem called', ['demo_id' => $demo_id, 'request' => $request->all()]);
            
            $validator = Validator::make($request->all(), [
                'item' => 'required|string',
                'qty' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0|max:100'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $item = BillItem::findOrFail($demo_id);
            $total = $request->qty * $request->price;
            $discount = $request->discount ?? 0;
            
            // Apply discount if any
            if ($discount > 0) {
                $discountAmount = $total * ($discount / 100);
                $total = $total - $discountAmount;
            }

            // ===== CHECK IF PRODUCT EXISTS IN ALL_PRODUCTS =====
            $productId = $item->product_id;
            $pnumber = $request->pnumber ?? '';
            
            // If product_id is null or product not found, try to find or create
            if (!$productId) {
                // Try to find by pnumber
                if (!empty($pnumber)) {
                    $existingProduct = AllProduct::where('pnumber', $pnumber)->first();
                    if ($existingProduct) {
                        $productId = $existingProduct->product_id;
                    }
                }
                
                // Try by name
                if (!$productId) {
                    $existingProduct = AllProduct::where('name', $request->item)->first();
                    if ($existingProduct) {
                        $productId = $existingProduct->product_id;
                    }
                }
                
                // Create new if still not found
                if (!$productId) {
                    $newProduct = AllProduct::create([
                        'name' => $request->item,
                        'pnumber' => $pnumber,
                        'unit' => $request->unit ?? 'PCS',
                        'price' => $request->price,
                        'hsn_code' => $request->nsn_code ?? ''
                    ]);
                    $productId = $newProduct->product_id;
                    session()->flash('info', 'New product "' . $request->item . '" has been added to the catalog.');
                }
            }

            $item->update([
                'Product' => $request->item,
                'pnumber' => $pnumber,
                'qty' => $request->qty,
                'unit' => $request->unit ?? 'PCS',
                'price' => $request->price,
                'nsn_code' => $request->nsn_code ?? '',
                'total' => $total,
                'product_id' => $productId
            ]);

            return redirect()->route('bills.edit', $item->bill_id)
                ->with('success', 'Item updated successfully!');

        } catch (\Exception $e) {
            \Log::error('updateItem error', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Error updating item: ' . $e->getMessage());
        }
    }
}