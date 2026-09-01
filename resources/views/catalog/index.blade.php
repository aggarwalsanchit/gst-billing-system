@extends('layouts.app')

@section('title', 'Product Catalog')
@section('page-title', 'Product Catalog')

@section('content')
<style>
    .catalog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px 0;
    }
    .catalog-card {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        padding: 15px;
    }
    .catalog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .catalog-card .product-image {
        width: 100%;
        height: 200px;
        object-fit: contain;
        background: #f8f9fa;
        border-radius: 4px;
    }
    .catalog-card .product-no {
        font-weight: bold;
        color: #007bff;
        font-size: 18px;
    }
    .catalog-card .product-name {
        font-size: 14px;
        color: #333;
        margin: 5px 0;
    }
    .catalog-card .product-details {
        font-size: 12px;
        color: #666;
        margin: 3px 0;
    }
    .catalog-card .product-rate {
        font-weight: bold;
        color: #28a745;
        font-size: 18px;
    }
    .catalog-card .actions {
        margin-top: 10px;
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    .catalog-card .badge-status {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .search-box {
        max-width: 300px;
    }
    .product-count {
        font-size: 14px;
        color: #6c757d;
    }
</style>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5><i class="fas fa-book"></i> Product Catalog</h5>
            <span class="product-count">Total: {{ $products->count() }} products</span>
        </div>
        <div>
            <form action="{{ route('catalog.export-pdf') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </form>
            <a href="{{ route('catalog.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Search -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="productSearch" class="form-control" placeholder="Search by product number or name...">
            </div>
        </div>

        <!-- Product Grid -->
        <div class="catalog-grid" id="productGrid">
            @forelse($products as $product)
            <div class="catalog-card product-item" data-product="{{ $product->product_no }}" data-name="{{ $product->name }}">
                <div style="position: relative;">
                    <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }} badge-status">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                @if($product->image_path)
                    <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="product-image">
                @else
                    <div class="product-image d-flex align-items-center justify-content-center bg-light">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                @endif
                <div class="mt-2">
                    <div class="product-no">#{{ $product->product_no }}</div>
                    <div class="product-name"><strong>{{ $product->name }}</strong></div>
                    <div class="product-details"><strong>Rate:</strong> ₹{{ number_format($product->rate, 2) }}</div>
                    <div class="product-details"><strong>Size:</strong> {{ $product->size }}</div>
                    <div class="product-details"><strong>Work:</strong> {{ $product->work }}</div>
                    <div class="product-details"><strong>Design:</strong> {{ $product->design }}</div>
                    <div class="product-details"><strong>Material:</strong> {{ $product->material }}</div>
                    <div class="product-details"><strong>Colours:</strong> {{ $product->colours }}</div>
                    @if($product->description)
                        <div class="product-details"><strong>Description:</strong> {{ Str::limit($product->description, 100) }}</div>
                    @endif
                </div>
                <div class="actions">
                    <a href="{{ route('catalog.edit', $product->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('catalog.toggle-status', $product->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $product->is_active ? 'btn-secondary' : 'btn-success' }}">
                            <i class="fas {{ $product->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                            {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form action="{{ route('catalog.destroy', $product->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5>No products found</h5>
                <p class="text-muted">Add your first product to the catalog.</p>
                <a href="{{ route('catalog.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            </div>
            @endforelse
        </div>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>

@push('scripts')
<script>
    // Search functionality
    document.getElementById('productSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.product-item');
        
        items.forEach(item => {
            const productNo = item.dataset.product.toLowerCase();
            const name = item.dataset.name.toLowerCase();
            
            if (productNo.includes(searchTerm) || name.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection