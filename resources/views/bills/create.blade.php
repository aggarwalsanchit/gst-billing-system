@extends('layouts.app')

@section('title', 'Create New Bill')
@section('page-title', 'Create New Bill')

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Step Indicator -->
        <div class="step-indicator" id="stepIndicator">
            <div class="step completed" id="step1">
                <span class="step-number">1</span>
                <span>Bill Info</span>
            </div>
            <div class="step-line completed" id="line1"></div>
            <div class="step completed" id="step2">
                <span class="step-number">2</span>
                <span>Customer</span>
            </div>
            <div class="step-line completed" id="line2"></div>
            <div class="step active" id="step3">
                <span class="step-number">3</span>
                <span>Products</span>
            </div>
            <div class="step-line" id="line3"></div>
            <div class="step" id="step4">
                <span class="step-number">4</span>
                <span>Details</span>
            </div>
        </div>

        <form action="{{ route('bills.store') }}" method="POST" id="billForm">
            @csrf

            <!-- Hidden fields -->
            <input type="hidden" name="customer_id" id="customer_id" value="{{ $customer_id ?? '' }}">
            <input type="hidden" name="is_existing" id="is_existing" value="{{ $is_existing ?? 0 }}">
            <input type="hidden" name="bill_id" value="{{ $nextBillId }}">
            <input type="hidden" name="bill_date" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="state" id="state" value="{{ $customer_state ?? '' }}">

            <!-- Step 1: Bill Info -->
            @include('bills.partials.step1-bill-info')

            <!-- Step 2: Customer -->
            @include('bills.partials.step2-customer')

            <!-- Step 3: Products -->
            @include('bills.partials.step3-products')

            <!-- Step 4: Details -->
            @include('bills.partials.step4-details')
        </form>
    </div>
</div>

<!-- Modals -->
@include('bills.partials.modals')
@include('bills.partials.customer-modal')
@endsection

@push('scripts')
<script>
// ========== PAGE-SPECIFIC VARIABLES ==========
let currentStep = 3;

// ========== STEP NAVIGATION ==========
function goToStep(step) {
    $('#step3Card, #step4Card').addClass('section-hidden');

    if (step === 3) {
        $('#step3Card').removeClass('section-hidden');
        $('#step3').addClass('active');
        $('#step4').removeClass('active');
        $('#line3').removeClass('active');
        currentStep = 3;
        $('#step4ProductList').empty(); // Clear step-4 product list when going back
    } else if (step === 4) {
        if (productList.length === 0) {
            alert('Please add at least one product first');
            return;
        }
        $('#step4Card').removeClass('section-hidden');
        $('#step4').addClass('active');
        $('#step3').removeClass('active');
        $('#line3').addClass('active');
        currentStep = 4;

        // Render products in step 4
        renderStep4Products();
        updatePreview();
    }
}

function goToStep3() { goToStep(3); }
function goToStep4() { goToStep(4); }

// ========== RENDER PRODUCTS IN STEP 4 ==========
function renderStep4Products() {
    const container = $('#step4ProductList');
    container.empty();

    if (productList.length === 0) {
        container.html('<div class="alert alert-warning">No products added.</div>');
        return;
    }

    productList.forEach((product, index) => {
        const qty      = parseFloat(product.qty) || 0;
        const price    = parseFloat(product.price) || 0;
        const discPct  = parseFloat(product.discount) || 0;
        const gross    = qty * price;
        const discAmt  = gross * (discPct / 100);
        const net      = gross - discAmt;

        const discountText = discPct > 0
            ? `<span class="badge bg-warning ms-1">${discPct}% off</span>`
            : '';

        container.append(`
            <div class="product-item">
                <div class="product-info">
                    <div>
                        <strong>${product.name}</strong>
                        <span class="badge bg-secondary ms-2">#${product.pnumber || 'N/A'}</span>
                        ${product.product_id ? '<span class="badge bg-info ms-1">Catalog</span>' : '<span class="badge bg-warning ms-1">New</span>'}
                        ${discountText}
                        <div class="text-muted small">
                            ${qty} × ₹${price.toFixed(2)} = ₹${gross.toFixed(2)}
                            ${discAmt > 0 ? `<span class="text-danger ms-2">− ₹${discAmt.toFixed(2)}</span>` : ''}
                            <span class="ms-2">Net: ₹${net.toFixed(2)}</span>
                            <span class="ms-2">Unit: ${product.unit}</span>
                            ${product.nsn ? `<span class="ms-2">HSN: ${product.nsn}</span>` : ''}
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeProductFromStep4(${index})" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `);
    });
}

// ========== REMOVE PRODUCT FROM STEP 4 ==========
function removeProductFromStep4(index) {
    if (confirm('Remove this product from the bill?')) {
        productList.splice(index, 1);

        // If no products left, go back to step 3
        if (productList.length === 0) {
            goToStep(3);
            updateTotals();
            renderProductList();
            $('#productCount').text('0 Products');
            return;
        }

        renderStep4Products();
        updatePreview();
        updateTotals();
        renderProductList();
        $('#productCount').text(productList.length + ' Products');
    }
}

// ========== UPDATE PREVIEW (with per-item + overall discount) ==========
function updatePreview() {
    let grossSubtotal   = 0;
    let itemDiscountAmt = 0;
    let netSubtotal     = 0;

    productList.forEach(p => {
        const qty     = parseFloat(p.qty) || 0;
        const price   = parseFloat(p.price) || 0;
        const discPct = parseFloat(p.discount) || 0;

        const gross = qty * price;
        const disc  = gross * (discPct / 100);
        const net   = gross - disc;

        grossSubtotal   += gross;
        itemDiscountAmt += disc;
        netSubtotal     += net;

        // Keep stored values in sync
        p.total     = gross;
        p.net_total = net;
    });

    const overallDiscPct = parseFloat($('#overall_discount').val()) || 0;
    const overallDiscAmt = netSubtotal * (overallDiscPct / 100);
    const afterDiscount  = netSubtotal - overallDiscAmt;

    const transport  = parseFloat($('#transport').val()) || 0;
    const packaging  = parseFloat($('#package').val()) || 0;
    const grossTotal = afterDiscount + transport + packaging;

    $('#previewGrossSubtotal').text('₹' + grossSubtotal.toFixed(2));
    $('#previewItemDiscount').text('₹' + itemDiscountAmt.toFixed(2));
    $('#previewNetSubtotal').text('₹' + netSubtotal.toFixed(2));
    $('#previewOverallDiscount').text('₹' + overallDiscAmt.toFixed(2));
    $('#previewAfterDiscount').text('₹' + afterDiscount.toFixed(2));
    $('#previewTransport').text('₹' + transport.toFixed(2));
    $('#previewPackaging').text('₹' + packaging.toFixed(2));
    $('#previewGrossTotal').text('₹' + grossTotal.toFixed(2));
    $('#previewGrandTotal').text('₹' + grossTotal.toFixed(2)); // Pre-GST preview

    // Also update step4 header totals
    $('#step4TotalItems').text(productList.reduce((sum, p) => sum + (parseFloat(p.qty) || 0), 0));
    $('#step4TotalAmount').text('₹' + netSubtotal.toFixed(2));
}

// ========== RESET ==========
function resetAll() {
    if (confirm('Reset everything?')) {
        location.reload();
    }
}

// ========== FORM SUBMIT ==========
$('#billForm').on('submit', function(e) {
    if (!selectedCustomer) {
        e.preventDefault();
        alert('Please select a customer first');
        return false;
    }
    if (productList.length === 0) {
        e.preventDefault();
        alert('Please add at least one product');
        return false;
    }
    updateHiddenInputs();
    return true;
});

// ========== AUTO-PREVIEW UPDATE ==========
$(document).ready(function() {
    $('#overall_discount, #transport, #package').on('input', function() {
        if (currentStep === 4) {
            updatePreview();
        }
    });

    // Customer Search
    $('#customerSearch').on('keyup', function() {
        const query = $(this).val().trim();
        if (query.length < 2) {
            $('#searchResults').hide().empty();
            return;
        }
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            searchCustomers(query, '#searchResults');
        }, 300);
    });

    $('#customerSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const name = $(this).val().trim();
            if (name.length > 1) {
                checkAndAddNewCustomer(name, '#searchResults');
            }
        }
    });

    // Product Search
    $('#productSearch').on('keyup', function() {
        const query = $(this).val().trim();
        if (query.length < 2) {
            $('#productSearchResults').hide().empty();
            return;
        }
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            searchProducts(query, '#productSearchResults');
        }, 300);
    });

    $('#productSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const query = $(this).val().trim();
            if (query.length > 1) {
                checkAndAddNewProduct(query);
            }
        }
    });

    // Quick add on Enter
    $('#product_qty, #product_price, #product_discount').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            addProductToList();
        }
    });

    // Close search results on outside click
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $('#searchResults, #productSearchResults').hide();
        }
    });
});

// ========== UPDATE BILL CUSTOMER (create page — local state only, no server call) ==========
function updateBillCustomer() {
    if (!modalSelectedCustomer) {
        alert('No customer selected');
        return;
    }

    // Use the modal's selected customer as the current one
    selectedCustomer = modalSelectedCustomer;
    isNewCustomer = false;

    $('#customer_id').val(selectedCustomer.customer_id);
    $('#is_existing').val(1);
    $('#state').val(selectedCustomer.state);

    $('#displayName').text(selectedCustomer.name);
    $('#displayAddress').text(selectedCustomer.address || 'Address: N/A');
    $('#displayPhone').text('Phone: ' + (selectedCustomer.phone || 'N/A'));
    $('#displayGst').text('GST: ' + (selectedCustomer.gstnumber || 'N/A'));
    $('#displayPan').text('PAN: ' + (selectedCustomer.panno || 'N/A'));
    $('#displayState').text('State: ' + (selectedCustomer.state || 'N/A'));

    $('#customerDisplay').show();
    $('#newCustomerForm').hide();
    $('#customerSearch').val(selectedCustomer.name);
    $('#customerSearch').addClass('customer-selected');
    $('#customerStatus').removeClass('bg-secondary bg-warning').addClass('bg-success').text('Customer Selected');
    $('#state_display').val(selectedCustomer.state);

    // Close the modal
    $('#changeCustomerModal').modal('hide');
}
</script>
@endpush