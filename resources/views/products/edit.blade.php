@extends('layouts.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit"></i> Edit Product</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('products.update', $product->database_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Number</label>
                            <input type="text" name="pnumber" class="form-control" value="{{ $product->pnumber }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="qty" class="form-control" value="{{ $product->qty }}" required min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control" value="{{ $product->unit }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" value="{{ $product->price }}" required step="0.01" min="0">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">NSN/HSN Code</label>
                            <input type="text" name="nsn_code" class="form-control" value="{{ $product->nsn_code }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection