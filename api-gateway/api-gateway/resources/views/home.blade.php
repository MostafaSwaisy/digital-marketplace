@extends('layouts.app')

@section('title', 'Digital Marketplace - Buy & Sell Digital Products')

@section('content')
<!-- Hero Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="jumbotron bg-primary text-white p-5 rounded">
            <div class="container text-center">
                <h1 class="display-4">
                    <i class="fas fa-store"></i> Digital Marketplace
                </h1>
                <p class="lead">The best place to buy and sell digital products</p>
                <p class="mb-4">Discover amazing digital assets created by talented creators worldwide</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button class="btn btn-light btn-lg me-md-2" onclick="showRegisterModal()">
                        <i class="fas fa-user-plus"></i> Start Selling
                    </button>
                    <button class="btn btn-outline-light btn-lg" onclick="browseProducts()">
                        <i class="fas fa-search"></i> Browse Products
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="row mb-5">
    <div class="col-12">
        <h2 class="text-center mb-4">Why Choose Our Marketplace?</h2>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Secure Transactions</h5>
                <p class="card-text">All transactions are protected with enterprise-grade security</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-download fa-3x text-success mb-3"></i>
                <h5 class="card-title">Instant Downloads</h5>
                <p class="card-text">Get your digital products immediately after purchase</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-users fa-3x text-info mb-3"></i>
                <h5 class="card-title">Global Community</h5>
                <p class="card-text">Join thousands of creators and buyers worldwide</p>
            </div>
        </div>
    </div>
</div>

<!-- Popular Products Section -->
<div class="row mb-5">
    <div class="col-12">
        <h2 class="text-center mb-4">Popular Products</h2>
        <div id="popularProducts">
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Loading popular products...</p>
            </div>
        </div>
    </div>
</div>

<!-- Categories Section -->
<div class="row mb-5">
    <div class="col-12">
        <h2 class="text-center mb-4">Browse by Category</h2>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card category-card" onclick="browseCategory('Graphics')">
            <div class="card-body text-center">
                <i class="fas fa-palette fa-2x text-primary mb-2"></i>
                <h6>Graphics</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card category-card" onclick="browseCategory('Templates')">
            <div class="card-body text-center">
                <i class="fas fa-file-alt fa-2x text-success mb-2"></i>
                <h6>Templates</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card category-card" onclick="browseCategory('Fonts')">
            <div class="card-body text-center">
                <i class="fas fa-font fa-2x text-warning mb-2"></i>
                <h6>Fonts</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card category-card" onclick="browseCategory('Icons')">
            <div class="card-body text-center">
                <i class="fas fa-icons fa-2x text-info mb-2"></i>
                <h6>Icons</h6>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="bg-light p-4 rounded">
            <div class="row text-center">
                <div class="col-md-3">
                    <h3 class="text-primary" id="totalProducts">-</h3>
                    <p>Digital Products</p>
                </div>
                <div class="col-md-3">
                    <h3 class="text-success" id="totalCreators">-</h3>
                    <p>Creators</p>
                </div>
                <div class="col-md-3">
                    <h3 class="text-info" id="totalOrders">-</h3>
                    <p>Orders Completed</p>
                </div>
                <div class="col-md-3">
                    <h3 class="text-warning">24/7</h3>
                    <p>Support</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .jumbotron {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .category-card {
        cursor: pointer;
        transition: transform 0.2s;
    }
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('scripts')
<script>
    // Load homepage data
    document.addEventListener('DOMContentLoaded', function() {
        loadPopularProducts();
        loadStats();
    });

    // Load popular products
    async function loadPopularProducts() {
        try {
            const result = await apiCall('/api/products?per_page=4');
            
            if (result.success && result.data.products) {
                displayPopularProducts(result.data.products);
            } else {
                document.getElementById('popularProducts').innerHTML = 
                    '<p class="text-center text-muted">No products available</p>';
            }
        } catch (error) {
            document.getElementById('popularProducts').innerHTML = 
                '<p class="text-center text-danger">Error loading products</p>';
        }
    }

    // Display popular products
    function displayPopularProducts(products) {
        const container = document.getElementById('popularProducts');
        
        if (products.length === 0) {
            container.innerHTML = '<p class="text-center text-muted">No products available</p>';
            return;
        }

        container.innerHTML = `
            <div class="row">
                ${products.map(product => `
                    <div class="col-md-3 mb-3">
                        <div class="card product-card">
                            <div class="card-body">
                                <h6 class="card-title">${product.name}</h6>
                                <p class="card-text small text-muted">${product.description.substring(0, 60)}...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h6 text-primary">$${parseFloat(product.price).toFixed(2)}</span>
                                    <span class="badge bg-${getStatusBadgeColor(product.status)}">${product.status}</span>
                                </div>
                                ${product.is_featured ? '<span class="badge bg-warning">⭐ Featured</span>' : ''}
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }

    // Load statistics
    async function loadStats() {
        try {
            // Load products count
            const productsResult = await apiCall('/api/products');
            if (productsResult.success && productsResult.data.pagination) {
                document.getElementById('totalProducts').textContent = productsResult.data.pagination.total;
            }

            // Load users count (creators)
            const usersResult = await apiCall('/api/users');
            if (usersResult.success && Array.isArray(usersResult.data)) {
                const creators = usersResult.data.filter(user => user.role === 'creator');
                document.getElementById('totalCreators').textContent = creators.length;
            }

            // Load orders count
            const ordersResult = await apiCall('/api/orders');
            if (ordersResult.success && ordersResult.data.pagination) {
                document.getElementById('totalOrders').textContent = ordersResult.data.pagination.total;
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    // Browse products
    function browseProducts() {
        window.location.href = '/products';
    }

    // Browse by category
    function browseCategory(category) {
        window.location.href = `/products?category=${category}`;
    }

    // Helper function
    function getStatusBadgeColor(status) {
        const colors = {
            'draft': 'secondary',
            'published': 'success',
            'suspended': 'danger'
        };
        return colors[status] || 'secondary';
    }
</script>
@endsection