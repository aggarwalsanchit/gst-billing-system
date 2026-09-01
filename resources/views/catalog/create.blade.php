@extends('layouts.app')

@section('title', 'Add Product')
@section('page-title', 'Add New Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus"></i> Add New Product</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('catalog.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Product Number <span class="text-danger">*</span></label>
                            <input type="text" name="product_no" class="form-control" required placeholder="e.g., 2162">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g., SHAWL NO. 2162">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Rate <span class="text-danger">*</span></label>
                            <input type="number" name="rate" class="form-control" required step="0.01" min="0" placeholder="82">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Size <span class="text-danger">*</span></label>
                            <input type="text" name="size" class="form-control" required placeholder="30/64 APROX">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Work <span class="text-danger">*</span></label>
                            <input type="text" name="work" class="form-control" required placeholder="BLOCK PRINTING">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Design <span class="text-danger">*</span></label>
                            <input type="text" name="design" class="form-control" required placeholder="GOLDEN DESIGN">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Material <span class="text-danger">*</span></label>
                            <input type="text" name="material" class="form-control" required placeholder="ROTOR SPUN WOOL">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Colours <span class="text-danger">*</span></label>
                            <input type="text" name="colours" class="form-control" required placeholder="WHITE ONLY">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Additional product details..."></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Upload a product image (JPEG, PNG, JPG - Max 2MB)</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('catalog.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection