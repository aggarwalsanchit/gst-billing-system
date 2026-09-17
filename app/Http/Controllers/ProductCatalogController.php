<?php

namespace App\Http\Controllers;

use App\Models\ProductCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\PdfToText\Pdf as PdfToText;

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

        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $pdf = Pdf::loadView('catalog.pdf', compact('products'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
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

    /**
     * Show the PDF import form.
     */
    public function importForm()
    {
        return view('catalog.import');
    }

    /**
     * Handle PDF import.
     */
    public function importPdf(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:20480', // 20MB
        ]);

        // Ensure temp directory exists
        if (!File::exists(storage_path('app'))) {
            File::makeDirectory(storage_path('app'), 0755, true);
        }

        // Move uploaded PDF to temp location
        $uploadedPdf = $request->file('pdf');
        $tempFilename = 'temp_import_' . time() . '.pdf';
        $uploadedPdf->move(storage_path('app'), $tempFilename);
        $tempPdf = storage_path('app/' . $tempFilename);

        // Extract text
        try {
            $text = PdfToText::getText($tempPdf);
        } catch (\Exception $e) {
            File::delete($tempPdf);
            return redirect()->back()->with('error', 'Failed to read PDF: ' . $e->getMessage());
        }

        // Parse products
        $products = $this->parsePdfText($text);

        if (empty($products)) {
            File::delete($tempPdf);
            return redirect()->back()->with('error', 'No products could be parsed. Make sure the PDF matches the catalog format.');
        }

        // Save (skip duplicates)
        $inserted = 0;
        $skipped  = 0;

        foreach ($products as $p) {
            if (ProductCatalog::where('product_no', $p['product_no'])->exists()) {
                $skipped++;
                continue;
            }

            ProductCatalog::create([
                'product_no'  => $p['product_no'],
                'name'        => $p['name'],
                'rate'        => $p['rate'],
                'size'        => $p['size'],
                'work'        => $p['work'],
                'design'      => $p['design'],
                'material'    => $p['material'],
                'colours'     => $p['colours'],
                'description' => $p['description'] ?? null,
                'image_path'  => null,
                'is_active'   => true,
            ]);

            $inserted++;
        }

        File::delete($tempPdf);

        return redirect()->route('catalog.index')
            ->with('success', "Imported {$inserted} products. Skipped {$skipped} duplicates.");
    }

/**
 * Parse extracted PDF text into product rows.
 */
private function parsePdfText(string $text): array
{
    // Flatten all whitespace
    $flat = preg_replace('/\s+/', ' ', $text);
    $flat = trim($flat);

    // Remove page marker lines
    $flat = preg_replace('/=====\s*Page\s*\d+\s*=====/i', ' ', $flat);

    // Match each product
    $pattern = '/DESIGN\s*NO\.?\s*(\d+(?:\s*-\s*\d+)?)\s+RATE\s*-?\s*([\d,\.]+)\s*\/\s*-?\s*SIZE\s*-?\s*([\d"\/\s\.]+?)\s*(?:APPROX\.?|APPROX)\s*\.?\s*(.*?)(?=DESIGN\s*NO\.|\z)/is';

    $products = [];

    if (preg_match_all($pattern, $flat, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $designNo    = trim(preg_replace('/\s+/', ' ', $m[1]));
            $rate        = (float) str_replace(',', '', $m[2]);
            $size        = trim(preg_replace('/\s+/', ' ', $m[3]));
            $description = trim($m[4], " \t\n\r\0\x0B-");

            if (empty($designNo)) {
                continue;
            }

            $products[] = [
                'product_no'  => preg_replace('/\s+/', '', $designNo),
                'name'        => 'DESIGN NO. ' . $designNo,
                'rate'        => $rate,
                'size'        => $size,
                'work'        => '',
                'design'      => '',
                'material'    => '',
                'colours'     => '',
                'description' => $description,
            ];
        }
    }

    return $products;
}
}