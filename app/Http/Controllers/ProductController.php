<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        $products = Product::orderBy('database_id', 'desc')->get();
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'pnumber' => 'nullable|string|max:100',
            'qty' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'nsn_code' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Product::create($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Product added successfully!');
    }

    /**
     * Display the specified product (for AJAX and web).
     */
    public function show($id, Request $request)
    {
        $product = Product::findOrFail($id);
        
        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($product);
        }
        
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'pnumber' => 'nullable|string|max:100',
            'qty' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'nsn_code' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Check if product is used in any bill items
        if ($product->billItems()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete product with existing bill items!');
        }
        
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Search products for autocomplete - searches by name AND pnumber.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        
        // Search by name OR pnumber (both should work)
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('pnumber', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();
        
        return response()->json($products);
    }

    /**
     * Update product stock (for AJAX).
     */
    public function updateStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'qty' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $product = Product::findOrFail($id);
        $product->update(['qty' => $request->qty]);

        return response()->json(['success' => true, 'qty' => $product->qty]);
    }
}