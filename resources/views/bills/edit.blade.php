@extends('layouts.app')

@section('title', 'Edit Bill #' . $bill->bill_id)
@section('page-title', 'Bill #' . $bill->bill_id)

@section('content')
{{-- ===== ALL HTML CONTENT HERE ===== --}}
<div class="row">
    <!-- Bill Header Info -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Bill No:</strong> #{{ $bill->bill_id }}
                    </div>
                    <div class="col-md-3">
                        <strong>Date:</strong> {{ $bill->bill_date->format('d/m/Y') }}
                        <button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#dateModal">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <strong>Customer:</strong> <span id="customerNameDisplay">{{ $bill->customer->name ?? 'N/A' }}</span>
                        <button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#changeCustomerModal">
                            <i class="fas fa-user-edit"></i> Change
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('invoices.show', $bill->bill_id) }}" class="btn btn-success btn-sm" target="_blank">
                            <i class="fas fa-print"></i> View Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ... rest of the HTML content ... -->
</div>

<!-- Date Edit Modal -->
<div class="modal fade" id="dateModal" tabindex="-1">
    <!-- ... date modal content ... -->
</div>

<!-- Change Customer Modal -->
@include('bills.partials.customer-modal')
@endsection

{{-- ===== @push with matching @endpush ===== --}}
@push('scripts')
<script>
// Pass bill ID to JavaScript
const BILL_ID = '{{ $bill->bill_id }}';
const CUSTOMER_ID = '{{ $bill->customer_id }}';

// ========== PAGE-SPECIFIC VARIABLES ==========
let editSelectedProduct = null;
let editSearchTimeout = null;
let modalSearchTimeout = null;

// ========== UPDATE BILL CUSTOMER ==========
function updateBillCustomer() {
    const customerId = $('#modal_customer_id').val();
    
    if (!customerId) {
        alert('Please select a customer first');
        return;
    }

    const updateBtn = $('#modalUpdateCustomerBtn');
    const originalText = updateBtn.html();
    updateBtn.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    
    $.ajax({
        url: `/bills/${BILL_ID}/update-customer`,
        method: 'POST',
        data: {
            customer_id: customerId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $('#customerNameDisplay').text(response.customer.name);
                $('#changeCustomerModal').modal('hide');
                alert('Customer updated successfully!');
                location.reload();
            } else {
                alert('Error updating customer: ' + (response.message || 'Unknown error'));
                updateBtn.html(originalText).prop('disabled', false);
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error updating customer';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            alert(errorMsg);
            console.error(xhr);
            updateBtn.html(originalText).prop('disabled', false);
        }
    });
}

// ========== EDIT BILL ITEM ==========
function editBillItem(demoId) {
    $.ajax({
        url: `/bill-items/${demoId}`,
        method: 'GET',
        success: function(item) {
            $('#edit_demo_id').val(item.demo_id);
            $('#edit_product_name').val(item.Product);
            $('#edit_product_pnumber').val(item.pnumber || '');
            $('#edit_product_qty').val(item.qty);
            $('#edit_product_unit').val(item.unit || 'PCS');
            $('#edit_product_price').val(item.price);
            $('#edit_product_nsn').val(item.nsn_code || '');
            $('#edit_product_discount').val(item.discount || 0);
            $('#edit_product_stock').val('Editing Item');
            
            $('#editProductFormTitle').text('Edit Product');
            $('#editProductForm').show();
            $('#editAddProductBtnText').text('Update Product');
            $('#editProductSearch').val(item.Product);
            
            $('#addItemForm').attr('action', `/bills/update-item/${demoId}`);
            $('#addItemForm').find('input[name="_method"]').remove();
            $('#addItemForm').append('<input type="hidden" name="_method" value="PUT">');
            $('#addItemForm').find('input[name="edit_mode"]').remove();
            $('#addItemForm').append('<input type="hidden" name="edit_mode" value="1">');
            
            $('#edit_product_qty').focus().select();
        },
        error: function(xhr) {
            console.error('Error loading item:', xhr);
            alert('Error loading item details');
        }
    });
}

// ========== CANCEL EDIT PRODUCT FORM ==========
function cancelEditProductForm() {
    $('#edit_product_name').val('');
    $('#edit_product_pnumber').val('');
    $('#edit_product_unit').val('PCS');
    $('#edit_product_price').val('');
    $('#edit_product_nsn').val('');
    $('#edit_product_stock').val('');
    $('#edit_product_qty').val(1);
    $('#edit_product_discount').val(0);
    $('#edit_demo_id').val('');
    $('#editProductFormTitle').text('New Product');
    $('#editProductSearch').val('');
    $('#editProductSearch').removeClass('product-selected');
    editSelectedProduct = null;
    $('#editProductForm').hide();
    $('#editAddProductBtnText').text('Add Product');
    $('#editProductSearch').focus().select();
    
    $('#addItemForm').attr('action', `/bills/${BILL_ID}/add-item`);
    $('#addItemForm').find('input[name="_method"]').remove();
    $('#addItemForm').find('input[name="edit_mode"]').remove();
}

// ========== ADD ITEM FORM SUBMIT ==========
$('#addItemForm').on('submit', function(e) {
    const name = $('#edit_product_name').val().trim();
    const qty = parseInt($('#edit_product_qty').val()) || 0;
    const price = parseFloat($('#edit_product_price').val()) || 0;

    if (!name) {
        e.preventDefault();
        alert('Please enter product name');
        $('#edit_product_name').focus();
        return false;
    }
    if (qty <= 0) {
        e.preventDefault();
        alert('Please enter valid quantity');
        $('#edit_product_qty').focus();
        return false;
    }
    if (price <= 0) {
        e.preventDefault();
        alert('Please enter valid price');
        $('#edit_product_price').focus();
        return false;
    }

    const discount = $('#edit_product_discount').val() || 0;
    if ($('#addItemForm').find('input[name="discount"]').length === 0) {
        $('#addItemForm').append(`<input type="hidden" name="discount" value="${discount}">`);
    } else {
        $('#addItemForm').find('input[name="discount"]').val(discount);
    }

    return true;
});

// ========== MODAL EVENT LISTENERS ==========
$(document).ready(function() {
    // Modal Customer Search
    $('#modalCustomerSearch').on('keyup', function() {
        const query = $(this).val().trim();
        if (query.length < 2) {
            $('#modalSearchResults').hide().empty();
            return;
        }
        clearTimeout(modalSearchTimeout);
        modalSearchTimeout = setTimeout(function() { 
            searchModalCustomers(query); 
        }, 300);
    });

    $('#modalCustomerSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const name = $(this).val().trim();
            if (name.length > 1) {
                checkAndAddModalNewCustomer(name);
            }
        }
    });

    // Edit Product Search
    $('#editProductSearch').on('keyup', function() {
        const query = $(this).val().trim();
        if (query.length < 2) {
            $('#editProductSearchResults').hide().empty();
            return;
        }
        clearTimeout(editSearchTimeout);
        editSearchTimeout = setTimeout(function() { 
            searchEditProducts(query, '#editProductSearchResults'); 
        }, 300);
    });

    $('#editProductSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const query = $(this).val().trim();
            if (query.length > 1) {
                checkAndAddEditProduct(query);
            }
        }
    });

    // Close search results on outside click
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $('#modalSearchResults, #editProductSearchResults').hide();
        }
    });

    // Reset modal when closed
    $('#changeCustomerModal').on('hidden.bs.modal', function() {
        clearModalCustomer();
    });

    // Reset modal when opened
    $('#changeCustomerModal').on('shown.bs.modal', function() {
        $('#modalCustomerSearch').focus();
        $('#modalUpdateCustomerBtn').prop('disabled', true);
    });
});
</script>
@endpush