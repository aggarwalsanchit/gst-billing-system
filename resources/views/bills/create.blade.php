@extends('layouts.app')

@section('title', 'Create New Bill')
@section('page-title', 'Create New Bill')

@section('content')
{{-- ===== ALL HTML CONTENT HERE ===== --}}
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
@endsection

{{-- ===== FIX: Added @push with matching @endpush ===== --}}
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
        updatePreview();
    }
}

function goToStep3() {
    goToStep(3);
}

function goToStep4() {
    goToStep(4);
}

// ========== UPDATE PREVIEW ==========
function updatePreview() {
    const subtotal = productList.reduce((sum, p) => sum + p.total, 0);
    const discountPercent = parseFloat($('#overall_discount').val()) || 0;
    const discountAmount = subtotal * (discountPercent / 100);
    const transport = parseFloat($('#transport').val()) || 0;
    const packaging = parseFloat($('#package').val()) || 0;
    const grandTotal = subtotal - discountAmount + transport + packaging;

    $('#previewSubtotal').text('₹' + subtotal.toFixed(2));
    $('#previewDiscount').text('₹' + discountAmount.toFixed(2));
    $('#previewTransport').text('₹' + transport.toFixed(2));
    $('#previewPackaging').text('₹' + packaging.toFixed(2));
    $('#previewGrandTotal').text('₹' + grandTotal.toFixed(2));
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

    // Customer Search Event
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

    // Product Search Events
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
</script>
@endpush