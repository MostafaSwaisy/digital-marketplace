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
       // Fixed Create Product Form Submission with Enhanced Error Handling
document.getElementById('createProductForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    console.log('=== FORM SUBMISSION STARTED ===');
    
    // Validate required fields first
    const name = document.getElementById('productName').value.trim();
    const description = document.getElementById('productDescription').value.trim();
    const price = document.getElementById('productPrice').value;
    const category = document.getElementById('productCategory').value;
    const status = document.getElementById('productStatus').value;
    
    // Client-side validation
    if (!name) {
        showAlert('Product name is required', 'danger');
        return;
    }
    
    if (!description) {
        showAlert('Product description is required', 'danger');
        return;
    }
    
    if (!price || parseFloat(price) < 0) {
        showAlert('Valid price is required', 'danger');
        return;
    }
    
    if (!currentUser || !currentUser.id) {
        showAlert('User authentication error. Please login again.', 'danger');
        return;
    }
    
    console.log('Client validation passed');
    
    try {
        const formData = new FormData();
        
        // Add text fields with proper validation
        formData.append('name', name);
        formData.append('description', description);
        formData.append('price', parseFloat(price));
        formData.append('seller_id', parseInt(currentUser.id));
        formData.append('category', category || '');
        formData.append('status', status || 'draft');
        formData.append('is_featured', document.getElementById('productFeatured').checked ? '1' : '0');
        
        // Handle tags properly
        const tagsInput = document.getElementById('productTags').value.trim();
        let tags = [];
        if (tagsInput) {
            tags = tagsInput
                .split(',')
                .map(tag => tag.trim())
                .filter(tag => tag.length > 0);
        }
        formData.append('tags', JSON.stringify(tags));
        
        console.log('Form data prepared:', {
            name: name,
            description: description.substring(0, 50) + '...',
            price: price,
            seller_id: currentUser.id,
            category: category,
            tags: tags,
            status: status
        });
        
        // Handle file uploads with validation
        const productFiles = document.getElementById('productFiles').files;
        const previewFiles = document.getElementById('previewFiles').files;
        
        console.log('Files to upload:', {
            productFiles: productFiles.length,
            previewFiles: previewFiles.length
        });
        
        // Validate file types and sizes
        const allowedMainTypes = ['zip', 'rar', 'pdf', 'psd', 'ai', 'eps', 'doc', 'docx'];
        const allowedPreviewTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        const maxMainFileSize = 100 * 1024 * 1024; // 100MB
        const maxPreviewFileSize = 10 * 1024 * 1024; // 10MB
        
        // Validate main files
        for (let i = 0; i < productFiles.length; i++) {
            const file = productFiles[i];
            const extension = file.name.split('.').pop().toLowerCase();
            
            if (!allowedMainTypes.includes(extension)) {
                showAlert(`Invalid file type: ${file.name}. Allowed types: ${allowedMainTypes.join(', ')}`, 'danger');
                return;
            }
            
            if (file.size > maxMainFileSize) {
                showAlert(`File too large: ${file.name}. Maximum size is 100MB.`, 'danger');
                return;
            }
            
            formData.append('product_files[]', file);
        }
        
        // Validate preview files
        for (let i = 0; i < previewFiles.length; i++) {
            const file = previewFiles[i];
            const extension = file.name.split('.').pop().toLowerCase();
            
            if (!allowedPreviewTypes.includes(extension)) {
                showAlert(`Invalid preview file type: ${file.name}. Allowed types: ${allowedPreviewTypes.join(', ')}`, 'danger');
                return;
            }
            
            if (file.size > maxPreviewFileSize) {
                showAlert(`Preview file too large: ${file.name}. Maximum size is 10MB.`, 'danger');
                return;
            }
            
            formData.append('preview_files[]', file);
        }
        
        console.log('File validation passed');
        
        // Show loading state
        const submitBtn = document.querySelector('#createProductForm button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        submitBtn.disabled = true;
        
        console.log('Making API request to /api/products');
        
        // Make the request
        const response = await fetch('/api/products', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
                // Don't set Content-Type for FormData - browser will set it with boundary
            },
            body: formData
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        let result;
        try {
            result = await response.json();
            console.log('Response data:', result);
        } catch (jsonError) {
            console.error('Failed to parse JSON response:', jsonError);
            const textResponse = await response.text();
            console.log('Raw response:', textResponse);
            throw new Error('Invalid JSON response from server');
        }
        
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (response.ok && result.success !== false) {
            console.log('=== SUCCESS ===');
            showAlert('Product created successfully!', 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('createProductModal'));
            if (modal) {
                modal.hide();
            }
            
            // Reset form
            document.getElementById('createProductForm').reset();
            document.getElementById('filePreview').innerHTML = '';
            
            // Reload dashboard data
            loadCreatorDashboard();
            
        } else {
            console.log('=== FAILURE ===');
            console.log('Error result:', result);
            
            let errorMsg = 'Failed to create product';
            
            if (result.errors) {
                // Laravel validation errors
                const errorMessages = [];
                for (const field in result.errors) {
                    if (result.errors[field] && Array.isArray(result.errors[field])) {
                        errorMessages.push(...result.errors[field]);
                    }
                }
                if (errorMessages.length > 0) {
                    errorMsg = errorMessages.join(', ');
                }
            } else if (result.message) {
                errorMsg = result.message;
            }
            
            showAlert(errorMsg, 'danger');
        }
        
    } catch (error) {
        console.error('=== EXCEPTION ===');
        console.error('Upload error:', error);
        
        // Reset button state
        const submitBtn = document.querySelector('#createProductForm button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-plus"></i> Create Product';
            submitBtn.disabled = false;
        }
        
        showAlert('Error creating product: ' + error.message, 'danger');
    }
});

// Enhanced file preview with better validation feedback
function showFilePreview(files, type) {
    const preview = document.getElementById('filePreview');
    
    if (files.length === 0) {
        return;
    }
    
    let html = `<div class="mt-3"><h6>${type === 'main' ? 'Main Files' : 'Preview Files'}:</h6>`;
    
    for (let file of files) {
        const size = (file.size / 1024 / 1024).toFixed(2);
        const extension = file.name.split('.').pop().toLowerCase();
        
        // Check file type
        const allowedTypes = type === 'main' 
            ? ['zip', 'rar', 'pdf', 'psd', 'ai', 'eps', 'doc', 'docx']
            : ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        const maxSize = type === 'main' ? 100 : 10; // MB
        const isValidType = allowedTypes.includes(extension);
        const isValidSize = parseFloat(size) <= maxSize;
        
        const statusClass = isValidType && isValidSize ? 'border-success' : 'border-danger';
        const statusIcon = isValidType && isValidSize ? 'fa-check text-success' : 'fa-times text-danger';
        
        html += `
            <div class="d-flex justify-content-between align-items-center p-2 border rounded mb-1 ${statusClass}">
                <div>
                    <span><i class="fas fa-file"></i> ${file.name}</span>
                    ${!isValidType ? '<br><small class="text-danger">Invalid file type</small>' : ''}
                    ${!isValidSize ? `<br><small class="text-danger">Too large (max ${maxSize}MB)</small>` : ''}
                </div>
                <div class="text-end">
                    <i class="fas ${statusIcon}"></i>
                    <br><small class="text-muted">${size} MB</small>
                </div>
            </div>
        `;
    }
    
    html += '</div>';
    preview.innerHTML += html;
}

// Enhanced API call function with better error handling
async function enhancedApiCall(url, method = 'GET', data = null, isFormData = false) {
    const options = {
        method: method,
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    };

    // Add authorization header if we have a token
    if (authToken) {
        options.headers['Authorization'] = `Bearer ${authToken}`;
    }

    // Handle different data types
    if (data) {
        if (isFormData) {
            // Don't set Content-Type for FormData
            options.body = data;
        } else {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(data);
        }
    }

    try {
        console.log(`Enhanced API Call: ${method} ${url}`);
        const response = await fetch(url, options);
        
        let result;
        try {
            result = await response.json();
        } catch (e) {
            const text = await response.text();
            console.error('Failed to parse JSON. Raw response:', text);
            throw new Error('Invalid JSON response');
        }

        console.log(`API Response (${response.status}):`, result);

        // Handle token expiration
        if (response.status === 401 && authToken) {
            console.log('Token expired, logging out...');
            logout();
            return {
                success: false,
                error: 'Session expired'
            };
        }

        return {
            success: response.ok,
            data: result,
            status: response.status,
            response: response
        };
    } catch (error) {
        console.error('Enhanced API call failed:', error);
        return {
            success: false,
            error: error.message
        };
    }
}
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