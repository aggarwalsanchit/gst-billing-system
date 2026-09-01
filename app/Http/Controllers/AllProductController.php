<?php

namespace App\Http\Controllers;

use App\Models\AllProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AllProductController extends Controller
{
    /**
     * Display a listing of all products.
     */
    public function index()
    {
        $products = AllProduct::orderBy('product_id', 'desc')->get();
        return view('all-products.index', compact('products'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'pnumber' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'hsn_code' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        AllProduct::create($request->all());

        return redirect()->route('all-products.index')
            ->with('success', 'Product added to catalog successfully!');
    }

    /**
     * Display the specified product (for AJAX).
     */
    public function show($id, Request $request)
    {
        $product = AllProduct::findOrFail($id);
        
        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($product);
        }
        
        return view('all-products.show', compact('product'));
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
        
        $products = AllProduct::where('name', 'LIKE', "%{$query}%")
            ->orWhere('pnumber', 'LIKE', "%{$query}%")
            ->limit(20)
            ->get()
            ->map(function($product) {
                return [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'pnumber' => $product->pnumber,
                    'unit' => $product->unit,
                    'price' => $product->price,
                    'hsn_code' => $product->hsn_code,
                ];
            });
        
        return response()->json($products);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = AllProduct::findOrFail($id);
        
        // Check if product is used in any bill items
        if ($product->billItems()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete product with existing bill items!');
        }
        
        $product->delete();

        return redirect()->route('all-products.index')
            ->with('success', 'Product removed from catalog successfully!');
    }
}