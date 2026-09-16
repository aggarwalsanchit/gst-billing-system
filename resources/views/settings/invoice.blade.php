@extends('layouts.app')

@section('title', 'Invoice Settings')
@section('page-title', 'Invoice Settings')

@section('content')
<style>
    .settings-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .settings-section h6 {
        margin-bottom: 12px;
        color: #333;
        font-weight: 600;
    }
    .settings-section .form-label {
        font-size: 13px;
        font-weight: 500;
    }
    .terms-list .term-item {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 6px;
    }
    .terms-list .term-item input {
        flex: 1;
    }
    .terms-list .term-item .btn-remove-term {
        color: #dc3545;
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
    }
    .terms-list .term-item .btn-remove-term:hover {
        color: #bd2130;
    }
    .preview-box {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        font-size: 13px;
        font-family: 'Courier New', monospace;
    }
    .preview-box .preview-label {
        font-weight: 600;
        color: #666;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }
</style>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-cog"></i> Invoice Settings</h5>
        <div>
            <button type="button" class="btn btn-danger btn-sm" onclick="resetSettings()">
                <i class="fas fa-undo"></i> Reset to Default
            </button>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('settings.invoice.update') }}" method="POST" id="settingsForm">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Company Details -->
                <div class="col-md-6">
                    <div class="settings-section">
                        <h6><i class="fas fa-building"></i> Company Details</h6>
                        <div class="mb-2">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control" 
                                   value="{{ $settings->company_name }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Company Address</label>
                            <input type="text" name="company_address" class="form-control" 
                                   value="{{ $settings->company_address }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="company_gst" class="form-control" 
                                   value="{{ $settings->company_gst }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="company_phone" class="form-control" 
                                   value="{{ $settings->company_phone }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Phone Number 2</label>
                            <input type="text" name="company_phone2" class="form-control" 
                                   value="{{ $settings->company_phone2 }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Footer Text (e.g., For A.B Shawls)</label>
                            <input type="text" name="footer_text" class="form-control" 
                                   value="{{ $settings->footer_text }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Default GST Rate (%)</label>
                            <input type="number" name="default_gst_rate" class="form-control" 
                                   value="{{ $settings->default_gst_rate }}" step="0.1" min="0" max="100">
                        </div>
                    </div>
                </div>

                <!-- Bank Details -->
                <div class="col-md-6">
                    <div class="settings-section">
                        <h6><i class="fas fa-university"></i> Bank Details</h6>
                        <div class="mb-2">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" 
                                   value="{{ $settings->bank_name }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="bank_account" class="form-control" 
                                   value="{{ $settings->bank_account }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="bank_ifsc" class="form-control" 
                                   value="{{ $settings->bank_ifsc }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Bank Branch</label>
                            <input type="text" name="bank_branch" class="form-control" 
                                   value="{{ $settings->bank_branch }}" placeholder="Optional">
                        </div>
                    </div>

                    <div class="settings-section">
                        <h6><i class="fas fa-sticky-note"></i> Invoice Notes</h6>
                        <div class="mb-2">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="invoice_notes" class="form-control" rows="3" 
                                      placeholder="Additional notes on invoice...">{{ $settings->invoice_notes }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="col-md-12">
                    <div class="settings-section">
                        <h6><i class="fas fa-list"></i> Terms & Conditions</h6>
                        <p class="text-muted small">These will appear at the bottom of every invoice.</p>
                        
                        <div class="terms-list" id="termsList">
                            @php
                                $terms = $settings->getTermsArray();
                            @endphp
                            @foreach($terms as $index => $term)
                            <div class="term-item">
                                <span class="text-muted" style="font-size:12px;">{{ $index + 1 }}.</span>
                                <input type="text" name="terms[]" class="form-control" value="{{ $term }}">
                                <button type="button" class="btn-remove-term" onclick="removeTerm(this)" {{ count($terms) <= 1 ? 'disabled' : '' }}>
                                    ×
                                </button>
                            </div>
                            @endforeach
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addTerm()">
                            <i class="fas fa-plus"></i> Add Term
                        </button>
                    </div>
                </div>

                <!-- Preview -->
                <div class="col-md-12">
                    <div class="settings-section">
                        <h6><i class="fas fa-eye"></i> Preview</h6>
                        <div class="preview-box">
                            <div class="preview-label">Invoice Footer Preview</div>
                            <div id="previewContent">
                                @php
                                    $termsList = $settings->getTermsArray();
                                @endphp
                                @foreach($termsList as $term)
                                    <div>{{ $term }}</div>
                                @endforeach
                                <div style="margin-top: 5px; font-weight: bold;">{{ $settings->footer_text }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Add term
    function addTerm() {
        const container = document.getElementById('termsList');
        const items = container.querySelectorAll('.term-item');
        const index = items.length + 1;
        
        const div = document.createElement('div');
        div.className = 'term-item';
        div.innerHTML = `
            <span class="text-muted" style="font-size:12px;">${index}.</span>
            <input type="text" name="terms[]" class="form-control" placeholder="Enter term...">
            <button type="button" class="btn-remove-term" onclick="removeTerm(this)">
                ×
            </button>
        `;
        container.appendChild(div);
        updatePreview();
    }

    // Remove term
    function removeTerm(button) {
        const container = document.getElementById('termsList');
        const items = container.querySelectorAll('.term-item');
        
        if (items.length <= 1) {
            alert('You need at least one term!');
            return;
        }
        
        const item = button.closest('.term-item');
        item.remove();
        
        // Re-index
        container.querySelectorAll('.term-item').forEach((el, idx) => {
            el.querySelector('span').textContent = (idx + 1) + '.';
        });
        
        updatePreview();
    }

    // Update preview
    function updatePreview() {
        const container = document.getElementById('termsList');
        const inputs = container.querySelectorAll('input[name="terms[]"]');
        const preview = document.getElementById('previewContent');
        const footerText = document.querySelector('input[name="footer_text"]').value;
        
        let html = '';
        inputs.forEach(input => {
            if (input.value.trim()) {
                html += `<div>${input.value.trim()}</div>`;
            }
        });
        
        if (footerText) {
            html += `<div style="margin-top: 5px; font-weight: bold;">${footerText}</div>`;
        }
        
        preview.innerHTML = html || '<span class="text-muted">No terms added yet</span>';
    }

    // Reset settings
    function resetSettings() {
        if (!confirm('Reset all invoice settings to default? This cannot be undone.')) {
            return;
        }
        
        window.location.href = '{{ route("settings.invoice.reset") }}';
    }

    // Auto-update preview on input change
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[name="terms[]"], input[name="footer_text"]')) {
            updatePreview();
        }
    });

    // Initial preview
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush
@endsection