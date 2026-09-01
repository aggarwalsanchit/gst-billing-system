@extends('layouts.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product - #' . $product->product_no)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit"></i> Edit Product</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('catalog.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Product Number <span class="text-danger">*</span></label>
                            <input type="text" name="product_no" class="form-control" value="{{ $product->product_no }}" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Rate <span class="text-danger">*</span></label>
                            <input type="number" name="rate" class="form-control" value="{{ $product->rate }}" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Size <span class="text-danger">*</span></label>
                            <input type="text" name="size" class="form-control" value="{{ $product->size }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Work <span class="text-danger">*</span></label>
                            <input type="text" name="work" class="form-control" value="{{ $product->work }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Design <span class="text-danger">*</span></label>
                            <input type="text" name="design" class="form-control" value="{{ $product->design }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Material <span class="text-danger">*</span></label>
                            <input type="text" name="material" class="form-control" value="{{ $product->material }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Colours <span class="text-danger">*</span></label>
                            <input type="text" name="colours" class="form-control" value="{{ $product->colours }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" {{ $product->is_active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$product->is_active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Product Image</label>
                            @if($product->image_path)
                                <div class="mb-2">
                                    <img src="{{ asset($product->image_path) }}" alt="Current Image" style="max-height: 150px;">
                                    <p class="text-muted">Current image</p>
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Upload a new image to replace the current one.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('catalog.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
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