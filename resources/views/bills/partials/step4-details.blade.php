{{-- resources/views/bills/partials/step4-details.blade.php --}}
<div class="card mb-4 section-hidden" id="step4Card">
    <div class="card-header">
        <h5><i class="fas fa-file-invoice"></i> Bill Details</h5>
    </div>
    <div class="card-body">
        <!-- Product Summary in Step 4 -->
        <div class="mb-4">
            <h6><i class="fas fa-list"></i> Products in this Bill</h6>
            <div id="step4ProductList" class="product-list-container"></div>
            <div class="text-end mt-2">
                <small class="text-muted">Total Items: <span id="step4TotalItems">0</span> | 
                Total Amount: <span class="total-amount" id="step4TotalAmount">₹0.00</span></small>
            </div>
            <hr>
        </div>

        <!-- Charges -->
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Overall Discount (%)</label>
                <input type="number" name="discount" id="overall_discount" class="form-control" value="0" step="0.01" min="0" max="100">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Transport Cost</label>
                <input type="number" name="transport" id="transport" class="form-control" value="0" step="0.01" min="0">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Packaging Cost</label>
                <input type="number" name="package" id="package" class="form-control" value="0" step="0.01" min="0">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Bill Size</label>
                <input type="text" name="size" id="size" class="form-control" value="440" readonly>
            </div>
        </div>

        <hr>

        <!-- Notes -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Despatched Thru</label>
                <input type="text" name="despatch" id="despatch" class="form-control" placeholder="e.g., VRL Transport">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Delivery Note</label>
                <input type="text" name="deliverynote" id="deliverynote" class="form-control" placeholder="e.g., Delivery Note #">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">GR No.</label>
                <input type="text" name="grno" id="grno" class="form-control" placeholder="e.g., GR-2024-001">
            </div>
        </div>

        <!-- Summary Preview -->
        <div class="row mt-3">
            <div class="col-md-6 offset-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Bill Summary</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td>Subtotal:</td>
                                <td class="text-end" id="previewSubtotal">₹0.00</td>
                            </tr>
                            <tr>
                                <td>Discount:</td>
                                <td class="text-end" id="previewDiscount">₹0.00</td>
                            </tr>
                            <tr>
                                <td>Transport:</td>
                                <td class="text-end" id="previewTransport">₹0.00</td>
                            </tr>
                            <tr>
                                <td>Packaging:</td>
                                <td class="text-end" id="previewPackaging">₹0.00</td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Grand Total:</strong></td>
                                <td class="text-end"><strong id="previewGrandTotal">₹0.00</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="goToStep3()">
                <i class="fas fa-arrow-left"></i> Back to Products
            </button>
            <div>
                <button type="button" class="btn btn-secondary me-2" onclick="resetAll()">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="submit" class="btn btn-success btn-lg" id="createBtn">
                    <i class="fas fa-check"></i> Create Bill
                </button>
            </div>
        </div>
    </div>
</div>