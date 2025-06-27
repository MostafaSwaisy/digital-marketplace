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

    <!-- Quick Actions - FIXED NAVIGATION -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-flex">
                        <button class="btn btn-success" onclick="showCreateProductModal()">
                            <i class="fas fa-plus"></i> Add New Product
                        </button>
                        <a href="/products?creator_filter=my_products" class="btn btn-primary">
                            <i class="fas fa-box"></i> Manage My Products
                        </a>
                        <a href="/orders?creator_view=true" class="btn btn-info">
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>My Recent Products</h5>
                    <a href="/products?creator_filter=my_products" class="btn btn-sm btn-outline-primary">View All</a>
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

    <!-- Create Product Modal - ADDED -->
    <div class="modal fade" id="createProductModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createProductForm" enctype="multipart/form-data">
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
                                    <label for="productTags" class="form-label">Tags (comma separated)</label>
                                    <input type="text" class="form-control" id="productTags" placeholder="web, template, modern">
                                </div>
                            </div>
                        </div>

                        <!-- FILE UPLOAD SECTION - NEW -->
                        <div class="mb-3">
                            <label class="form-label">Product Files</label>
                            <div class="border rounded p-3">
                                <div class="mb-3">
                                    <label for="productFiles" class="form-label">Main Product Files</label>
                                    <input type="file" class="form-control" id="productFiles" multiple accept=".zip,.rar,.pdf,.psd,.ai,.eps">
                                    <small class="form-text text-muted">Upload your main product files (ZIP, RAR, PDF, PSD, AI, EPS)</small>
                                </div>
                                <div class="mb-3">
                                    <label for="previewFiles" class="form-label">Preview Files (Optional)</label>
                                    <input type="file" class="form-control" id="previewFiles" multiple accept=".jpg,.jpeg,.png,.gif">
                                    <small class="form-text text-muted">Upload preview images (JPG, PNG, GIF)</small>
                                </div>
                                <div id="filePreview" class="mt-3"></div>
                            </div>
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
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create Product
                        </button>
                    </div>
                </form>
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

        // Show create product modal
        function showCreateProductModal() {
            const modal = new bootstrap.Modal(document.getElementById('createProductModal'));
            modal.show();
        }

        // File preview functionality
        document.getElementById('productFiles').addEventListener('change', function(e) {
            showFilePreview(e.target.files, 'main');
        });

        document.getElementById('previewFiles').addEventListener('change', function(e) {
            showFilePreview(e.target.files, 'preview');
        });

        function showFilePreview(files, type) {
            const preview = document.getElementById('filePreview');
            let html = `<h6>${type === 'main' ? 'Main Files' : 'Preview Files'}:</h6>`;
            
            for (let file of files) {
                const size = (file.size / 1024 / 1024).toFixed(2);
                html += `
                    <div class="d-flex justify-content-between align-items-center p-2 border rounded mb-1">
                        <span><i class="fas fa-file"></i> ${file.name}</span>
                        <small class="text-muted">${size} MB</small>
                    </div>
                `;
            }
            
            preview.innerHTML += html;
        }

        // Create product form submission with file upload
        document.getElementById('createProductForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            
            // Add text fields
            formData.append('name', document.getElementById('productName').value);
            formData.append('description', document.getElementById('productDescription').value);
            formData.append('price', document.getElementById('productPrice').value);
            formData.append('seller_id', currentUser.id);
            formData.append('category', document.getElementById('productCategory').value);
            formData.append('status', document.getElementById('productStatus').value);
            formData.append('is_featured', document.getElementById('productFeatured').checked);
            
            // Add tags
            const tags = document.getElementById('productTags').value
                .split(',')
                .map(tag => tag.trim())
                .filter(tag => tag.length > 0);
            formData.append('tags', JSON.stringify(tags));
            
            // Add files
            const productFiles = document.getElementById('productFiles').files;
            for (let file of productFiles) {
                formData.append('product_files[]', file);
            }
            
            const previewFiles = document.getElementById('previewFiles').files;
            for (let file of previewFiles) {
                formData.append('preview_files[]', file);
            }

            try {
                const response = await fetch('/api/products', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success !== false) {
                    showAlert('Product created successfully!', 'success');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createProductModal'));
                    modal.hide();
                    
                    // Reset form
                    document.getElementById('createProductForm').reset();
                    document.getElementById('filePreview').innerHTML = '';
                    
                    // Reload dashboard data
                    loadCreatorDashboard();
                } else {
                    const errorMsg = result.message || 'Failed to create product';
                    showAlert(errorMsg, 'danger');
                }
            } catch (error) {
                showAlert('Error creating product: ' + error.message, 'danger');
            }
        });

        // Rest of the existing creator dashboard code...
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
                            '<p class="text-muted">No products yet. <button class="btn btn-link p-0" onclick="showCreateProductModal()">Create your first product!</button></p>';
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