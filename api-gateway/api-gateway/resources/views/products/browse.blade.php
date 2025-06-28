{{-- Updated Browse Products Page: api-gateway/resources/views/products/browse.blade.php --}}
@extends('layouts.app')

@section('title', 'Browse Products - Digital Marketplace')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-search"></i> Browse Products</h1>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="filterByCategory('all')">All Categories</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByCategory('Graphics')">Graphics</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByCategory('Templates')">Templates</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByCategory('Fonts')">Fonts</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByCategory('Icons')">Icons</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByCategory('UI Kits')">UI Kits</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-sort"></i> Sort
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="sortProducts('newest')">Newest First</a></li>
                            <li><a class="dropdown-item" href="#" onclick="sortProducts('oldest')">Oldest First</a></li>
                            <li><a class="dropdown-item" href="#" onclick="sortProducts('price_low')">Price: Low to High</a></li>
                            <li><a class="dropdown-item" href="#" onclick="sortProducts('price_high')">Price: High to Low</a></li>
                            <li><a class="dropdown-item" href="#" onclick="sortProducts('popular')">Most Popular</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search for products..." onkeyup="searchProducts()">
                        <button class="btn btn-primary" type="button" onclick="searchProducts()">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row">
        <div class="col-12">
            <div id="productsContainer">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading products...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="productModalContent">
                        <!-- Product details will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info" id="previewBtn" onclick="viewPreviews()" style="display:none;">
                        <i class="fas fa-eye"></i> View Previews
                    </button>
                    <button type="button" class="btn btn-success" id="purchaseBtn" onclick="purchaseProduct()">
                        <i class="fas fa-shopping-cart"></i> Purchase Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Confirmation Modal -->
    <div class="modal fade" id="purchaseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Purchase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="purchaseDetails">
                        <!-- Purchase details will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="confirmPurchase()">
                        <i class="fas fa-credit-card"></i> Confirm Purchase
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- File Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product Previews</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="previewContent">
                        <!-- Preview files will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        height: 100%;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    .product-image {
        height: 200px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
    }
    .price-tag {
        font-size: 1.25rem;
        font-weight: bold;
    }
    .creator-link {
        color: #6c757d;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .creator-link:hover {
        color: #495057;
        text-decoration: underline;
    }
    .file-count {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>
@endsection

@section('scripts')
<script>
    let allProducts = [];
    let displayedProducts = [];
    let currentFilter = 'all';
    let currentSort = 'newest';
    let selectedProduct = null;
    let users = []; // Store users for creator info

    document.addEventListener('DOMContentLoaded', function() {
        loadUsers(); // Load users first for creator info
        loadProducts();
        
        // Check URL parameters for category filter
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');
        if (category) {
            filterByCategory(category);
        }
    });

    async function loadUsers() {
        try {
            const result = await apiCall('/api/users');
            if (result.success) {
                users = Array.isArray(result.data) ? result.data : [];
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    async function loadProducts() {
        try {
            const result = await apiCall('/api/products');
            
            if (result.success && result.data.products) {
                allProducts =