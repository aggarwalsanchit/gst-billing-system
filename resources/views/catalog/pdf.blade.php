<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Catalog</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #fff; padding: 0; margin: 0; }
        
        .page {
            page-break-after: always;
            width: 100%;
            min-height: 100vh;
            padding: 15px 30px;
            position: relative;
            background: #fff;
            display: flex;
            flex-direction: column;
        }
        
        .page-header {
            text-align: center;
            padding: 5px 0 8px 0;
            border-bottom: 2px solid #333;
            margin-bottom: 8px;
            flex-shrink: 0;
        }
        
        .page-header h1 {
            font-size: 24px;
            color: #1a1a2e;
            margin: 0;
            letter-spacing: 3px;
            font-weight: 900;
        }
        
        .page-header p {
            font-size: 12px;
            color: #555;
            margin: 2px 0 0 0;
            letter-spacing: 1px;
        }
        
        .product-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 5px 0;
            width: 100%;
        }
        
        .product-image-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            width: 100%;
            text-align: center;
        }
        
        .product-image-wrapper img {
            max-width: 90%;
            max-height: 540px;
            width: auto;
            height: auto;
            display: inline-block;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            background: #fafafa;
        }
        
        .product-image-wrapper .no-image {
            color: #ccc;
            font-size: 16px;
            text-align: center;
            padding: 35px 20px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            background: #fafafa;
            width: 50%;
            max-width: 300px;
            display: inline-block;
        }
        
        .product-image-wrapper .no-image i {
            font-size: 45px;
            display: block;
            margin-bottom: 6px;
            color: #ddd;
        }
        
        .product-details {
            width: 100%;
            max-width: 700px;
            padding: 10px 0 6px 0;
            border-top: 2px solid #333;
            margin-top: 10px;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
        }
        
        .product-details .product-name {
            font-size: 28px;
            font-weight: 900;
            color: #1a1a2e;
            margin-bottom: 2px;
        }
        
        .product-details .product-rate {
            font-size: 28px;
            font-weight: 900;
            color: #28a745;
            margin-bottom: 6px;
        }
        
        .product-details .detail-row {
            font-size: 20px;
            color: #333;
            margin: 2px 0;
            line-height: 1.6;
        }
        
        .product-details .detail-row .label {
            font-weight: 700;
            color: #1a1a2e;
        }
        
        .product-details .detail-row .value {
            color: #444;
        }
        
        .product-details .description {
            font-size: 15px;
            color: #555;
            margin-top: 5px;
            font-style: italic;
            border-top: 1px dashed #ddd;
            padding-top: 5px;
        }
        
        .page-footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            margin-top: 6px;
            flex-shrink: 0;
        }
        
        .page-number {
            position: absolute;
            bottom: 10px;
            right: 30px;
            font-size: 10px;
            color: #999;
            font-weight: 500;
        }
        
        @media print {
            .page { page-break-after: always; padding: 12px 20px; }
            .product-image-wrapper img { max-height: 380px; }
        }
    </style>
</head>
<body>

@foreach($products as $index => $product)
<div class="page">
    <div class="page-header">
        <h1>PRODUCT CATALOG</h1>
        <p>A.B SHAWLS - Product Reference Guide</p>
    </div>

    <div class="product-container">
        <!-- ===== IMAGE - FAST BASE64 (No Network Calls) ===== -->
        <div class="product-image-wrapper">
            @php
                $imageData = null;
                if ($product->image_path) {
                    // Get the full path
                    $path = ltrim($product->image_path, '/');
                    $fullPath = public_path($path);
                    
                    // If not found, try storage path
                    if (!file_exists($fullPath)) {
                        $fullPath = storage_path('app/public/' . str_replace('storage/', '', $path));
                    }
                    
                    // If still not found, try catalog_images directly
                    if (!file_exists($fullPath)) {
                        $fullPath = public_path('storage/catalog_images/' . basename($path));
                    }
                    
                    if (file_exists($fullPath)) {
                        $imageData = base64_encode(file_get_contents($fullPath));
                    }
                }
            @endphp
            
            @if($imageData)
                <img src="data:image/jpeg;base64,{{ $imageData }}" alt="{{ $product->name }}">
            @else
                <div class="no-image">
                    <i class="fas fa-image"></i>
                    No Image Available
                </div>
            @endif
        </div>

        <!-- ===== DETAILS - CENTERED ===== -->
        <div class="product-details">
            <div class="product-name">{{ $product->name }}</div>
            <div class="product-rate">RATE - Rs. {{ number_format($product->rate, 0) }}/-</div>

            <div class="detail-row">
                <span class="label">SIZE -</span>
                <span class="value">{{ $product->size }}</span>
            </div>
            <div class="detail-row">
                <span class="label">WORK -</span>
                <span class="value">{{ $product->work }}</span>
            </div>
            <div class="detail-row">
                <span class="label">DESIGN -</span>
                <span class="value">{{ $product->design }}</span>
            </div>
            <div class="detail-row">
                <span class="label">MATERIAL -</span>
                <span class="value">{{ $product->material }}</span>
            </div>
            <div class="detail-row">
                <span class="label">AVAILABLE IN COLOURS -</span>
                <span class="value">{{ $product->colours }}</span>
            </div>

            @if($product->description)
                <div class="description">{{ $product->description }}</div>
            @endif
        </div>
    </div>

    <div class="page-footer">
        <p>Product Catalog - A.B SHAWLS | Page {{ $index + 1 }} of {{ count($products) }}</p>
    </div>
    <div class="page-number">Page {{ $index + 1 }}</div>
</div>
@endforeach

</body>
</html>