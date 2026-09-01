/**
 * Billing System - Common JavaScript Functions
 * Used across Create Bill and Edit Bill pages
 */

// ============================================================
// ========== GLOBAL VARIABLES ==========
// ============================================================
let selectedCustomer = null;
let selectedProduct = null;
let searchTimeout = null;
let productList = [];
let productIdCounter = 0;
let isNewCustomer = false;

// ============================================================
// ========== CUSTOMER FUNCTIONS ==========
// ============================================================

/**
 * Search customers via AJAX
 */
function searchCustomers(query, resultsContainer, callback) {
    if (query.length < 2) {
        $(resultsContainer).hide().empty();
        return;
    }
    
    $.ajax({
        url: '/customers/search',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            const results = $(resultsContainer);
            results.empty().show();

            if (response.length === 0) {
                results.append(`
                    <div class="no-results">
                        <i class="fas fa-user-plus"></i> No customer found. 
                        <strong>Press Enter</strong> to add "${query}" as new customer.
                    </div>
                `);
                return;
            }

            response.forEach(function(customer) {
                results.append(`
                    <div class="search-result-item" onclick="selectCustomer(${customer.customer_id}, '${resultsContainer}')">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="item-name">${customer.name}</div>
                                <div class="item-details">
                                    <i class="fas fa-phone"></i> ${customer.phone || 'N/A'} 
                                    <i class="fas fa-map-marker-alt ms-2"></i> ${customer.state || 'N/A'}
                                </div>
                            </div>
                            ${customer.gstnumber ? '<span class="badge bg-success">GST</span>' : '<span class="badge bg-warning">No GST</span>'}
                        </div>
                    </div>
                `);
            });
            
            // Callback if provided
            if (typeof callback === 'function') {
                callback(response);
            }
        },
        error: function(xhr) {
            console.error('Search error:', xhr);
            $(resultsContainer).html(`
                <div class="no-results">
                    <i class="fas fa-exclamation-triangle"></i> Error searching. Please try again.
                </div>
            `).show();
        }
    });
}

/**
 * Select a customer from search results
 */
function selectCustomer(customerId, resultsContainer) {
    $.ajax({
        url: `/customers/${customerId}`,
        method: 'GET',
        success: function(customer) {
            selectedCustomer = customer;
            isNewCustomer = false;
            
            // Update hidden fields
            $('#customer_id').val(customer.customer_id);
            $('#is_existing').val(1);
            $('#state').val(customer.state);
            
            // Update display
            $('#displayName').text(customer.name);
            $('#displayAddress').text(customer.address || 'Address: N/A');
            $('#displayPhone').text('Phone: ' + (customer.phone || 'N/A'));
            $('#displayGst').text('GST: ' + (customer.gstnumber || 'N/A'));
            $('#displayPan').text('PAN: ' + (customer.panno || 'N/A'));
            $('#displayState').text('State: ' + (customer.state || 'N/A'));
            
            // Show customer display
            $('#customerDisplay').show();
            $('#newCustomerForm').hide();
            $('#customerSearch').val(customer.name);
            $('#customerSearch').addClass('customer-selected');
            $('#customerStatus').removeClass('bg-secondary bg-warning').addClass('bg-success').text('Customer Selected');
            
            // Hide search results
            $(resultsContainer).hide();
            
            // Show products section
            $('#productForm').show();
            $('#productSearch').focus();
            
            // Update state display
            $('#state_display').val(customer.state);
        },
        error: function() {
            alert('Error loading customer details');
        }
    });
}

/**
 * Check and add new customer
 */
function checkAndAddNewCustomer(name, searchResultsContainer) {
    // Check if customer already exists in search results
    let found = false;
    $(searchResultsContainer + ' .search-result-item').each(function() {
        const customerName = $(this).find('.item-name').text();
        if (customerName.toLowerCase() === name.toLowerCase()) {
            found = true;
            return false;
        }
    });

    if (found) {
        $(searchResultsContainer + ' .search-result-item').first().click();
        return;
    }

    // New customer
    isNewCustomer = true;
    selectedCustomer = null;
    $('#customer_id').val('');
    $('#is_existing').val(0);
    $('#customer_name').val(name);
    $('#customerDisplay').hide();
    $('#newCustomerForm').show();
    $('#customerSearch').removeClass('customer-selected');
    $('#customerStatus').removeClass('bg-secondary bg-success').addClass('bg-warning').text('New Customer - Fill Details');
    
    setTimeout(function() {
        $('#customer_state').focus();
    }, 300);
}

/**
 * Save new customer
 */
function saveNewCustomer(callback) {
    const name = $('#customer_name').val().trim();
    const phone = $('#customer_phone').val().trim();
    const address = $('#customer_address').val().trim();
    const gst = $('#customer_gst').val().trim();
    const state = $('#customer_state').val();
    const pan = $('#customer_pan').val().trim();
    const aadhar = $('#customer_aadhar').val().trim();

    if (!name) {
        alert('Please enter customer name');
        $('#customer_name').focus();
        return;
    }

    if (!state) {
        alert('Please select customer state');
        $('#customer_state').focus();
        return;
    }

    const saveBtn = $('button[onclick*="saveNewCustomer"]');
    const originalText = saveBtn.html();
    saveBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: '/customers',
        method: 'POST',
        data: {
            name: name,
            phone: phone,
            address: address,
            gst: gst,
            state: state,
            panno: pan,
            adharno: aadhar,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            saveBtn.html(originalText).prop('disabled', false);
            
            if (response && response.success && response.customer) {
                const customer = response.customer;
                selectedCustomer = customer;
                isNewCustomer = false;
                $('#customer_id').val(customer.customer_id);
                $('#is_existing').val(1);
                $('#state').val(customer.state);
                
                // Update display
                $('#displayName').text(customer.name);
                $('#displayAddress').text(customer.address || 'Address: N/A');
                $('#displayPhone').text('Phone: ' + (customer.phone || 'N/A'));
                $('#displayGst').text('GST: ' + (customer.gstnumber || 'N/A'));
                $('#displayPan').text('PAN: ' + (customer.panno || 'N/A'));
                $('#displayState').text('State: ' + (customer.state || 'N/A'));
                
                $('#customerDisplay').show();
                $('#newCustomerForm').hide();
                $('#customerSearch').val(customer.name);
                $('#customerSearch').addClass('customer-selected');
                $('#customerStatus').removeClass('bg-warning').addClass('bg-success').text('Customer Saved');
                $('#state_display').val(customer.state);
                
                // Show products section
                $('#productForm').show();
                $('#productSearch').focus();
                
                alert('Customer "' + customer.name + '" saved successfully!');
                
                // Callback if provided
                if (typeof callback === 'function') {
                    callback(customer);
                }
            } else {
                alert('Error: Invalid response from server');
            }
        },
        error: function(xhr) {
            saveBtn.html(originalText).prop('disabled', false);
            alert('Error saving customer: ' + (xhr.responseJSON?.message || 'Unknown error'));
        }
    });
}

/**
 * Cancel new customer form
 */
function cancelNewCustomer() {
    $('#newCustomerForm').hide();
    $('#customer_name').val('');
    $('#customer_phone').val('');
    $('#customer_address').val('');
    $('#customer_gst').val('');
    $('#customer_pan').val('');
    $('#customer_aadhar').val('');
    $('#customer_state').val('');
    $('#customerSearch').val('');
    $('#customerSearch').focus();
    $('#customerStatus').removeClass('bg-warning').addClass('bg-secondary').text('Search Customer');
}

/**
 * Clear selected customer
 */
function clearCustomer() {
    selectedCustomer = null;
    $('#customer_id').val('');
    $('#is_existing').val(0);
    $('#customerSearch').val('');
    $('#searchResults').hide();
    $('#customerDisplay').hide();
    $('#newCustomerForm').hide();
    $('#customerSearch').removeClass('customer-selected');
    $('#customerStatus').removeClass('bg-success bg-warning').addClass('bg-secondary').text('Search Customer');
    $('#productForm').hide();
}


// ============================================================
// ========== PRODUCT FUNCTIONS ==========
// ============================================================

/**
 * Search products via AJAX
 */
function searchProducts(query, resultsContainer) {
    if (query.length < 2) {
        $(resultsContainer).hide().empty();
        return;
    }
    
    $.ajax({
        url: '/all-products/search',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            const results = $(resultsContainer);
            results.empty().show();

            if (response.length === 0) {
                results.append(`
                    <div class="no-results">
                        <i class="fas fa-plus-circle"></i> No product found. 
                        <strong>Press Enter</strong> to add "${query}" as new product.
                    </div>
                `);
                return;
            }

            response.forEach(function(product) {
                results.append(`
                    <div class="search-result-item" onclick="selectProduct(${product.product_id})">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="item-name">${product.pnumber || product.name}</div>
                                <div class="item-details">
                                    <i class="fas fa-tag"></i> ${product.name}
                                    <i class="fas fa-rupee-sign ms-2"></i> ₹${parseFloat(product.price).toFixed(2)}
                                    <i class="fas fa-box ms-2"></i> ${product.unit || 'PCS'}
                                    ${product.hsn_code ? `<span class="ms-2"><i class="fas fa-code"></i> ${product.hsn_code}</span>` : ''}
                                </div>
                            </div>
                            <span class="badge bg-info">Catalog</span>
                        </div>
                    </div>
                `);
            });
        },
        error: function(xhr) {
            console.error('Product search error:', xhr);
        }
    });
}

/**
 * Select a product from search results
 */
function selectProduct(productId) {
    $.ajax({
        url: `/all-products/${productId}`,
        method: 'GET',
        success: function(product) {
            selectedProduct = product;
            $('#product_name').val(product.name);
            $('#product_pnumber').val(product.pnumber || '');
            $('#product_unit').val(product.unit || 'PCS');
            $('#product_price').val(product.price);
            $('#product_nsn').val(product.hsn_code || '');
            $('#product_stock').val('Catalog Item');
            $('#product_qty').val(1);
            $('#product_discount').val(0);
            
            $('#productFormTitle').text('Edit Product (Existing)');
            $('#productForm').show();
            $('#productSearch').addClass('product-selected');
            $('#productSearchResults').hide();
            $('#productSearch').val(product.pnumber || product.name);
            $('#product_qty').focus().select();
        },
        error: function() {
            alert('Error loading product details');
        }
    });
}

/**
 * Check and add new product
 */
function checkAndAddNewProduct(query) {
    let found = false;
    $('#productSearchResults .search-result-item').each(function() {
        const itemName = $(this).find('.item-name').text();
        if (itemName.toLowerCase() === query.toLowerCase()) {
            found = true;
            return false;
        }
    });

    if (found) {
        $('#productSearchResults .search-result-item').first().click();
        return;
    }

    if ($('#productForm').is(':visible')) {
        const currentPnumber = $('#product_pnumber').val();
        if (currentPnumber === query) {
            $('#product_name').focus();
            return;
        }
    }

    selectedProduct = null;
    $('#product_pnumber').val(query);
    $('#product_name').val('');
    $('#product_unit').val('PCS');
    $('#product_price').val('');
    $('#product_nsn').val('');
    $('#product_stock').val('New Product');
    $('#product_qty').val(1);
    $('#product_discount').val(0);
    $('#productFormTitle').text('New Product - Fill Details');
    $('#productForm').show();
    $('#productSearch').removeClass('product-selected');
    
    setTimeout(function() {
        $('#product_name').focus();
    }, 300);
}

/**
 * Cancel product form
 */
function cancelProductForm() {
    $('#product_name').val('');
    $('#product_pnumber').val('');
    $('#product_unit').val('PCS');
    $('#product_price').val('');
    $('#product_nsn').val('');
    $('#product_stock').val('');
    $('#product_qty').val(1);
    $('#product_discount').val(0);
    $('#productFormTitle').text('New Product');
    $('#productSearch').val('');
    $('#productSearch').removeClass('product-selected');
    selectedProduct = null;
    $('#productForm').hide();
    $('#productSearch').focus().select();
}

/**
 * Add product to list (for create bill)
 */
function addProductToList() {
    const name = $('#product_name').val().trim();
    const pnumber = $('#product_pnumber').val().trim();
    const qty = parseInt($('#product_qty').val()) || 0;
    const unit = $('#product_unit').val().trim() || 'PCS';
    const price = parseFloat($('#product_price').val()) || 0;
    const discount = parseFloat($('#product_discount').val()) || 0;
    const nsn = $('#product_nsn').val().trim() || '';
    const product_id = selectedProduct?.product_id || null;

    if (!name) {
        alert('Please enter product name');
        $('#product_name').focus();
        return;
    }
    if (qty <= 0) {
        alert('Please enter valid quantity');
        $('#product_qty').focus();
        return;
    }
    if (price <= 0) {
        alert('Please enter valid price');
        $('#product_price').focus();
        return;
    }

    const total = qty * price;
    const discountAmount = total * (discount / 100);
    const finalTotal = total - discountAmount;

    // Check if product already in list
    const existingIndex = productList.findIndex(p => 
        p.name.toLowerCase() === name.toLowerCase() && 
        p.price === price
    );

    if (existingIndex !== -1) {
        if (!confirm('Product already in list. Update quantity?')) {
            return;
        }
        productList[existingIndex].qty += qty;
        productList[existingIndex].total = productList[existingIndex].qty * productList[existingIndex].price;
        productList[existingIndex].discount = discount;
        productList[existingIndex].finalTotal = productList[existingIndex].total - (productList[existingIndex].total * (discount / 100));
    } else {
        productList.push({
            id: ++productIdCounter,
            name: name,
            pnumber: pnumber,
            qty: qty,
            unit: unit,
            price: price,
            discount: discount,
            nsn: nsn,
            product_id: product_id,
            total: total,
            discountAmount: discountAmount,
            finalTotal: finalTotal
        });
    }

    renderProductList();
    cancelProductForm();
    updateTotals();
    $('#productCount').text(productList.length + ' Products');
}

/**
 * Render product list (for create bill)
 */
function renderProductList() {
    const container = $('#productList');
    container.empty();

    if (productList.length === 0) {
        $('#productListContainer').hide();
        return;
    }

    $('#productListContainer').show();

    productList.forEach((product, index) => {
        const discountText = product.discount > 0 ? 
            `<span class="badge bg-warning ms-1">${product.discount}% off</span>` : '';
        
        container.append(`
            <div class="product-item">
                <div class="product-info">
                    <div>
                        <strong>${product.name}</strong>
                        <span class="badge bg-secondary ms-2">#${product.pnumber || 'N/A'}</span>
                        ${product.product_id ? '<span class="badge bg-info ms-1">Catalog</span>' : '<span class="badge bg-warning ms-1">New</span>'}
                        ${discountText}
                        <div class="text-muted small">
                            ${product.qty} × ₹${product.price.toFixed(2)} = ₹${product.total.toFixed(2)}
                            ${product.discount > 0 ? `<span class="text-danger">- ₹${product.discountAmount.toFixed(2)}</span>` : ''}
                            <span class="ms-2">Unit: ${product.unit}</span>
                            ${product.nsn ? `<span class="ms-2">HSN: ${product.nsn}</span>` : ''}
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-warning me-1" onclick="editProductFromList(${index})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeProductFromList(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `);
    });

    updateHiddenInputs();
}

/**
 * Edit product from list
 */
function editProductFromList(index) {
    const product = productList[index];
    $('#product_name').val(product.name);
    $('#product_pnumber').val(product.pnumber || '');
    $('#product_unit').val(product.unit || 'PCS');
    $('#product_price').val(product.price);
    $('#product_nsn').val(product.nsn || '');
    $('#product_qty').val(product.qty);
    $('#product_discount').val(product.discount || 0);
    $('#product_stock').val('Editing...');
    $('#productFormTitle').text('Edit Product');
    $('#productForm').show();
    
    productList.splice(index, 1);
    renderProductList();
    updateTotals();
    $('#productCount').text(productList.length + ' Products');
    $('#product_qty').focus().select();
}

/**
 * Remove product from list
 */
function removeProductFromList(index) {
    if (confirm('Remove this product from the list?')) {
        productList.splice(index, 1);
        renderProductList();
        updateTotals();
        $('#productCount').text(productList.length + ' Products');
    }
}

/**
 * Update totals
 */
function updateTotals() {
    const total = productList.reduce((sum, p) => sum + p.total, 0);
    const finalTotal = productList.reduce((sum, p) => sum + (p.finalTotal || p.total), 0);
    const totalItems = productList.reduce((sum, p) => sum + p.qty, 0);
    $('#totalAmount').text('₹' + finalTotal.toFixed(2));
    $('#totalItems').text(totalItems);
}

/**
 * Update hidden inputs for form submission
 */
function updateHiddenInputs() {
    $('#productInputs').empty();
    const productsJson = JSON.stringify(productList);
    $('#productInputs').append(`
        <input type="hidden" name="products_json" value='${productsJson}'>
        <input type="hidden" name="product_count" value="${productList.length}">
    `);
}


// ============================================================
// ========== EDIT BILL PRODUCT FUNCTIONS ==========
// ============================================================

/**
 * Search edit products (for edit bill page)
 */
function searchEditProducts(query, resultsContainer) {
    if (query.length < 2) {
        $(resultsContainer).hide().empty();
        return;
    }
    
    $.ajax({
        url: '/all-products/search',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            const results = $(resultsContainer);
            results.empty().show();

            if (response.length === 0) {
                results.append(`
                    <div class="no-results">
                        <i class="fas fa-plus-circle"></i> No product found. 
                        <strong>Press Enter</strong> to add "${query}" as new product.
                    </div>
                `);
                return;
            }

            response.forEach(function(product) {
                results.append(`
                    <div class="search-result-item" onclick="selectEditProduct(${product.product_id})">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="item-name">${product.pnumber || product.name}</div>
                                <div class="item-details">
                                    <i class="fas fa-tag"></i> ${product.name}
                                    <i class="fas fa-rupee-sign ms-2"></i> ₹${parseFloat(product.price).toFixed(2)}
                                    <i class="fas fa-box ms-2"></i> ${product.unit || 'PCS'}
                                    ${product.hsn_code ? `<span class="ms-2"><i class="fas fa-code"></i> ${product.hsn_code}</span>` : ''}
                                </div>
                            </div>
                            <span class="badge bg-info">Catalog</span>
                        </div>
                    </div>
                `);
            });
        },
        error: function(xhr) {
            console.error('Product search error:', xhr);
            $(resultsContainer).html(`
                <div class="no-results">
                    <i class="fas fa-exclamation-triangle"></i> Error searching products.
                </div>
            `).show();
        }
    });
}

// ========== SELECT EDIT PRODUCT ==========
function selectEditProduct(productId) {
    $.ajax({
        url: `/all-products/${productId}`,
        method: 'GET',
        success: function(product) {
            editSelectedProduct = product;
            $('#edit_product_name').val(product.name);
            $('#edit_product_pnumber').val(product.pnumber || '');
            $('#edit_product_unit').val(product.unit || 'PCS');
            $('#edit_product_price').val(product.price);
            $('#edit_product_nsn').val(product.hsn_code || '');
            $('#edit_product_stock').val('Catalog Item');
            $('#edit_product_qty').val(1);
            $('#edit_product_discount').val(0);
            $('#edit_demo_id').val('');
            
            $('#editProductFormTitle').text('Edit Product (Existing)');
            $('#editProductForm').show();
            $('#editProductSearch').addClass('product-selected');
            $('#editProductSearchResults').hide();
            $('#editProductSearch').val(product.pnumber || product.name);
            $('#editAddProductBtnText').text('Add Product');
            $('#edit_product_qty').focus().select();
        },
        error: function() {
            alert('Error loading product details');
        }
    });
}

/**
 * Check and add new edit product
 */
function checkAndAddEditProduct(query) {
    let found = false;
    $('#editProductSearchResults .search-result-item').each(function() {
        const itemName = $(this).find('.item-name').text();
        if (itemName.toLowerCase() === query.toLowerCase()) {
            found = true;
            return false;
        }
    });

    if (found) {
        $('#editProductSearchResults .search-result-item').first().click();
        return;
    }

    if ($('#editProductForm').is(':visible')) {
        const currentPnumber = $('#edit_product_pnumber').val();
        if (currentPnumber === query) {
            $('#edit_product_name').focus();
            return;
        }
    }

    editSelectedProduct = null;
    $('#edit_product_pnumber').val(query);
    $('#edit_product_name').val('');
    $('#edit_product_unit').val('PCS');
    $('#edit_product_price').val('');
    $('#edit_product_nsn').val('');
    $('#edit_product_stock').val('New Product');
    $('#edit_product_qty').val(1);
    $('#edit_product_discount').val(0);
    $('#edit_demo_id').val('');
    $('#editProductFormTitle').text('New Product - Fill Details');
    $('#editProductForm').show();
    $('#editProductSearch').removeClass('product-selected');
    $('#editAddProductBtnText').text('Add Product');
    
    setTimeout(function() {
        $('#edit_product_name').focus();
    }, 300);
}

/**
 * Cancel edit product form
 */
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
    
    // Reset form action back to add item
    $('#addItemForm').attr('action', '/bills/' + $('#bill_id').val() + '/add-item');
    $('#addItemForm').find('input[name="_method"]').remove();
    $('#addItemForm').find('input[name="edit_mode"]').remove();
}

/**
 * Edit bill item (for edit bill page)
 */
function editBillItem(demoId, billId) {
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
            
            // Update form action
            $('#addItemForm').attr('action', `/bills/update-item/${demoId}`);
            $('#addItemForm').find('input[name="_method"]').remove();
            $('#addItemForm').append('<input type="hidden" name="_method" value="PUT">');
            $('#addItemForm').find('input[name="edit_mode"]').remove();
            $('#addItemForm').append('<input type="hidden" name="edit_mode" value="1">');
            
            $('#edit_product_qty').focus().select();
        },
        error: function() {
            alert('Error loading item details');
        }
    });
}


// ============================================================
// ========== MODAL CUSTOMER FUNCTIONS ==========
// ============================================================

let modalSelectedCustomer = null;
let modalIsNewCustomer = false;
let modalSearchTimeout = null;

/**
 * Search modal customers
 */
function searchModalCustomers(query) {
    if (query.length < 2) {
        $('#modalSearchResults').hide().empty();
        return;
    }
    
    $.ajax({
        url: '/customers/search',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            const results = $('#modalSearchResults');
            results.empty().show();

            if (response.length === 0) {
                results.append(`
                    <div class="no-results">
                        <i class="fas fa-user-plus"></i> No customer found. 
                        <strong>Press Enter</strong> to add "${query}" as new customer.
                    </div>
                `);
                return;
            }

            response.forEach(function(customer) {
                results.append(`
                    <div class="search-result-item" onclick="selectModalCustomer(${customer.customer_id})">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="item-name">${customer.name}</div>
                                <div class="item-details">
                                    <i class="fas fa-phone"></i> ${customer.phone || 'N/A'} 
                                    <i class="fas fa-map-marker-alt ms-2"></i> ${customer.state || 'N/A'}
                                </div>
                            </div>
                            ${customer.gstnumber ? '<span class="badge bg-success">GST</span>' : '<span class="badge bg-warning">No GST</span>'}
                        </div>
                    </div>
                `);
            });
        },
        error: function(xhr) {
            console.error('Search error:', xhr);
        }
    });
}

/**
 * Select modal customer
 */
function selectModalCustomer(customerId) {
    $.ajax({
        url: `/customers/${customerId}`,
        method: 'GET',
        success: function(customer) {
            modalSelectedCustomer = customer;
            modalIsNewCustomer = false;
            $('#modal_customer_id').val(customer.customer_id);
            
            $('#modalDisplayName').text(customer.name);
            $('#modalDisplayAddress').text(customer.address || 'Address: N/A');
            $('#modalDisplayPhone').text('Phone: ' + (customer.phone || 'N/A'));
            $('#modalDisplayGst').text('GST: ' + (customer.gstnumber || 'N/A'));
            $('#modalDisplayPan').text('PAN: ' + (customer.panno || 'N/A'));
            $('#modalDisplayState').text('State: ' + (customer.state || 'N/A'));
            
            $('#modalSelectedName').text(customer.name);
            $('#modalSelectedId').text(customer.customer_id);
            $('#modalSelectedCustomer').show();
            
            $('#modalSearchResults').hide();
            $('#modalCustomerDisplay').show();
            $('#modalNewCustomerForm').hide();
            $('#modalCustomerSearch').val(customer.name);
            $('#modalCustomerSearch').addClass('customer-selected');
            $('#modalUpdateCustomerBtn').prop('disabled', false);
        },
        error: function() {
            alert('Error loading customer details');
        }
    });
}

/**
 * Check and add new modal customer
 */
function checkAndAddModalNewCustomer(name) {
    let found = false;
    $('#modalSearchResults .search-result-item').each(function() {
        const customerName = $(this).find('.item-name').text();
        if (customerName.toLowerCase() === name.toLowerCase()) {
            found = true;
            return false;
        }
    });

    if (found) {
        $('#modalSearchResults .search-result-item').first().click();
        return;
    }

    modalIsNewCustomer = true;
    modalSelectedCustomer = null;
    $('#modal_customer_id').val('');
    $('#modal_customer_name').val(name);
    $('#modalCustomerDisplay').hide();
    $('#modalNewCustomerForm').show();
    $('#modalSelectedCustomer').hide();
    $('#modalCustomerSearch').removeClass('customer-selected');
    $('#modalUpdateCustomerBtn').prop('disabled', true);
    
    setTimeout(function() {
        $('#modal_customer_state').focus();
    }, 300);
}

/**
 * Save modal new customer
 */
function saveModalNewCustomer(callback) {
    const name = $('#modal_customer_name').val().trim();
    const phone = $('#modal_customer_phone').val().trim();
    const address = $('#modal_customer_address').val().trim();
    const gst = $('#modal_customer_gst').val().trim();
    const state = $('#modal_customer_state').val();
    const pan = $('#modal_customer_pan').val().trim();
    const aadhar = $('#modal_customer_aadhar').val().trim();

    if (!name) {
        alert('Please enter customer name');
        $('#modal_customer_name').focus();
        return;
    }

    if (!state) {
        alert('Please select customer state');
        $('#modal_customer_state').focus();
        return;
    }

    const saveBtn = $('button[onclick="saveModalNewCustomer()"]');
    const originalText = saveBtn.html();
    saveBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: '/customers',
        method: 'POST',
        data: {
            name: name,
            phone: phone,
            address: address,
            gst: gst,
            state: state,
            panno: pan,
            adharno: aadhar,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            saveBtn.html(originalText).prop('disabled', false);
            
            if (response && response.success && response.customer) {
                const customer = response.customer;
                modalSelectedCustomer = customer;
                modalIsNewCustomer = false;
                $('#modal_customer_id').val(customer.customer_id);
                
                $('#modalDisplayName').text(customer.name);
                $('#modalDisplayAddress').text(customer.address || 'Address: N/A');
                $('#modalDisplayPhone').text('Phone: ' + (customer.phone || 'N/A'));
                $('#modalDisplayGst').text('GST: ' + (customer.gstnumber || 'N/A'));
                $('#modalDisplayPan').text('PAN: ' + (customer.panno || 'N/A'));
                $('#modalDisplayState').text('State: ' + (customer.state || 'N/A'));
                
                $('#modalSelectedName').text(customer.name);
                $('#modalSelectedId').text(customer.customer_id);
                $('#modalSelectedCustomer').show();
                $('#modalNewCustomerForm').hide();
                $('#modalCustomerDisplay').show();
                $('#modalCustomerSearch').val(customer.name);
                $('#modalCustomerSearch').addClass('customer-selected');
                $('#modalUpdateCustomerBtn').prop('disabled', false);
                
                alert('Customer "' + customer.name + '" saved successfully!');
                
                if (typeof callback === 'function') {
                    callback(customer);
                }
            } else {
                alert('Error: Invalid response from server');
            }
        },
        error: function(xhr) {
            saveBtn.html(originalText).prop('disabled', false);
            alert('Error saving customer: ' + (xhr.responseJSON?.message || 'Unknown error'));
        }
    });
}

/**
 * Clear modal customer
 */
function clearModalCustomer() {
    modalSelectedCustomer = null;
    modalIsNewCustomer = false;
    $('#modal_customer_id').val('');
    $('#modalCustomerSearch').val('');
    $('#modalSearchResults').hide();
    $('#modalCustomerDisplay').hide();
    $('#modalNewCustomerForm').hide();
    $('#modalSelectedCustomer').hide();
    $('#modalCustomerSearch').removeClass('customer-selected');
    $('#modalUpdateCustomerBtn').prop('disabled', true);
    $('#modalCustomerSearch').focus();
}

/**
 * Edit modal customer
 */
function editModalCustomer() {
    if (!modalSelectedCustomer) {
        alert('No customer selected');
        return;
    }
    $('#modal_customer_name').val(modalSelectedCustomer.name);
    $('#modal_customer_phone').val(modalSelectedCustomer.phone);
    $('#modal_customer_address').val(modalSelectedCustomer.address);
    $('#modal_customer_gst').val(modalSelectedCustomer.gstnumber);
    $('#modal_customer_pan').val(modalSelectedCustomer.panno);
    $('#modal_customer_aadhar').val(modalSelectedCustomer.adharno);
    $('#modal_customer_state').val(modalSelectedCustomer.state);
    $('#modalNewCustomerForm').show();
    $('#modalCustomerDisplay').hide();
    $('#modalUpdateCustomerBtn').prop('disabled', true);
}

/**
 * Cancel modal new customer
 */
function cancelModalNewCustomer() {
    $('#modalNewCustomerForm').hide();
    $('#modal_customer_name').val('');
    $('#modal_customer_phone').val('');
    $('#modal_customer_address').val('');
    $('#modal_customer_gst').val('');
    $('#modal_customer_pan').val('');
    $('#modal_customer_aadhar').val('');
    $('#modal_customer_state').val('');
    $('#modalCustomerSearch').focus();
}

/**
 * Update bill customer from modal
 */
function updateBillCustomer(billId) {
    const customerId = $('#modal_customer_id').val();
    
    if (!customerId) {
        alert('Please select a customer first');
        return;
    }

    const updateBtn = $('#modalUpdateCustomerBtn');
    const originalText = updateBtn.html();
    updateBtn.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    
    $.ajax({
        url: `/bills/${billId}/update-customer`,
        method: 'POST',
        data: {
            customer_id: customerId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#customerNameDisplay').text(response.customer.name);
                $('#changeCustomerModal').modal('hide');
                alert('Customer updated successfully!');
                location.reload();
            } else {
                alert('Error updating customer: ' + response.message);
                updateBtn.html(originalText).prop('disabled', false);
            }
        },
        error: function(xhr) {
            alert('Error updating customer');
            console.error(xhr);
            updateBtn.html(originalText).prop('disabled', false);
        }
    });
}