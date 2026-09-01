{{-- resources/views/bills/partials/product-form.blade.php --}}
<div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
        <i class="fas fa-info-circle"></i> <span id="productFormTitle">New Product</span>
    </div>
    <button type="button" class="btn-close" onclick="cancelProductForm()" aria-label="Close"></button>
</div>
<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label">Product Name <span class="text-danger">*</span></label>
        {{-- REMOVED required attribute - will be validated in JavaScript --}}
        <input type="text" id="product_name" class="form-control">
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Number</label>
        <input type="text" id="product_pnumber" class="form-control">
    </div>
    <div class="col-md-1 mb-3">
        <label class="form-label">Qty <span class="text-danger">*</span></label>
        <input type="number" id="product_qty" class="form-control" min="1" value="1">
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Unit</label>
        <input type="text" id="product_unit" class="form-control" placeholder="PCS">
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Price <span class="text-danger">*</span></label>
        {{-- REMOVED required attribute - will be validated in JavaScript --}}
        <input type="number" id="product_price" class="form-control" step="0.01" min="0">
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Discount (%)</label>
        <input type="number" id="product_discount" class="form-control" value="0" step="0.01" min="0" max="100">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">HSN/NSN Code</label>
        <input type="text" id="product_nsn" class="form-control">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Available Stock</label>
        <input type="text" id="product_stock" class="form-control" readonly>
    </div>
    <div class="col-md-6 mb-3 d-flex align-items-end gap-2">
        <button type="button" class="btn btn-success" onclick="addProductToList()">
            <i class="fas fa-plus"></i> Add Product
        </button>
        <button type="button" class="btn btn-secondary" onclick="cancelProductForm()">
            <i class="fas fa-times"></i> Cancel
        </button>
    </div>
</div>