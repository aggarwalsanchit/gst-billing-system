{{-- resources/views/bills/partials/step2-customer.blade.php --}}
<div class="card mb-4" id="step2Card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-user"></i> Customer Information</h5>
        <span class="badge bg-secondary" id="customerStatus">Search Customer</span>
    </div>
    <div class="card-body">
        <!-- Customer Search -->
        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Search Customer <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <input type="text" id="customerSearch" class="form-control form-control-lg" 
                           placeholder="Type customer name or phone..." autocomplete="off">
                    <div class="search-results-container" id="searchResults"></div>
                </div>
                <small class="text-muted">Type to search existing customers. Press Enter to add new.</small>
            </div>
        </div>

        <!-- Customer Display -->
        <div id="customerDisplay" style="display: none;">
            <div class="customer-info-display">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-1" id="displayName">Customer Name</h5>
                        <p class="mb-1" id="displayAddress">Address</p>
                        <p class="mb-0" id="displayPhone">Phone: N/A</p>
                        <p class="mb-0" id="displayGst">GST: N/A</p>
                        <p class="mb-0" id="displayPan">PAN: N/A</p>
                        <p class="mb-0" id="displayState">State: N/A</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-warning btn-sm" onclick="editCustomer()">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="clearCustomer()">
                            <i class="fas fa-times"></i> Change
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Customer Form -->
        <div id="newCustomerForm" style="display: none;">
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-info-circle"></i> New customer detected. Fill the details below.
                </div>
                <button type="button" class="btn-close" onclick="cancelNewCustomer()"></button>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    {{-- REMOVED required attribute - will be validated in JavaScript --}}
                    <input type="text" name="name" id="customer_name" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" id="customer_phone" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">State <span class="text-danger">*</span></label>
                    {{-- REMOVED required attribute - will be validated in JavaScript --}}
                    <select name="customer_state" id="customer_state" class="form-control">
                        <option value="">Select State</option>
                        <option value="Punjab">Punjab</option>
                        <option value="Haryana">Haryana</option>
                        <option value="Himachal Pradesh">Himachal Pradesh</option>
                        <option value="Uttar Pradesh">Uttar Pradesh</option>
                        <option value="Delhi">Delhi</option>
                        <option value="Rajasthan">Rajasthan</option>
                        <option value="Tamil Nadu">Tamil Nadu</option>
                        <option value="Karnataka">Karnataka</option>
                        <option value="Andhra Pradesh">Andhra Pradesh</option>
                        <option value="Telangana">Telangana</option>
                        <option value="Kerala">Kerala</option>
                        <option value="Maharashtra">Maharashtra</option>
                        <option value="Gujarat">Gujarat</option>
                        <option value="West Bengal">West Bengal</option>
                        <option value="Bihar">Bihar</option>
                        <option value="Jammu & Kashmir">Jammu & Kashmir</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" id="customer_address" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">GST Number</label>
                    <input type="text" name="gst" id="customer_gst" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">PAN Number</label>
                    <input type="text" name="panno" id="customer_pan" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Aadhar Number</label>
                    <input type="text" name="adharno" id="customer_aadhar" class="form-control">
                </div>
                <div class="col-md-12 mb-3 d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="saveNewCustomer()">
                        <i class="fas fa-save"></i> Save Customer
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="cancelNewCustomer()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>