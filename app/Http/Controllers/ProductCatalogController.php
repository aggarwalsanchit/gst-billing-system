<?php

namespace App\Http\Controllers;

use App\Models\ProductCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductCatalogController extends Controller
{
    /**
     * Display the product catalog.
     */
    public function index()
    {
        $products = ProductCatalog::orderBy('product_no')->paginate(20);
        return view('catalog.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('catalog.create');
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_no' => 'required|string|unique:product_catalog,product_no',
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'size' => 'required|string|max:50',
            'work' => 'required|string|max:255',
            'design' => 'required|string|max:255',
            'material' => 'required|string|max:255',
            'colours' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['image', '_token']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('catalog_images', $filename, 'public');
            $data['image_path'] = '/storage/' . $path;
        }

        ProductCatalog::create($data);

        return redirect()->route('catalog.index')
            ->with('success', 'Product added successfully!');
    }

    /**
     * Show the form for editing a product.
     */
    public function edit($id)
    {
        $product = ProductCatalog::findOrFail($id);
        return view('catalog.edit', compact('product'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, $id)
    {
        $product = ProductCatalog::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'product_no' => 'required|string|unique:product_catalog,product_no,' . $id,
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'size' => 'required|string|max:50',
            'work' => 'required|string|max:255',
            'design' => 'required|string|max:255',
            'material' => 'required|string|max:255',
            'colours' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['image', '_token', '_method']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Sanitize filename - remove spaces and special characters
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(' ', '_', $originalName)) . '.' . $extension;
            
            $path = $image->storeAs('catalog_images', $filename, 'public');
            $data['image_path'] = '/storage/' . $path;
        }

        $product->update($data);

        return redirect()->route('catalog.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product.
     */
    public function destroy($id)
    {
        $product = ProductCatalog::findOrFail($id);
        
        // Delete image
        if ($product->image_path) {
            $oldPath = str_replace('/storage/', '', $product->image_path);
            Storage::disk('public')->delete($oldPath);
        }

        $product->delete();

        return redirect()->route('catalog.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Export catalog to PDF.
     */
    public function exportPdf()
    {
        $products = ProductCatalog::orderBy('product_no')->get();
        
        // Increase memory and execution time for large catalogs
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        
        $pdf = Pdf::loadView('catalog.pdf', compact('products'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false, // Disable remote for faster loading
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);
        
        return $pdf->download('product-catalog.pdf');
    }

    /**
     * Toggle product status.
     */
    public function toggleStatus($id)
    {
        $product = ProductCatalog::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        return redirect()->back()
            ->with('success', 'Product status updated!');
    }
}