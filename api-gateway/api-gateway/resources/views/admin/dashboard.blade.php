@extends('layouts.app')

@section('title', 'Admin Dashboard - Digital Marketplace')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
                <div class="badge bg-danger">Admin Only</div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="totalUsers">-</h4>
                            <p>Total Users</p>
                        </div>
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="totalProducts">-</h4>
                            <p>Total Products</p>
                        </div>
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
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
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="totalRevenue">$-</h4>
                            <p>Total Revenue</p>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x"></i>
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
                        <a href="/users" class="btn btn-primary">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                        <a href="/products" class="btn btn-success">
                            <i class="fas fa-box"></i> Manage Products
                        </a>
                        <a href="/orders" class="btn btn-warning">
                            <i class="fas fa-shopping-cart"></i> Manage Orders
                        </a>
                        <button class="btn btn-info" onclick="loadSystemOverview()">
                            <i class="fas fa-sync"></i> Refresh Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Service Status</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            User Service
                            <span class="badge bg-secondary" id="user-service-status">Checking...</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Product Service
                            <span class="badge bg-secondary" id="product-service-status">Checking...</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Order Service
                            <span class="badge bg-secondary" id="order-service-status">Checking...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div id="recentActivity">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Loading activity...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data Preview -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent Users</h6>
                    <a href="/users" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div id="recentUsers">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent Products</h6>
                    <a href="/products" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body">
                    <div id="recentProducts">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent Orders</h6>
                    <a href="/orders" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
                <div class="card-body">
                    <div id="recentOrders">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Check if user is admin
        document.addEventListener('DOMContentLoaded', function() {
            if (!currentUser || currentUser.role !== 'admin') {
                showAlert('Access denied. Admin privileges required.', 'danger');
                window.location.href = '/';
                return;
            }

            // Load admin dashboard data
            loadAdminDashboard();
            testServices();
        });

        async function loadAdminDashboard() {
            try {
                // Load users count
                const usersResult = await apiCall('/api/users');
                if (usersResult.success && Array.isArray(usersResult.data)) {
                    document.getElementById('totalUsers').textContent = usersResult.data.length;
                    displayRecentUsers(usersResult.data.slice(0, 5));
                }

                // Load products count
                const productsResult = await apiCall('/api/products');
                if (productsResult.success && productsResult.data.pagination) {
                    document.getElementById('totalProducts').textContent = productsResult.data.pagination.total;
                    displayRecentProducts(productsResult.data.products.slice(0, 5));
                }

                // Load orders count and revenue
                const ordersResult = await apiCall('/api/orders');
                if (ordersResult.success && ordersResult.data.orders) {
                    const orders = ordersResult.data.orders;
                    document.getElementById('totalOrders').textContent = orders.length;
                    displayRecentOrders(orders.slice(0, 5));

                    const totalRevenue = orders
                        .filter(order => order.status === 'completed')
                        .reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
                    document.getElementById('totalRevenue').textContent = '$' + totalRevenue.toFixed(2);
                }

                loadRecentActivity();
            } catch (error) {
                console.error('Error loading admin dashboard:', error);
            }
        }

        async function testServices() {
            const services = {
                'user-service': '/api/users',
                'product-service': '/api/products',
                'order-service': '/api/orders'
            };

            for (const [serviceName, endpoint] of Object.entries(services)) {
                try {
                    const result = await apiCall(endpoint);
                    const statusElement = document.getElementById(serviceName + '-status');

                    if (result.success) {
                        statusElement.textContent = 'Online';
                        statusElement.className = 'badge bg-success';
                    } else {
                        statusElement.textContent = 'Error';
                        statusElement.className = 'badge bg-danger';
                    }
                } catch (error) {
                    const statusElement = document.getElementById(serviceName + '-status');
                    statusElement.textContent = 'Offline';
                    statusElement.className = 'badge bg-danger';
                }
            }
        }

        async function loadRecentActivity() {
            try {
                const ordersResult = await apiCall('/api/orders?per_page=5');
                if (ordersResult.success && ordersResult.data.orders) {
                    const activities = ordersResult.data.orders.map(order => ({
                        type: 'order',
                        message: `Order ${order.order_number} ${order.status}`,
                        time: new Date(order.created_at).toLocaleDateString()
                    }));

                    const activityHtml = activities.map(activity => `
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span>${activity.message}</span>
                        <small class="text-muted">${activity.time}</small>
                    </div>
                `).join('');

                    document.getElementById('recentActivity').innerHTML = activityHtml ||
                        '<p class="text-muted">No recent activity</p>';
                }
            } catch (error) {
                document.getElementById('recentActivity').innerHTML =
                    '<p class="text-danger">Error loading activity</p>';
            }
        }

        function displayRecentUsers(users) {
            const container = document.getElementById('recentUsers');
            if (users.length === 0) {
                container.innerHTML = '<p class="text-muted">No users found</p>';
                return;
            }

            container.innerHTML = users.map(user => `
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>${user.name || user.username}</strong>
                        <br><small class="text-muted">${user.role}</small>
                    </div>
                    <span class="badge bg-${getRoleBadgeColor(user.role)}">${user.role}</span>
                </div>
            `).join('');
        }

        function displayRecentProducts(products) {
            const container = document.getElementById('recentProducts');
            if (products.length === 0) {
                container.innerHTML = '<p class="text-muted">No products found</p>';
                return;
            }

            container.innerHTML = products.map(product => `
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>${product.name}</strong>
                        <br><small class="text-muted">$${parseFloat(product.price).toFixed(2)}</small>
                    </div>
                    <span class="badge bg-${getStatusBadgeColor(product.status)}">${product.status}</span>
                </div>
            `).join('');
        }

        function displayRecentOrders(orders) {
            const container = document.getElementById('recentOrders');
            if (orders.length === 0) {
                container.innerHTML = '<p class="text-muted">No orders found</p>';
                return;
            }

            container.innerHTML = orders.map(order => `
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>${order.order_number}</strong>
                        <br><small class="text-muted">$${parseFloat(order.total_amount).toFixed(2)}</small>
                    </div>
                    <span class="badge bg-${getOrderStatusColor(order.status)}">${order.status}</span>
                </div>
            `).join('');
        }

        function loadSystemOverview() {
            loadAdminDashboard();
            testServices();
            showAlert('Dashboard data refreshed', 'success');
        }

        // Helper functions
        function getRoleBadgeColor(role) {
            const colors = {
                'admin': 'danger',
                'creator': 'success',
                'buyer': 'primary'
            };
            return colors[role] || 'secondary';
        }

        function getStatusBadgeColor(status) {
            const colors = {
                'draft': 'secondary',
                'published': 'success',
                'suspended': 'danger'
            };
            return colors[status] || 'secondary';
        }

        function getOrderStatusColor(status) {
            const colors = {
                'pending': 'warning',
                'completed': 'success',
                'failed': 'danger',
                'refunded': 'info'
            };
            return colors[status] || 'secondary';
        }
    </script>
@endsection