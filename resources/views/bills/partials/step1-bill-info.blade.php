{{-- resources/views/bills/partials/step1-bill-info.blade.php --}}
<div class="card mb-4" id="step1Card">
    <div class="card-header">
        <h5><i class="fas fa-file-invoice"></i> Bill Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Bill Number <span class="text-danger">*</span></label>
                <input type="text" name="bill_id" class="form-control" value="{{ $nextBillId }}" required readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Bill Date <span class="text-danger">*</span></label>
                <input type="date" name="bill_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">State</label>
                <input type="text" id="state_display" class="form-control" placeholder="Auto-filled from customer" readonly>
                <small class="text-muted">Auto-filled from customer</small>
            </div>
        </div>
    </div>
</div>