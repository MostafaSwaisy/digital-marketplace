@extends('layouts.app')

@section('title', 'Creator Dashboard - Digital Marketplace')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-chart-line"></i> Creator Dashboard</h1>
                <div class="badge bg-success">Creator Account</div>
            </div>
        </div>
    </div>

    <!-- Creator Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="myProducts">-</h4>
                            <p>My Products</p>
                        </div>
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="totalSales">-</h4>
                            <p>Total Sales</p>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="totalEarnings">$-</h4>
                            <p>Total Earnings</p>
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
                        <a href="/creator/products" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add New Product
                        </a>
                        <a href="/products" class="btn btn-primary">
                            <i class="fas fa-box"></i> Manage My Products
                        </a>
                        <a href="/orders" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> View Sales Reports
                        </a>
                        <button class="btn btn-outline-secondary" onclick="loadCreatorData()">
                            <i class="fas fa-sync"></i> Refresh Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Products & Recent Sales -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>My Recent Products</h5>
                </div>
                <div class="card-body">
                    <div id="myRecentProducts">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Loading products...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Sales</h5>
                </div>
                <div class="card-body">
                    <div id="recentSales">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Loading sales...
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
            // Check if user is creator
            if (!currentUser || currentUser.role !== 'creator') {
                showAlert('Access denied. Creator account required.', 'danger');
                window.location.href = '/';
                return;
            }

            // Continue with creator dashboard code
            loadCreatorDashboard();
        });

        async function loadCreatorDashboard() {
            try {
                await Promise.all([
                    loadMyProducts(),
                    loadMySales(),
                    loadRecentProducts(),
                    loadRecentSales()
                ]);
            } catch (error) {
                console.error('Error loading creator dashboard:', error);
            }
        }

        async function loadMyProducts() {
            try {
                const result = await apiCall('/api/products');
                if (result.success && result.data.products) {
                    // Filter products by current user
                    const myProducts = result.data.products.filter(p => p.seller_id == currentUser.id);
                    document.getElementById('myProducts').textContent = myProducts.length;

                    const downloads = myProducts.reduce((sum, p) => sum + (p.downloads_count || 0), 0);
                    document.getElementById('totalDownloads').textContent = downloads;
                }
            } catch (error) {
                console.error('Error loading products:', error);
            }
        }

        async function loadMySales() {
            try {
                const result = await apiCall('/api/orders');
                if (result.success && result.data.orders) {
                    // Filter orders that contain my products
                    let totalSales = 0;
                    let totalEarnings = 0;

                    result.data.orders.forEach(order => {
                        if (order.items) {
                            order.items.forEach(item => {
                                // Assuming the API includes seller info in order items
                                if (item.seller_id == currentUser.id) {
                                    totalSales++;
                                    totalEarnings += parseFloat(item.seller_amount || 0);
                                }
                            });
                        }
                    });

                    document.getElementById('totalSales').textContent = totalSales;
                    document.getElementById('totalEarnings').textContent = '$' + totalEarnings.toFixed(2);
                }
            } catch (error) {
                console.error('Error loading sales:', error);
            }
        }

        async function loadRecentProducts() {
            try {
                const result = await apiCall('/api/products');
                if (result.success && result.data.products) {
                    const myProducts = result.data.products
                        .filter(p => p.seller_id == currentUser.id)
                        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                        .slice(0, 5);

                    const container = document.getElementById('myRecentProducts');

                    if (myProducts.length === 0) {
                        container.innerHTML =
                            '<p class="text-muted">No products yet. <a href="/products">Create your first product!</a></p>';
                        return;
                    }

                    container.innerHTML = myProducts.map(product => `
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <strong>${product.name}</strong>
                                <br><small class="text-muted">$${parseFloat(product.price).toFixed(2)} • ${product.status}</small>
                            </div>
                            <small class="text-muted">${new Date(product.created_at).toLocaleDateString()}</small>
                        </div>
                    `).join('');
                }
            } catch (error) {
                document.getElementById('myRecentProducts').innerHTML =
                    '<p class="text-danger">Error loading products</p>';
            }
        }

        async function loadRecentSales() {
            try {
                const result = await apiCall('/api/orders');
                if (result.success && result.data.orders) {
                    const mySales = [];

                    result.data.orders.forEach(order => {
                        if (order.items) {
                            order.items.forEach(item => {
                                if (item.seller_id == currentUser.id) {
                                    mySales.push({
                                        product_name: item.product_name,
                                        amount: item.seller_amount,
                                        date: order.created_at,
                                        order_number: order.order_number
                                    });
                                }
                            });
                        }
                    });

                    const container = document.getElementById('recentSales');

                    if (mySales.length === 0) {
                        container.innerHTML = '<p class="text-muted">No sales yet. Keep promoting your products!</p>';
                        return;
                    }

                    const recentSales = mySales
                        .sort((a, b) => new Date(b.date) - new Date(a.date))
                        .slice(0, 5);

                    container.innerHTML = recentSales.map(sale => `
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <strong>${sale.product_name}</strong>
                                <br><small class="text-muted">Order #${sale.order_number}</small>
                            </div>
                            <div class="text-end">
                                <strong class="text-success">$${parseFloat(sale.amount).toFixed(2)}</strong>
                                <br><small class="text-muted">${new Date(sale.date).toLocaleDateString()}</small>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                document.getElementById('recentSales').innerHTML =
                    '<p class="text-danger">Error loading sales</p>';
            }
        }

        function loadCreatorData() {
            loadCreatorDashboard();
            showAlert('Dashboard data refreshed', 'success');
        }
    </script>
@endsection
