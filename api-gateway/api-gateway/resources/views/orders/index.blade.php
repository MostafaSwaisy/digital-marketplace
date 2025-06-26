@extends('layouts.app')

@section('title', 'Orders Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-shopping-cart"></i> Orders Management</h1>
                <div>
                    <button class="btn btn-info me-2" onclick="testLoadOrders()">
                        <i class="fas fa-sync"></i> Debug Load
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createOrderModal">
                        <i class="fas fa-plus"></i> Create Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Container -->
    <div id="alerts"></div>

    <!-- Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Buyer</th>
                                    <th>Products</th>
                                    <th>Total</th>
                                    <th>Platform Fee</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTableBody">
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Loading orders...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Order Modal -->
    <div class="modal fade" id="createOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createOrderForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="orderBuyerId" class="form-label">Buyer</label>
                                    <select class="form-control" id="orderBuyerId" required>
                                        <option value="">Loading buyers...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="orderPaymentMethod" class="form-label">Payment Method</label>
                                    <select class="form-control" id="orderPaymentMethod" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="stripe">Stripe</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="credit_card">Credit Card</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Products to Order</label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <div id="productsList">
                                    <div class="text-center">
                                        <div class="spinner-border spinner-border-sm" role="status"></div>
                                        Loading products...
                                    </div>
                                </div>
                            </div>
                            <small class="form-text text-muted">Select one or more products for this order</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-info mb-0">
                                    <strong>Order Summary:</strong>
                                    <div id="orderSummary">
                                        <div>Products: <span id="selectedCount">0</span></div>
                                        <div>Subtotal: $<span id="orderSubtotal">0.00</span></div>
                                        <div>Platform Fee (10%): $<span id="orderPlatformFee">0.00</span></div>
                                        <div><strong>Total: $<span id="orderTotal">0.00</span></strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Replace the Edit Order Modal with this improved version -->
    <div class="modal fade" id="editOrderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editOrderForm">
                    <div class="modal-body">
                        <input type="hidden" id="editOrderId">

                        <div class="alert alert-info">
                            <small><strong>Note:</strong> Some status transitions may be restricted for business
                                reasons.</small>
                        </div>

                        <div class="mb-3">
                            <label for="editOrderStatus" class="form-label">Order Status</label>
                            <select class="form-select" id="editOrderStatus" required>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                            <small class="form-text text-muted">Current order status</small>
                        </div>

                        <div class="mb-3">
                            <label for="editPaymentStatus" class="form-label">Payment Status</label>
                            <select class="form-select" id="editPaymentStatus" required>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                            <small class="form-text text-muted">Current payment status</small>
                        </div>

                        <div class="mb-3">
                            <label for="editTransactionId" class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" id="editTransactionId"
                                placeholder="Enter transaction ID (optional)">
                            <small class="form-text text-muted">Payment gateway transaction ID</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- View Order Modal -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="orderDetails">
                        <!-- Order details will be populated here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let orders = [];
        let users = [];
        let products = [];
        let selectedProducts = [];

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, loading data...');
            loadUsers();
            loadProducts();
            loadOrders();
        });

        // Load users for buyer dropdown
        async function loadUsers() {
            try {
                const result = await apiCall('/api/users');
                if (result.success) {
                    users = Array.isArray(result.data) ? result.data : [];
                    console.log('Loaded users:', users);
                    populateBuyerDropdown();
                }
            } catch (error) {
                console.error('Error loading users:', error);
            }
        }

        // Load products for selection
        async function loadProducts() {
            try {
                const result = await apiCall('/api/products');
                if (result.success && result.data.products) {
                    products = result.data.products.filter(p => p.status === 'published');
                    console.log('Loaded products:', products);
                    populateProductsList();
                }
            } catch (error) {
                console.error('Error loading products:', error);
            }
        }

        // Load orders
        async function loadOrders() {
            console.log('=== Loading orders ===');
            try {
                const result = await apiCall('/api/orders');
                console.log('Orders API result:', result);

                if (result.success) {
                    if (result.data && result.data.orders) {
                        orders = result.data.orders;
                    } else if (Array.isArray(result.data)) {
                        orders = result.data;
                    } else {
                        orders = [];
                    }
                    console.log('Final orders:', orders);
                    displayOrders();
                } else {
                    console.error('Failed to load orders:', result);
                    document.getElementById('ordersTableBody').innerHTML = `
                    <tr><td colspan="9" class="text-center text-muted">Failed to load orders</td></tr>
                `;
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                document.getElementById('ordersTableBody').innerHTML = `
                <tr><td colspan="9" class="text-center text-muted">Error loading orders</td></tr>
            `;
            }
        }

        // Manual test function
        function testLoadOrders() {
            console.log('=== Manual Test Load Triggered ===');
            loadOrders();
        }

        // Populate buyer dropdown
        function populateBuyerDropdown() {
            const select = document.getElementById('orderBuyerId');

            if (users.length === 0) {
                select.innerHTML = '<option value="">No buyers available</option>';
                return;
            }

            const options = users.map(user =>
                `<option value="${user.id}">${user.name || user.username} (${user.email})</option>`
            ).join('');

            select.innerHTML = '<option value="">Select Buyer</option>' + options;
        }

        // Populate products list with checkboxes
        function populateProductsList() {
            const container = document.getElementById('productsList');

            if (products.length === 0) {
                container.innerHTML = '<div class="text-muted">No published products available</div>';
                return;
            }

            container.innerHTML = products.map(product => `
            <div class="form-check mb-2">
                <input class="form-check-input product-checkbox" type="checkbox" 
                       value="${product.id}" id="product_${product.id}" 
                       onchange="updateOrderSummary()">
                <label class="form-check-label" for="product_${product.id}">
                    <strong>${product.name}</strong> - $${parseFloat(product.price).toFixed(2)}
                    <br><small class="text-muted">${product.description}</small>
                </label>
            </div>
        `).join('');
        }

        // Update order summary when products are selected
        function updateOrderSummary() {
            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            selectedProducts = [];
            let subtotal = 0;

            checkboxes.forEach(checkbox => {
                const productId = parseInt(checkbox.value);
                const product = products.find(p => p.id === productId);
                if (product) {
                    selectedProducts.push(product);
                    subtotal += parseFloat(product.price);
                }
            });

            const platformFee = subtotal * 0.10; // 10% platform fee
            const total = subtotal + platformFee;

            document.getElementById('selectedCount').textContent = selectedProducts.length;
            document.getElementById('orderSubtotal').textContent = subtotal.toFixed(2);
            document.getElementById('orderPlatformFee').textContent = platformFee.toFixed(2);
            document.getElementById('orderTotal').textContent = total.toFixed(2);
        }

        // Add these functions to the existing JavaScript in orders/index.blade.php

        // Update the displayOrders function to include proper delete button
        function displayOrders() {
            console.log('=== Displaying orders ===');

            const tbody = document.getElementById('ordersTableBody');

            if (orders.length === 0) {
                tbody.innerHTML = `
            <tr><td colspan="9" class="text-center text-muted">No orders found</td></tr>
        `;
                return;
            }

            tbody.innerHTML = orders.map(order => {
                // Find buyer name
                const buyer = users.find(u => u.id == order.buyer_id);
                const buyerName = buyer ? (buyer.name || buyer.username) : `ID: ${order.buyer_id}`;

                // Count products
                const productCount = order.items ? order.items.length : 0;

                // Determine what delete/cancel actions are available
                let actionButtons = '';

                if (order.status === 'pending') {
                    actionButtons = `
                <button class="btn btn-sm btn-outline-danger" onclick="cancelOrder(${order.id})" title="Cancel Order">
                    <i class="fas fa-times"></i>
                </button>
            `;
                } else if (order.status === 'failed') {
                    actionButtons = `
                <button class="btn btn-sm btn-outline-danger" onclick="deleteOrder(${order.id})" title="Delete Order">
                    <i class="fas fa-trash"></i>
                </button>
            `;
                } else if (order.status === 'completed') {
                    actionButtons = `
                <button class="btn btn-sm btn-outline-info" onclick="refundOrder(${order.id})" title="Refund Order">
                    <i class="fas fa-undo"></i>
                </button>
            `;
                } else if (order.status === 'refunded') {
                    actionButtons = `
                <button class="btn btn-sm btn-outline-danger" onclick="deleteOrder(${order.id})" title="Delete Order">
                    <i class="fas fa-trash"></i>
                </button>
            `;
                }

                return `
            <tr>
                <td><strong>${order.order_number}</strong></td>
                <td>${buyerName}</td>
                <td>${productCount} item(s)</td>
                <td>$${parseFloat(order.total_amount).toFixed(2)}</td>
                <td>$${parseFloat(order.platform_fee).toFixed(2)}</td>
                <td>
                    <span class="badge bg-${getOrderStatusColor(order.status)}">${order.status}</span>
                </td>
                <td>
                    <span class="badge bg-${getPaymentStatusColor(order.payment_status)}">${order.payment_status}</span>
                </td>
                <td>${formatDate(order.created_at)}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewOrder(${order.id})" title="View Order">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="editOrder(${order.id})" title="Edit Order">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${actionButtons}
                    </div>
                </td>
            </tr>
        `;
            }).join('');
        }
        // Edit order function
        // Replace the editOrder function with this improved version
        function editOrder(orderId) {
            const order = orders.find(o => o.id === orderId);
            if (order) {
                console.log('Editing order:', order);

                // Populate edit form
                document.getElementById('editOrderId').value = order.id;
                document.getElementById('editOrderStatus').value = order.status;
                document.getElementById('editPaymentStatus').value = order.payment_status;
                document.getElementById('editTransactionId').value = order.payment_transaction_id || '';

                console.log('Set payment status to:', order.payment_status);
                console.log('Payment status element value:', document.getElementById('editPaymentStatus').value);

                // Show edit modal
                const modal = new bootstrap.Modal(document.getElementById('editOrderModal'));
                modal.show();
            }
        }

        // Replace the edit order form submission with this improved version
        document.getElementById('editOrderForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const orderId = document.getElementById('editOrderId').value;
            const orderStatus = document.getElementById('editOrderStatus').value;
            const paymentStatus = document.getElementById('editPaymentStatus').value;
            const transactionId = document.getElementById('editTransactionId').value;

            console.log('Submitting order update:', {
                orderId,
                orderStatus,
                paymentStatus,
                transactionId
            });

            const orderData = {
                status: orderStatus,
                payment_status: paymentStatus,
                payment_transaction_id: transactionId || null
            };

            try {
                const result = await apiCall(`/api/orders/${orderId}`, 'PUT', orderData);
                console.log('Update result:', result);

                if (result.success) {
                    showAlert('Order updated successfully!', 'success');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editOrderModal'));
                    modal.hide();

                    // Reload orders
                    loadOrders();
                } else {
                    console.error('Update failed:', result);
                    const errorMsg = result.data?.message || 'Failed to update order';
                    showAlert(errorMsg, 'danger');
                }
            } catch (error) {
                console.error('Update error:', error);
                showAlert('Error updating order: ' + error.message, 'danger');
            }
        });
        // Cancel order function
        async function cancelOrder(orderId) {
            const order = orders.find(o => o.id === orderId);
            if (order && confirm(`Are you sure you want to cancel order "${order.order_number}"?`)) {
                try {
                    const result = await apiCall(`/api/orders/${orderId}`, 'DELETE');

                    if (result.success) {
                        showAlert('Order cancelled successfully!', 'success');
                        loadOrders();
                    } else {
                        const errorMsg = result.data?.message || 'Failed to cancel order';
                        showAlert(errorMsg, 'danger');
                    }
                } catch (error) {
                    showAlert('Error cancelling order: ' + error.message, 'danger');
                }
            }
        }

        // Refund order function
        async function refundOrder(orderId) {
            const order = orders.find(o => o.id === orderId);
            if (order && confirm(`Are you sure you want to refund order "${order.order_number}"?`)) {
                try {
                    const result = await apiCall(`/api/orders/${orderId}/refund`, 'POST');

                    if (result.success) {
                        showAlert('Order refunded successfully!', 'success');
                        loadOrders();
                    } else {
                        const errorMsg = result.data?.message || 'Failed to refund order';
                        showAlert(errorMsg, 'danger');
                    }
                } catch (error) {
                    showAlert('Error refunding order: ' + error.message, 'danger');
                }
            }
        }
        // Delete order function (for failed/refunded orders)
        async function deleteOrder(orderId) {
            const order = orders.find(o => o.id === orderId);
            if (!order) return;

            let confirmMessage = '';
            if (order.status === 'failed') {
                confirmMessage =
                    `Are you sure you want to permanently delete the failed order "${order.order_number}"? This action cannot be undone.`;
            } else if (order.status === 'refunded') {
                confirmMessage =
                    `Are you sure you want to permanently delete the refunded order "${order.order_number}"? This will remove it from records.`;
            } else {
                confirmMessage = `Are you sure you want to delete order "${order.order_number}"?`;
            }

            if (confirm(confirmMessage)) {
                try {
                    const result = await apiCall(`/api/orders/${orderId}`, 'DELETE');

                    if (result.success) {
                        showAlert('Order deleted successfully!', 'success');
                        loadOrders();
                    } else {
                        const errorMsg = result.data?.message || 'Failed to delete order';
                        showAlert(errorMsg, 'danger');
                    }
                } catch (error) {
                    showAlert('Error deleting order: ' + error.message, 'danger');
                }
            }
        }

        // Update the existing cancelOrder function for better UX
        async function cancelOrder(orderId) {
            const order = orders.find(o => o.id === orderId);
            if (order && confirm(
                    `Are you sure you want to cancel order "${order.order_number}"? This will cancel the order and notify the buyer.`
                    )) {
                try {
                    const result = await apiCall(`/api/orders/${orderId}`, 'DELETE');

                    if (result.success) {
                        showAlert('Order cancelled successfully!', 'success');
                        loadOrders();
                    } else {
                        const errorMsg = result.data?.message || 'Failed to cancel order';
                        showAlert(errorMsg, 'danger');
                    }
                } catch (error) {
                    showAlert('Error cancelling order: ' + error.message, 'danger');
                }
            }
        }
        // Edit order form submission
        document.getElementById('editOrderForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const orderId = document.getElementById('editOrderId').value;
            const orderData = {
                status: document.getElementById('editOrderStatus').value,
                payment_status: document.getElementById('editPaymentStatus').value,
                payment_transaction_id: document.getElementById('editTransactionId').value
            };

            try {
                const result = await apiCall(`/api/orders/${orderId}`, 'PUT', orderData);

                if (result.success) {
                    showAlert('Order updated successfully!', 'success');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editOrderModal'));
                    modal.hide();

                    // Reload orders
                    loadOrders();
                } else {
                    const errorMsg = result.data?.message || 'Failed to update order';
                    showAlert(errorMsg, 'danger');
                }
            } catch (error) {
                showAlert('Error updating order: ' + error.message, 'danger');
            }
        });
        // Create order form submission
        document.getElementById('createOrderForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (selectedProducts.length === 0) {
                showAlert('Please select at least one product', 'warning');
                return;
            }

            const orderData = {
                buyer_id: parseInt(document.getElementById('orderBuyerId').value),
                items: selectedProducts.map(product => ({
                    product_id: product.id
                })),
                payment_method: document.getElementById('orderPaymentMethod').value
            };

            console.log('Creating order with data:', orderData);

            try {
                const result = await apiCall('/api/orders', 'POST', orderData);
                console.log('Order creation result:', result);

                if (result.success) {
                    showAlert('Order created successfully!', 'success');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createOrderModal'));
                    modal.hide();

                    // Reset form
                    document.getElementById('createOrderForm').reset();
                    selectedProducts = [];
                    updateOrderSummary();

                    // Reload orders
                    loadOrders();
                } else {
                    const errorMsg = result.data?.message || 'Failed to create order';
                    showAlert(errorMsg, 'danger');
                }
            } catch (error) {
                showAlert('Error creating order: ' + error.message, 'danger');
            }
        });

        // Helper functions
        function getOrderStatusColor(status) {
            const colors = {
                'pending': 'warning',
                'completed': 'success',
                'failed': 'danger',
                'refunded': 'info'
            };
            return colors[status] || 'secondary';
        }

        function getPaymentStatusColor(status) {
            const colors = {
                'pending': 'warning',
                'paid': 'success',
                'failed': 'danger',
                'refunded': 'info'
            };
            return colors[status] || 'secondary';
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        }

        function viewOrder(orderId) {
            const order = orders.find(o => o.id === orderId);
            if (order) {
                const buyer = users.find(u => u.id == order.buyer_id);
                const buyerName = buyer ? (buyer.name || buyer.username) : `ID: ${order.buyer_id}`;

                let orderDetailsHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <p><strong>Order Number:</strong> ${order.order_number}</p>
                        <p><strong>Buyer:</strong> ${buyerName}</p>
                        <p><strong>Status:</strong> <span class="badge bg-${getOrderStatusColor(order.status)}">${order.status}</span></p>
                        <p><strong>Payment:</strong> <span class="badge bg-${getPaymentStatusColor(order.payment_status)}">${order.payment_status}</span></p>
                        <p><strong>Payment Method:</strong> ${order.payment_method}</p>
                        <p><strong>Created:</strong> ${formatDate(order.created_at)}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Order Summary</h6>
                        <p><strong>Total Amount:</strong> $${parseFloat(order.total_amount).toFixed(2)}</p>
                        <p><strong>Platform Fee:</strong> $${parseFloat(order.platform_fee).toFixed(2)}</p>
                        <p><strong>Transaction ID:</strong> ${order.payment_transaction_id || 'N/A'}</p>
                    </div>
                </div>
            `;

                if (order.items && order.items.length > 0) {
                    orderDetailsHtml += `
                    <hr>
                    <h6>Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Seller Amount</th>
                                    <th>Downloaded</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${order.items.map(item => `
                                                        <tr>
                                                            <td>${item.product_name}</td>
                                                            <td>$${parseFloat(item.price).toFixed(2)}</td>
                                                            <td>$${parseFloat(item.seller_amount).toFixed(2)}</td>
                                                            <td>${item.is_downloaded ? '✅ Yes' : '❌ No'}</td>
                                                        </tr>
                                                    `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
                }

                document.getElementById('orderDetails').innerHTML = orderDetailsHtml;

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
                modal.show();
            }
        }

        function viewDownloads(orderId) {
            showAlert('Download management coming soon!', 'info');
        }
    </script>
@endsection
