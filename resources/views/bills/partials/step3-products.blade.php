{{-- resources/views/bills/partials/step3-products.blade.php --}}
<div class="card mb-4" id="step3Card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-boxes"></i> Products</h5>
        <span class="badge bg-primary" id="productCount">0 Products</span>
    </div>
    <div class="card-body">
        <!-- Product Search -->
        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Search Product <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <input type="text" id="productSearch" class="form-control form-control-lg" 
                           placeholder="Type product name or number..." autocomplete="off">
                    <div class="search-results-container" id="productSearchResults"></div>
                </div>
                <small class="text-muted">Type product name or number. Press Enter to add new.</small>
            </div>
        </div>

        <!-- Product Form -->
        <div id="productForm" style="display: none;">
            @include('bills.partials.product-form')
        </div>

        <!-- Product List -->
        <div class="mt-4" id="productListContainer" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6><i class="fas fa-list"></i> Added Products</h6>
                <span class="total-amount" id="totalAmount">₹0.00</span>
            </div>
            <div class="product-list-container" id="productList"></div>
            <div class="text-end mt-3">
                <small class="text-muted">Total Items: <span id="totalItems">0</span></small>
            </div>
        </div>

        <!-- Hidden inputs for products -->
        <div id="productInputs"></div>

        <!-- Next Button -->
        <div class="text-end mt-4">
            <button type="button" class="btn btn-primary btn-lg" onclick="goToStep4()">
                <i class="fas fa-arrow-right"></i> Next: Bill Details
            </button>
        </div>
    </div>
</div>