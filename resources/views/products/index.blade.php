@extends('layouts.app')

@section('title', 'Inventory')
@section('page-title', 'Inventory')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-boxes"></i> Product Inventory</h5>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>NSN Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $product->database_id }}</td>
                        <td><strong>{{ $product->name }}</strong></td>
                        <td>{{ $product->pnumber }}</td>
                        <td>
                            <span class="badge {{ $product->qty <= 5 ? 'bg-danger' : ($product->qty <= 20 ? 'bg-warning' : 'bg-success') }}">
                                {{ $product->qty }}
                            </span>
                        </td>
                        <td>{{ $product->unit }}</td>
                        <td>₹{{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->nsn_code }}</td>
                        <td>
                            <a href="{{ route('products.edit', $product->database_id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" onclick="confirmDelete('{{ route('products.destroy', $product->database_id) }}')" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection