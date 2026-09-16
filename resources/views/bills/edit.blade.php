@extends('layouts.app')

@section('title', 'Edit Bill #' . $bill->bill_id)
@section('page-title', 'Bill #' . $bill->bill_id)

@section('content')
<div class="row">
    <div class="col-md-12">

        {{-- ===== BILL HEADER ===== --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Bill #{{ $bill->bill_id }}</h5>
                <div>
                    <a href="{{ route('invoices.show', $bill->bill_id) }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-print"></i> View Invoice
                    </a>
                    <a href="{{ route('bills.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Bill No:</strong> #{{ $bill->bill_id }}
                    </div>
                    <div class="col-md-3">
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($bill->bill_date)->format('d/m/Y') }}
                        <button class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#dateModal">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <strong>Customer:</strong>
                        <span id="customerNameDisplay">{{ $bill->customer->name ?? 'N/A' }}</span>
                        <button class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#changeCustomerModal">
                            <i class="fas fa-user-edit"></i> Change
                        </button>
                    </div>
                    <div class="col-md-2 text-end">
                        @if($bill->customer)
                            <small class="text-muted">
                                {{ $bill->customer->state ?? '' }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== ITEMS TABLE ===== --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-boxes"></i> Items</h5>
                <span class="badge bg-primary">{{ $bill->items->count() }} Products</span>
            </div>
            <div class="card-body">
                @if($bill->items->count() > 0)
                    @php
                        $anyItemDiscount = $bill->items->contains(fn($i) => ((float)($i->discount ?? 0)) > 0);
                        $colCount = $anyItemDiscount ? 9 : 8;
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Number</th>
                                    <th class="text-end">Qty</th>
                                    <th>Unit</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Amount</th>
                                    @if($anyItemDiscount)
                                        <th class="text-center">Disc %</th>
                                        <th class="text-end">Disc Amt</th>
                                    @endif
                                    <th class="text-end">Net</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bill->items as $index => $item)
                                    @php
                                        $gross   = $item->qty * $item->price;
                                        $discPct = (float)($item->discount ?? 0);
                                        $discAmt = $gross * ($discPct / 100);
                                        $net     = $gross - $discAmt;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $item->Product }}
                                            @if($item->nsn_code)
                                                <br><small class="text-muted">HSN: {{ $item->nsn_code }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->pnumber ?: '-' }}</td>
                                        <td class="text-end">{{ $item->qty }}</td>
                                        <td>{{ $item->unit ?: 'PCS' }}</td>
                                        <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">₹{{ number_format($gross, 2) }}</td>
                                        @if($anyItemDiscount)
                                            <td class="text-center">
                                                {{ $discPct > 0 ? number_format($discPct, 2) . '%' : '-' }}
                                            </td>
                                            <td class="text-end">
                                                {{ $discAmt > 0 ? '₹' . number_format($discAmt, 2) : '-' }}
                                            </td>
                                        @endif
                                        <td class="text-end">₹{{ number_format($net, 2) }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-warning"
                                                    onclick="editBillItem({{ $item->demo_id }})"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('bills.remove-item', $item->demo_id) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Remove this item?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Remove">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                @php
                                    $grossSubtotal   = $bill->items->sum(fn($i) => $i->qty * $i->price);
                                    $itemDiscountAmt = $bill->items->sum(fn($i) => ($i->qty * $i->price) * ((float)($i->discount ?? 0) / 100));
                                    $netSubtotal     = $grossSubtotal - $itemDiscountAmt;
                                @endphp
                                <tr>
                                    <td colspan="{{ $colCount - 2 }}" class="text-end"><strong>Gross Subtotal:</strong></td>
                                    <td class="text-end" colspan="2"><strong>₹{{ number_format($grossSubtotal, 2) }}</strong></td>
                                </tr>
                                @if($itemDiscountAmt > 0)
                                    <tr>
                                        <td colspan="{{ $colCount - 2 }}" class="text-end text-danger"><strong>Item Discounts:</strong></td>
                                        <td class="text-end text-danger" colspan="2"><strong>-₹{{ number_format($itemDiscountAmt, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="{{ $colCount - 2 }}" class="text-end"><strong>Net Subtotal:</strong></td>
                                        <td class="text-end" colspan="2"><strong>₹{{ number_format($netSubtotal, 2) }}</strong></td>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No items in this bill yet. Add one below.
                    </div>
                @endif

                {{-- ===== ADD / EDIT ITEM FORM ===== --}}
                <hr>
                <h6><i class="fas fa-plus"></i> <span id="editProductFormTitle">Add New Item</span></h6>

                {{-- Product Search (separate input) --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Search Product</label>
                        <div class="position-relative">
                            <input type="text" id="editProductSearch" class="form-control form-control-lg"
                                   placeholder="Type product name or number to search catalog..."
                                   autocomplete="off">
                            <div class="search-results-container" id="editProductSearchResults"></div>
                        </div>
                        <small class="text-muted">Search the catalog, or fill the form below to add a new product.</small>
                    </div>
                </div>

                <form action="{{ route('bills.add-item') }}" method="POST" id="addItemForm">
                    @csrf
                    <input type="hidden" name="bill_id" value="{{ $bill->bill_id }}">
                    <input type="hidden" name="customer_id" value="{{ $bill->customer_id }}">

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" id="edit_product_name" name="item" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Number</label>
                            <input type="text" id="edit_product_pnumber" name="pnumber" class="form-control">
                        </div>
                        <div class="col-md-1 mb-3">
                            <label class="form-label">Qty <span class="text-danger">*</span></label>
                            <input type="number" id="edit_product_qty" name="qty" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-md-1 mb-3">
                            <label class="form-label">Unit</label>
                            <input type="text" id="edit_product_unit" name="unit" class="form-control" value="PCS">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" id="edit_product_price" name="price" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-1 mb-3">
                            <label class="form-label">Disc %</label>
                            <input type="number" id="edit_product_discount" name="discount" class="form-control"
                                   step="0.01" min="0" max="100" value="0">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">HSN/NSN</label>
                            <input type="text" id="edit_product_nsn" name="nsn_code" class="form-control">
                        </div>
                    </div>

                    <input type="hidden" id="edit_demo_id" name="demo_id" value="">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success" id="editAddProductSubmitBtn">
                            <i class="fas fa-plus"></i> <span id="editAddProductBtnText">Add Product</span>
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="cancelEditProductForm()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== BILL CHARGES ===== --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calculator"></i> Bill Charges</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('bills.update-header', $bill->bill_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Overall Discount (%)</label>
                            <input type="number" name="discount" id="overall_discount" class="form-control"
                                   value="{{ $bill->discount ?? 0 }}" step="0.01" min="0" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Transport Cost (₹)</label>
                            <input type="number" name="transport" id="transport" class="form-control"
                                   value="{{ $bill->transport ?? 0 }}" step="0.01" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Packaging Cost (₹)</label>
                            <input type="number" name="package" id="package" class="form-control"
                                   value="{{ $bill->package ?? 0 }}" step="0.01" min="0">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Charges
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== BILL NOTES ===== --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-sticky-note"></i> Bill Notes</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('bills.update-notes', $bill->bill_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Despatched Through</label>
                            <input type="text" name="despatch" class="form-control"
                                   value="{{ $bill->note->despatch ?? '' }}" placeholder="e.g., VRL Transport">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Delivery Note</label>
                            <input type="text" name="deliverynote" class="form-control"
                                   value="{{ $bill->note->deliverynote ?? '' }}" placeholder="e.g., Delivery Note #">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">GR No.</label>
                            <input type="text" name="grno" class="form-control"
                                   value="{{ $bill->note->grno ?? '' }}" placeholder="e.g., GR-2024-001">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Notes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== DELETE BILL ===== --}}
        <div class="card mb-4 border-danger">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <strong class="text-danger"><i class="fas fa-exclamation-triangle"></i> Danger Zone</strong>
                    <p class="mb-0 text-muted small">Deleting a bill cannot be undone. Stock will be restored for inventory items.</p>
                </div>
                <form action="{{ route('bills.destroy', $bill->bill_id) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete bill #{{ $bill->bill_id }}? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Bill
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- ===== MODALS ===== --}}

<!-- Date Edit Modal -->
<div class="modal fade" id="dateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('bills.update-date', $bill->bill_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-calendar"></i> Edit Bill Date</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Bill Date <span class="text-danger">*</span></label>
                    <input type="date" name="bill_date" class="form-control"
                           value="{{ \Carbon\Carbon::parse($bill->bill_date)->format('Y-m-d') }}" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Customer Modal -->
@include('bills.partials.customer-modal')

@endsection

@push('scripts')
<script>
// Pass bill ID to JavaScript
const BILL_ID = '{{ $bill->bill_id }}';
const CUSTOMER_ID = '{{ $bill->customer_id }}';

// ========== PAGE-SPECIFIC VARIABLES ==========
let editSelectedProduct = null;
let editSearchTimeout = null;

// ========== UPDATE BILL CUSTOMER (override common one to use BILL_ID) ==========
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

            // Set the form action for update
            $('#addItemForm').attr('action', `/bills/update-item/${demoId}`);
            $('#addItemForm').find('input[name="_method"]').remove();
            $('#addItemForm').append('<input type="hidden" name="_method" value="PUT">');
            $('#editAddProductBtnText').text('Update Product');
            $('#editProductFormTitle').text('Edit Item');

            // Scroll to form
            $('html, body').animate({
                scrollTop: $("#addItemForm").offset().top - 100
            }, 300);

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
    $('#edit_product_qty').val(1);
    $('#edit_product_discount').val(0);
    $('#edit_demo_id').val('');
    editSelectedProduct = null;
    $('#editAddProductBtnText').text('Add Product');
    $('#editProductFormTitle').text('Add New Item');
    $('#editProductSearch').val('').removeClass('product-selected');
    $('#editProductSearchResults').hide().empty();

    $('#addItemForm').attr('action', "{{ route('bills.add-item') }}");
    $('#addItemForm').find('input[name="_method"]').remove();
    $('#addItemForm').find('input[name="edit_mode"]').remove();
}

// ========== ADD/UPDATE ITEM FORM SUBMIT ==========
$('#addItemForm').on('submit', function(e) {
    const name = $('#edit_product_name').val().trim();
    const qty = parseInt($('#edit_product_qty').val()) || 0;
    const price = parseFloat($('#edit_product_price').val()) || 0;
    const discount = parseFloat($('#edit_product_discount').val()) || 0;

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
    if (discount < 0 || discount > 100) {
        e.preventDefault();
        alert('Discount must be between 0 and 100');
        $('#edit_product_discount').focus();
        return false;
    }

    return true;
});

// ========== MODAL EVENT LISTENERS ==========
$(document).ready(function() {

    // ---- Modal Customer Search ----
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

    // ---- Product Search (dedicated input) ----
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

    // ---- Close search results on outside click ----
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $('#modalSearchResults, #editProductSearchResults').hide();
        }
    });

    // ---- Reset modal when closed ----
    $('#changeCustomerModal').on('hidden.bs.modal', function() {
        clearModalCustomer();
    });

    // ---- Focus search when modal opens ----
    $('#changeCustomerModal').on('shown.bs.modal', function() {
        $('#modalCustomerSearch').focus();
        $('#modalUpdateCustomerBtn').prop('disabled', true);
    });
});
</script>
@endpush