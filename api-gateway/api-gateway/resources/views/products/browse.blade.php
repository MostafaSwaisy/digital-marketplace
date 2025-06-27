{{-- Create this file: resources/views/products/browse.blade.php --}}
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
        <div class="modal-dialog modal-lg">
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
                    <button type="button" class="btn btn-success" id="purchaseBtn" onclick="purchaseProduct()">
                        <i class="fas fa-shopping-cart"></i> Purchase Now
                    </button>
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
</style>
@endsection

@section('scripts')
<script>
    let allProducts = [];
    let displayedProducts = [];
    let currentFilter = 'all';
    let currentSort = 'newest';
    let selectedProduct = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadProducts();
        
        // Check URL parameters for category filter
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');
        if (category) {
            filterByCategory(category);
        }
    });

    async function loadProducts() {
        try {
            const result = await apiCall('/api/products');
            
            if (result.success && result.data.products) {
                allProducts = result.data.products.filter(p => p.status === 'published');
                displayedProducts = [...allProducts];
                applyCurrentFilters();
                displayProducts();
            } else {
                document.getElementById('productsContainer').innerHTML = 
                    '<div class="col-12 text-center"><p class="text-muted">No products available</p></div>';
            }
        } catch (error) {
            document.getElementById('productsContainer').innerHTML = 
                '<div class="col-12 text-center"><p class="text-danger">Error loading products</p></div>';
        }
    }

    function displayProducts() {
        const container = document.getElementById('productsContainer');
        
        if (displayedProducts.length === 0) {
            container.innerHTML = '<div class="col-12 text-center"><p class="text-muted">No products found matching your criteria</p></div>';
            return;
        }

        container.innerHTML = `
            <div class="row">
                ${displayedProducts.map(product => `
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card product-card" onclick="showProductDetails(${product.id})">
                            <div class="product-image">
                                <i class="fas fa-${getProductIcon(product.category)}"></i>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">${product.name}</h6>
                                <p class="card-text small text-muted">${product.description.substring(0, 80)}...</p>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="price-tag text-primary">$${parseFloat(product.price).toFixed(2)}</span>
                                    ${product.is_featured ? '<span class="badge bg-warning">⭐ Featured</span>' : ''}
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">${product.category || 'Uncategorized'}</small>
                                    <small class="text-muted">${product.downloads_count || 0} downloads</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }

    function getProductIcon(category) {
        const icons = {
            'Graphics': 'palette',
            'Templates': 'file-alt',
            'Fonts': 'font',
            'Icons': 'icons',
            'Photos': 'camera',
            'UI Kits': 'mobile-alt'
        };
        return icons[category] || 'box';
    }

    function filterByCategory(category) {
        currentFilter = category;
        applyCurrentFilters();
        displayProducts();
    }

    function sortProducts(sortType) {
        currentSort = sortType;
        applyCurrentFilters();
        displayProducts();
    }

    function searchProducts() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        applyCurrentFilters(searchTerm);
        displayProducts();
    }

    function applyCurrentFilters(searchTerm = '') {
        let filtered = [...allProducts];

        // Apply category filter
        if (currentFilter !== 'all') {
            filtered = filtered.filter(p => p.category === currentFilter);
        }

        // Apply search filter
        if (searchTerm) {
            filtered = filtered.filter(p => 
                p.name.toLowerCase().includes(searchTerm) ||
                p.description.toLowerCase().includes(searchTerm) ||
                (p.tags && p.tags.some(tag => tag.toLowerCase().includes(searchTerm)))
            );
        }

        // Apply sorting
        switch (currentSort) {
            case 'newest':
                filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                break;
            case 'oldest':
                filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                break;
            case 'price_low':
                filtered.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
                break;
            case 'price_high':
                filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
                break;
            case 'popular':
                filtered.sort((a, b) => (b.downloads_count || 0) - (a.downloads_count || 0));
                break;
        }

        displayedProducts = filtered;
    }

    function showProductDetails(productId) {
        selectedProduct = allProducts.find(p => p.id === productId);
        if (!selectedProduct) return;

        document.getElementById('productModalTitle').textContent = selectedProduct.name;
        
        const modalContent = document.getElementById('productModalContent');
        modalContent.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="product-image mb-3">
                        <i class="fas fa-${getProductIcon(selectedProduct.category)}"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4>${selectedProduct.name}</h4>
                    <p class="text-muted">${selectedProduct.description}</p>
                    
                    <div class="mb-3">
                        <span class="h4 text-primary">$${parseFloat(selectedProduct.price).toFixed(2)}</span>
                        ${selectedProduct.is_featured ? '<span class="badge bg-warning ms-2">⭐ Featured</span>' : ''}
                    </div>
                    
                    <ul class="list-unstyled">
                        <li><strong>Category:</strong> ${selectedProduct.category || 'Uncategorized'}</li>
                        <li><strong>Downloads:</strong> ${selectedProduct.downloads_count || 0}</li>
                        <li><strong>Added:</strong> ${new Date(selectedProduct.created_at).toLocaleDateString()}</li>
                        ${selectedProduct.tags && selectedProduct.tags.length > 0 ? 
                            `<li><strong>Tags:</strong> ${selectedProduct.tags.join(', ')}</li>` : ''}
                    </ul>
                </div>
            </div>
        `;

        // Update purchase button based on login status
        const purchaseBtn = document.getElementById('purchaseBtn');
        if (!currentUser) {
            purchaseBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login to Purchase';
            purchaseBtn.onclick = () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
                modal.hide();
                showLoginModal();
            };
        } else if (currentUser.role === 'creator' && selectedProduct.seller_id == currentUser.id) {
            purchaseBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Product';
            purchaseBtn.onclick = () => window.location.href = '/products';
        } else {
            purchaseBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Purchase Now';
            purchaseBtn.onclick = purchaseProduct;
        }

        const modal = new bootstrap.Modal(document.getElementById('productModal'));
        modal.show();
    }

    function purchaseProduct() {
        if (!currentUser) {
            showAlert('Please login to purchase products', 'warning');
            return;
        }

        if (!selectedProduct) {
            showAlert('No product selected', 'error');
            return;
        }

        // TODO: Implement actual purchase flow
        // For now, redirect to orders page to create manual order
        if (confirm(`Purchase "${selectedProduct.name}" for $${parseFloat(selectedProduct.price).toFixed(2)}?`)) {
            window.location.href = '/orders';
        }
    }

    // Allow Enter key search
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchProducts();
        }
    });
</script>
@endsection