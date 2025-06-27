{{-- Create this file: resources/views/buyer/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Buyer Dashboard - Digital Marketplace')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-user"></i> My Account</h1>
                <div class="badge bg-primary">Buyer Account</div>
            </div>
        </div>
    </div>

    <!-- Buyer Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="totalOrders">-</h4>
                            <p>Total Orders</p>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="totalPurchases">-</h4>
                            <p>Products Owned</p>
                        </div>
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="totalSpent">$-</h4>
                            <p>Total Spent</p>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="totalDownloads">-</h4>
                            <p>Downloads</p>
                        </div>
                        <i class="fas fa-download fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-flex">
                        <a href="/products" class="btn btn-primary">
                            <i class="fas fa-search"></i> Browse Products
                        </a>
                        <a href="/buyer/orders" class="btn btn-success">
                            <i class="fas fa-shopping-cart"></i> My Orders
                        </a>
                        <a href="/orders" class="btn btn-info">
                            <i class="fas fa-download"></i> My Downloads
                        </a>
                        <button class="btn btn-outline-secondary" onclick="loadBuyerData()">
                            <i class="fas fa-sync"></i> Refresh Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Owned Products -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Orders</h5>
                </div>
                <div class="card-body">
                    <div id="recentOrders">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Loading orders...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>My Products</h5>
                </div>
                <div class="card-body">
                    <div id="myProducts">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Loading products...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <span id="userName">-</span></p>
                            <p><strong>Email:</strong> <span id="userEmail">-</span></p>
                            <p><strong>Username:</strong> <span id="userUsername">-</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Member Since:</strong> <span id="memberSince">-</span></p>
                            <p><strong>Account Status:</strong> <span id="accountStatus">-</span></p>
                            <button class="btn btn-outline-primary btn-sm" onclick="editProfile()">
                                <i class="fas fa-edit"></i> Edit Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if user is buyer
            if (!currentUser || currentUser.role !== 'buyer') {
                showAlert('Access denied. Buyer account required.', 'danger');
                window.location.href = '/';
                return;
            }

            loadBuyerDashboard();
            loadAccountInfo();
        });

        async function loadBuyerDashboard() {
            try {
                await Promise.all([
                    loadMyOrders(),
                    loadMyOwnedProducts(),
                    loadRecentOrders()
                ]);
            } catch (error) {
                console.error('Error loading buyer dashboard:', error);
            }
        }

        async function loadMyOrders() {
            try {
                const result = await apiCall('/api/orders');
                if (result.success && result.data.orders) {
                    // Filter orders by current user
                    const myOrders = result.data.orders.filter(o => o.buyer_id == currentUser.id);
                    document.getElementById('totalOrders').textContent = myOrders.length;
                    
                    // Calculate total spent
                    const totalSpent = myOrders
                        .filter(order => order.status === 'completed')
                        .reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
                    document.getElementById('totalSpent').textContent = '$' + totalSpent.toFixed(2);

                    // Count total products purchased
                    let totalProducts = 0;
                    let totalDownloads = 0;
                    
                    myOrders.forEach(order => {
                        if (order.items) {
                            totalProducts += order.items.length;
                            totalDownloads += order.items.filter(item => item.is_downloaded).length;
                        }
                    });
                    
                    document.getElementById('totalPurchases').textContent = totalProducts;
                    document.getElementById('totalDownloads').textContent = totalDownloads;
                }
            } catch (error) {
                console.error('Error loading orders:', error);
            }
        }

        async function loadRecentOrders() {
            try {
                const result = await apiCall('/api/orders');
                if (result.success && result.data.orders) {
                    const myOrders = result.data.orders
                        .filter(o => o.buyer_id == currentUser.id)
                        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                        .slice(0, 5);

                    const container = document.getElementById('recentOrders');
                    
                    if (myOrders.length === 0) {
                        container.innerHTML = '<p class="text-muted">No orders yet. <a href="/products">Start shopping!</a></p>';
                        return;
                    }

                    container.innerHTML = myOrders.map(order => `
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <strong>Order #${order.order_number}</strong>
                                <br><small class="text-muted">${order.items ? order.items.length : 0} items • ${order.status}</small>
                            </div>
                            <div class="text-end">
                                <strong>${parseFloat(order.total_amount).toFixed(2)}</strong>
                                <br><small class="text-muted">${new Date(order.created_at).toLocaleDateString()}</small>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                document.getElementById('recentOrders').innerHTML = 
                    '<p class="text-danger">Error loading orders</p>';
            }
        }

        async function loadMyOwnedProducts() {
            try {
                const result = await apiCall('/api/orders');
                if (result.success && result.data.orders) {
                    const ownedProducts = [];
                    
                    // Extract all products from completed orders
                    result.data.orders
                        .filter(o => o.buyer_id == currentUser.id && o.status === 'completed')
                        .forEach(order => {
                            if (order.items) {
                                order.items.forEach(item => {
                                    ownedProducts.push({
                                        name: item.product_name,
                                        price: item.price,
                                        downloaded: item.is_downloaded,
                                        purchase_date: order.created_at,
                                        order_id: order.id
                                    });
                                });
                            }
                        });

                    const container = document.getElementById('myProducts');
                    
                    if (ownedProducts.length === 0) {
                        container.innerHTML = '<p class="text-muted">No products owned yet. <a href="/products">Browse our collection!</a></p>';
                        return;
                    }

                    // Show most recent 5 products
                    const recentProducts = ownedProducts
                        .sort((a, b) => new Date(b.purchase_date) - new Date(a.purchase_date))
                        .slice(0, 5);

                    container.innerHTML = recentProducts.map(product => `
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <strong>${product.name}</strong>
                                <br><small class="text-muted">${parseFloat(product.price).toFixed(2)}</small>
                            </div>
                            <div class="text-end">
                                ${product.downloaded ? 
                                    '<span class="badge bg-success">Downloaded</span>' : 
                                    '<button class="btn btn-sm btn-primary" onclick="downloadProduct(' + product.order_id + ')">Download</button>'
                                }
                                <br><small class="text-muted">${new Date(product.purchase_date).toLocaleDateString()}</small>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                document.getElementById('myProducts').innerHTML = 
                    '<p class="text-danger">Error loading products</p>';
            }
        }

        function loadAccountInfo() {
            if (currentUser) {
                document.getElementById('userName').textContent = currentUser.name || 'Not set';
                document.getElementById('userEmail').textContent = currentUser.email;
                document.getElementById('userUsername').textContent = currentUser.username;
                document.getElementById('memberSince').textContent = new Date(currentUser.created_at).toLocaleDateString();
                
                const statusBadge = currentUser.is_verified ? 
                    '<span class="badge bg-success">Verified</span>' : 
                    '<span class="badge bg-warning">Unverified</span>';
                document.getElementById('accountStatus').innerHTML = statusBadge;
            }
        }

        function loadBuyerData() {
            loadBuyerDashboard();
            loadAccountInfo();
            showAlert('Dashboard data refreshed', 'success');
        }

        function downloadProduct(orderId) {
            // TODO: Implement actual download functionality
            showAlert('Download functionality coming soon!', 'info');
        }

        function editProfile() {
            // TODO: Implement profile editing
            showAlert('Profile editing coming soon!', 'info');
        }
    </script>
@endsection