@extends('layouts.app')

@section('title', 'Products Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-box"></i> Products Management</h1>
            <div>
                <button class="btn btn-info me-2" onclick="testLoadProducts()">
                    <i class="fas fa-sync"></i> Debug Load
                </button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createProductModal">
                    <i class="fas fa-plus"></i> Create Product
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alerts Container -->
<div id="alerts"></div>

<!-- Products Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Seller</th>
                                <th>Status</th>
                                <th>Downloads</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    Loading products...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Product Modal -->
<div class="modal fade" id="createProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createProductForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productName" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="productName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productCategory" class="form-label">Category</label>
                                <select class="form-control" id="productCategory">
                                    <option value="">Select Category</option>
                                    <option value="Graphics">Graphics</option>
                                    <option value="Templates">Templates</option>
                                    <option value="Photos">Photos</option>
                                    <option value="Fonts">Fonts</option>
                                    <option value="Icons">Icons</option>
                                    <option value="UI Kits">UI Kits</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="productDescription" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productPrice" class="form-label">Price ($)</label>
                                <input type="number" class="form-control" id="productPrice" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productSellerId" class="form-label">Seller</label>
                                <select class="form-control" id="productSellerId" required>
                                    <option value="">Loading sellers...</option>
                                </select>
                                <small class="form-text text-muted">Select the seller for this product</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="productTags" class="form-label">Tags (comma separated)</label>
                        <input type="text" class="form-control" id="productTags" placeholder="web, template, modern">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productStatus" class="form-label">Status</label>
                                <select class="form-control" id="productStatus" required>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="productFeatured" value="1">
                                    <label class="form-check-label" for="productFeatured">
                                        <strong>Featured Product</strong>
                                        <br><small class="text-muted">This product will be highlighted</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProductForm">
                <div class="modal-body">
                    <input type="hidden" id="editProductId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editProductName" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="editProductName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editProductCategory" class="form-label">Category</label>
                                <select class="form-control" id="editProductCategory">
                                    <option value="">Select Category</option>
                                    <option value="Graphics">Graphics</option>
                                    <option value="Templates">Templates</option>
                                    <option value="Photos">Photos</option>
                                    <option value="Fonts">Fonts</option>
                                    <option value="Icons">Icons</option>
                                    <option value="UI Kits">UI Kits</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editProductDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editProductDescription" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editProductPrice" class="form-label">Price ($)</label>
                                <input type="number" class="form-control" id="editProductPrice" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editProductStatus" class="form-label">Status</label>
                                <select class="form-control" id="editProductStatus" required>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editProductTags" class="form-label">Tags (comma separated)</label>
                        <input type="text" class="form-control" id="editProductTags">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="editProductFeatured" value="1">
                        <label class="form-check-label" for="editProductFeatured">
                            <strong>Featured Product</strong>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let products = [];
    let users = [];

    // Load products and users on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded, loading data...');
        loadUsers();
        loadProducts();
    });

    // Load users for seller dropdown
    async function loadUsers() {
        try {
            const result = await apiCall('/api/users');
            if (result.success) {
                users = Array.isArray(result.data) ? result.data : [];
                console.log('Loaded users:', users);
                populateSellerDropdowns();
            } else {
                console.error('Failed to load users:', result);
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    // Populate seller dropdowns
    function populateSellerDropdowns() {
        const createSelect = document.getElementById('productSellerId');
        
        if (users.length === 0) {
            createSelect.innerHTML = '<option value="">No sellers available</option>';
            return;
        }

        const options = users.map(user => 
            `<option value="${user.id}">${user.name || user.username} (${user.role})</option>`
        ).join('');
        
        createSelect.innerHTML = '<option value="">Select Seller</option>' + options;
    }

    // Load all products
    async function loadProducts() {
        console.log('=== Starting to load products ===');
        
        try {
            const result = await apiCall('/api/products');
            console.log('API call result:', result);
            
            if (result.success) {
                if (result.data && result.data.products) {
                    products = result.data.products;
                    console.log('Found products:', products);
                    displayProducts();
                } else {
                    console.error('No products found in response:', result.data);
                    document.getElementById('productsTableBody').innerHTML = `
                        <tr><td colspan="9" class="text-center text-muted">No products found</td></tr>
                    `;
                }
            } else {
                console.error('API call failed:', result);
                document.getElementById('productsTableBody').innerHTML = `
                    <tr><td colspan="9" class="text-center text-muted">Failed to load products</td></tr>
                `;
            }
        } catch (error) {
            console.error('Error in loadProducts:', error);
            document.getElementById('productsTableBody').innerHTML = `
                <tr><td colspan="9" class="text-center text-muted">Error loading products</td></tr>
            `;
        }
    }

    // Manual test function
    function testLoadProducts() {
        console.log('=== Manual Test Load Triggered ===');
        loadProducts();
    }

    // Display products in table
    function displayProducts() {
        console.log('=== Displaying products ===');
        
        const tbody = document.getElementById('productsTableBody');
        
        if (products.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="9" class="text-center text-muted">No products found</td></tr>
            `;
            return;
        }

        tbody.innerHTML = products.map(product => {
            // Find seller name
            const seller = users.find(u => u.id == product.seller_id);
            const sellerName = seller ? (seller.name || seller.username) : `ID: ${product.seller_id}`;
            
            return `
                <tr>
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.category || 'N/A'}</td>
                    <td>$${parseFloat(product.price).toFixed(2)}</td>
                    <td>${sellerName}</td>
                    <td>
                        <span class="badge bg-${getStatusBadgeColor(product.status)}">${product.status}</span>
                        ${product.is_featured ? '<span class="badge bg-warning ms-1">⭐ Featured</span>' : ''}
                    </td>
                    <td>${product.downloads_count || 0}</td>
                    <td>${formatDate(product.created_at)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewProduct(${product.id})" title="View Product">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="editProduct(${product.id})" title="Edit Product">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.id})" title="Delete Product">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Create product form submission
    document.getElementById('createProductForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const tags = document.getElementById('productTags').value
            .split(',')
            .map(tag => tag.trim())
            .filter(tag => tag.length > 0);

        // Fix: Properly handle the featured checkbox
        const isFeatured = document.getElementById('productFeatured').checked;
        console.log('Featured checkbox value:', isFeatured);

        const productData = {
            name: document.getElementById('productName').value,
            description: document.getElementById('productDescription').value,
            price: parseFloat(document.getElementById('productPrice').value),
            seller_id: parseInt(document.getElementById('productSellerId').value),
            category: document.getElementById('productCategory').value,
            tags: tags,
            status: document.getElementById('productStatus').value,
            is_featured: isFeatured // Send boolean value
        };

        console.log('Sending product data:', productData);

        try {
            const result = await apiCall('/api/products', 'POST', productData);
            
            if (result.success) {
                showAlert('Product created successfully!', 'success');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('createProductModal'));
                modal.hide();
                
                // Reset form
                document.getElementById('createProductForm').reset();
                
                // Reload products
                loadProducts();
            } else {
                const errorMsg = result.data?.message || 'Failed to create product';
                showAlert(errorMsg, 'danger');
            }
        } catch (error) {
            showAlert('Error creating product: ' + error.message, 'danger');
        }
    });

    // Edit product form submission
    document.getElementById('editProductForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const productId = document.getElementById('editProductId').value;
        const tags = document.getElementById('editProductTags').value
            .split(',')
            .map(tag => tag.trim())
            .filter(tag => tag.length > 0);

        // Fix: Properly handle the featured checkbox in edit
        const isFeatured = document.getElementById('editProductFeatured').checked;
        console.log('Edit featured checkbox value:', isFeatured);

        const productData = {
            name: document.getElementById('editProductName').value,
            description: document.getElementById('editProductDescription').value,
            price: parseFloat(document.getElementById('editProductPrice').value),
            category: document.getElementById('editProductCategory').value,
            tags: tags,
            status: document.getElementById('editProductStatus').value,
            is_featured: isFeatured // Send boolean value
        };

        console.log('Sending edit product data:', productData);

        try {
            const result = await apiCall(`/api/products/${productId}`, 'PUT', productData);
            
            if (result.success) {
                showAlert('Product updated successfully!', 'success');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                modal.hide();
                
                // Reload products
                loadProducts();
            } else {
                const errorMsg = result.data?.message || 'Failed to update product';
                showAlert(errorMsg, 'danger');
            }
        } catch (error) {
            showAlert('Error updating product: ' + error.message, 'danger');
        }
    });

    // Helper functions
    function getStatusBadgeColor(status) {
        const colors = {
            'draft': 'secondary',
            'published': 'success',
            'suspended': 'danger'
        };
        return colors[status] || 'secondary';
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString();
    }

    function viewProduct(productId) {
        const product = products.find(p => p.id === productId);
        if (product) {
            const seller = users.find(u => u.id == product.seller_id);
            const sellerName = seller ? (seller.name || seller.username) : `ID: ${product.seller_id}`;
            
            const productDetails = `
Product Details:
Name: ${product.name}
Category: ${product.category || 'N/A'}
Price: $${product.price}
Seller: ${sellerName}
Description: ${product.description}
Status: ${product.status}
Featured: ${product.is_featured ? 'Yes' : 'No'}
Tags: ${product.tags ? product.tags.join(', ') : 'None'}
Downloads: ${product.downloads_count || 0}
Created: ${formatDate(product.created_at)}
            `;
            alert(productDetails);
        }
    }

    function editProduct(productId) {
        const product = products.find(p => p.id === productId);
        if (product) {
            // Populate edit form
            document.getElementById('editProductId').value = product.id;
            document.getElementById('editProductName').value = product.name;
            document.getElementById('editProductDescription').value = product.description;
            document.getElementById('editProductPrice').value = product.price;
            document.getElementById('editProductCategory').value = product.category || '';
            document.getElementById('editProductTags').value = product.tags ? product.tags.join(', ') : '';
            document.getElementById('editProductStatus').value = product.status;
            
            // Fix: Properly set the featured checkbox
            document.getElementById('editProductFeatured').checked = Boolean(product.is_featured);
            console.log('Setting edit featured checkbox to:', product.is_featured);
            
            // Show edit modal
            const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
            modal.show();
        }
    }

    async function deleteProduct(productId) {
        const product = products.find(p => p.id === productId);
        if (product && confirm(`Are you sure you want to delete product "${product.name}"?`)) {
            try {
                const result = await apiCall(`/api/products/${productId}`, 'DELETE');
                
                if (result.success) {
                    showAlert('Product deleted successfully!', 'success');
                    loadProducts();
                } else {
                    const errorMsg = result.data?.message || 'Failed to delete product';
                    showAlert(errorMsg, 'danger');
                }
            } catch (error) {
                showAlert('Error deleting product: ' + error.message, 'danger');
            }
        }
    }
</script>
@endsection