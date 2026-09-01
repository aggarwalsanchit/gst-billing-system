{{-- resources/views/bills/partials/customer-modal.blade.php --}}
<!-- Change Customer Modal -->
<div class="modal fade" id="changeCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user"></i> Change Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Customer Search -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Search Customer <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" id="modalCustomerSearch" class="form-control form-control-lg" 
                                   placeholder="Type customer name or phone..." autocomplete="off">
                            <div class="search-results-container" id="modalSearchResults"></div>
                        </div>
                        <small class="text-muted">Type to search existing customers. Press Enter to add new.</small>
                    </div>
                </div>

                <!-- Customer Display -->
                <div id="modalCustomerDisplay" style="display: none;">
                    <div class="customer-info-display">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1" id="modalDisplayName">Customer Name</h5>
                                <p class="mb-1" id="modalDisplayAddress">Address</p>
                                <p class="mb-0" id="modalDisplayPhone">Phone: N/A</p>
                                <p class="mb-0" id="modalDisplayGst">GST: N/A</p>
                                <p class="mb-0" id="modalDisplayPan">PAN: N/A</p>
                                <p class="mb-0" id="modalDisplayState">State: N/A</p>
                            </div>
                            <div>
                                <button type="button" class="btn btn-warning btn-sm" onclick="editModalCustomer()">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="clearModalCustomer()">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Customer Form -->
                <div id="modalNewCustomerForm" style="display: none;">
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-info-circle"></i> New customer detected. Fill the details below.
                        </div>
                        <button type="button" class="btn-close" onclick="cancelModalNewCustomer()"></button>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" id="modal_customer_name" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" id="modal_customer_phone" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <select id="modal_customer_state" class="form-control">
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
                            <textarea id="modal_customer_address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">GST Number</label>
                            <input type="text" id="modal_customer_gst" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">PAN Number</label>
                            <input type="text" id="modal_customer_pan" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" id="modal_customer_aadhar" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3 d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="saveModalNewCustomer()">
                                <i class="fas fa-save"></i> Save Customer
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelModalNewCustomer()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hidden Customer ID -->
                <input type="hidden" id="modal_customer_id" value="">

                <!-- Selected Customer Display -->
                <div id="modalSelectedCustomer" style="display: none;">
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle"></i> 
                        <strong>Selected Customer:</strong> <span id="modalSelectedName"></span>
                        <br>
                        <small>Customer ID: <span id="modalSelectedId"></span></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="modalUpdateCustomerBtn" onclick="updateBillCustomer()">
                    <i class="fas fa-save"></i> Update Customer
                </button>
            </div>
        </div>
    </div>
</div>