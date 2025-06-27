<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Digital Marketplace')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .service-card {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
            position: relative;
            transition: all 0.3s ease;
        }

        .service-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .card {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 2rem;
        }

        .btn {
            border-radius: 0.375rem;
        }

        .alert {
            border-radius: 0.375rem;
        }

        footer {
            background-color: #f8f9fa;
            padding: 2rem 0;
            margin-top: 3rem;
        }
    </style>

    @yield('styles')
</head>

<body>
    <!-- Update the navigation section -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-store"></i> Digital Marketplace
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('browse*') ? 'active' : '' }}"
                            href="{{ url('/browse') }}">
                            <i class="fas fa-search"></i> Browse Products
                        </a>
                    </li>

                    <!-- Admin Only Links -->
                    <li class="nav-item admin-only d-none">
                        <a class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}"
                            href="{{ url('/admin/dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                        </a>
                    </li>
                    <li class="nav-item admin-only d-none">
                        <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}"
                            href="{{ url('/users') }}">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                    </li>
                    <li class="nav-item admin-only d-none">
                        <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}"
                            href="{{ url('/products') }}">
                            <i class="fas fa-box"></i> Manage Products
                        </a>
                    </li>
                    <li class="nav-item admin-only d-none">
                        <a class="nav-link {{ request()->is('orders*') ? 'active' : '' }}"
                            href="{{ url('/orders') }}">
                            <i class="fas fa-shopping-cart"></i> Manage Orders
                        </a>
                    </li>

                    <!-- Creator Only Links -->
                    <li class="nav-item creator-only d-none">
                        <a class="nav-link {{ request()->is('creator/dashboard*') ? 'active' : '' }}"
                            href="{{ url('/creator/dashboard') }}">
                            <i class="fas fa-chart-line"></i> My Dashboard
                        </a>
                    </li>
                    <li class="nav-item creator-only d-none">
                        <a class="nav-link {{ request()->is('creator/products*') ? 'active' : '' }}"
                            href="{{ url('/creator/products') }}">
                            <i class="fas fa-palette"></i> My Products
                        </a>
                    </li>

                    <!-- Buyer Only Links -->
                    <li class="nav-item buyer-only d-none">
                        <a class="nav-link {{ request()->is('buyer/dashboard*') ? 'active' : '' }}"
                            href="{{ url('/buyer/dashboard') }}">
                            <i class="fas fa-user"></i> My Account
                        </a>
                    </li>
                    <li class="nav-item buyer-only d-none">
                        <a class="nav-link {{ request()->is('buyer/orders*') ? 'active' : '' }}"
                            href="{{ url('/buyer/orders') }}">
                            <i class="fas fa-shopping-cart"></i> My Orders
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <!-- Authentication Links -->
                    <li class="nav-item" id="loginNavItem">
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal"
                                data-bs-target="#loginModal">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                            <button class="btn btn-light btn-sm" onclick="showRegisterModal()">
                                <i class="fas fa-user-plus"></i> Register
                            </button>
                        </div>
                    </li>
                    <li class="nav-item dropdown d-none" id="userNavDropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <span id="currentUserName">User</span>
                            <span class="badge bg-light text-dark ms-1" id="currentUserRole">role</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="showProfile()">
                                    <i class="fas fa-user"></i> My Profile
                                </a></li>
                            <li><a class="dropdown-item" href="#" onclick="goToDashboard()">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#" onclick="logout()">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login to Digital Marketplace</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="loginForm">
                    <div class="modal-body">
                        <div id="loginAlerts"></div>
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="loginEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="loginPassword" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-link" onclick="showRegisterModal()">Don't have an
                            account? Register</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register New Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="registerForm">
                    <div class="modal-body">
                        <div id="registerAlerts"></div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="registerName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="registerName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="registerUsername" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="registerUsername" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="registerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="registerEmail" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="registerPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="registerPassword" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="registerPasswordConfirm" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="registerPasswordConfirm"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="registerRole" class="form-label">Role</label>
                            <select class="form-control" id="registerRole" required>
                                <option value="">Select Role</option>
                                <option value="buyer">Buyer</option>
                                <option value="creator">Creator</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="registerBio" class="form-label">Bio (Optional)</label>
                            <textarea class="form-control" id="registerBio" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-link" onclick="showLoginModal()">Already have an
                            account? Login</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Main Content -->
    <main class="container mt-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-light text-center">
        <div class="container">
            <p class="mb-0">&copy; 2025 Digital Marketplace - Microservices Architecture Demo</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global authentication state
        let currentUser = null;
        let authToken = null;

        // Global configuration
        const services = {
            gateway: 'http://localhost:8000',
            user: 'http://localhost:8001',
            product: 'http://localhost:8002',
            order: 'http://localhost:8003'
        };

        // CSRF token for Laravel
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Check if user is logged in on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkAuthStatus();
            setTimeout(checkPageAccess, 100);
        });

        // Check authentication status
        function checkAuthStatus() {
            const token = localStorage.getItem('auth_token');
            const user = localStorage.getItem('auth_user');

            if (token && user) {
                authToken = token;
                currentUser = JSON.parse(user);
                updateAuthUI(true);
            } else {
                updateAuthUI(false);
            }
        }

        // Update authentication UI with role-based visibility
        function updateAuthUI(isLoggedIn) {
            const loginNavItem = document.getElementById('loginNavItem');
            const userNavDropdown = document.getElementById('userNavDropdown');
            const currentUserName = document.getElementById('currentUserName');
            const currentUserRole = document.getElementById('currentUserRole');

            // Hide all role-specific items first
            document.querySelectorAll('.admin-only, .creator-only, .buyer-only, .authenticated-only').forEach(item => {
                item.classList.add('d-none');
            });

            if (isLoggedIn && currentUser) {
                loginNavItem.classList.add('d-none');
                userNavDropdown.classList.remove('d-none');
                currentUserName.textContent = currentUser.name || currentUser.username;
                currentUserRole.textContent = currentUser.role;

                // Show role-specific navigation items
                const roleClass = currentUser.role + '-only';
                document.querySelectorAll('.' + roleClass).forEach(item => {
                    item.classList.remove('d-none');
                });

                // Show general authenticated items
                document.querySelectorAll('.authenticated-only').forEach(item => {
                    item.classList.remove('d-none');
                });

                console.log('UI updated for user:', currentUser.username, 'Role:', currentUser.role);
            } else {
                loginNavItem.classList.remove('d-none');
                userNavDropdown.classList.add('d-none');
                console.log('UI updated for logged out state');
            }
        }

        // Show register modal
        function showRegisterModal() {
            console.log('Showing register modal');
            // Hide login modal if open
            const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
            if (loginModal) {
                loginModal.hide();
            }

            // Show register modal
            const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
            registerModal.show();
        }

        // Show login modal
        function showLoginModal() {
            console.log('Showing login modal');
            // Hide register modal if open
            const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
            if (registerModal) {
                registerModal.hide();
            }

            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }

        // Go to appropriate dashboard based on role
        function goToDashboard() {
            if (!currentUser) return;

            const dashboards = {
                'admin': '/admin/dashboard',
                'creator': '/creator/dashboard',
                'buyer': '/buyer/dashboard'
            };

            const dashboardUrl = dashboards[currentUser.role] || '/';
            window.location.href = dashboardUrl;
        }

        // Redirect to dashboard based on role
        function redirectToDashboard(role) {
            const dashboards = {
                'admin': '/admin/dashboard',
                'creator': '/creator/dashboard',
                'buyer': '/buyer/dashboard'
            };

            const dashboardUrl = dashboards[role] || '/';
            console.log(`Redirecting ${role} to ${dashboardUrl}`);
            window.location.href = dashboardUrl;
        }

        // Logout function
        async function logout() {
            if (!confirm('Are you sure you want to logout?')) {
                return;
            }

            try {
                // Call logout API (optional)
                if (authToken) {
                    await apiCall('/api/auth/logout', 'POST');
                }
            } catch (error) {
                console.log('Logout API call failed, but continuing with local cleanup');
            }

            // Clear local storage
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');

            // Clear global variables
            authToken = null;
            currentUser = null;

            // Update UI
            updateAuthUI(false);

            // Show goodbye message
            showAlert('You have been logged out successfully.', 'info');

            // Redirect to home page
            setTimeout(() => {
                window.location.href = '/';
            }, 1500);
        }

        // Show user profile
        function showProfile() {
            if (!currentUser) {
                showAlert('Please login to view your profile.', 'warning');
                return;
            }

            const profileInfo = `
Profile Information:
Name: ${currentUser.name || 'Not set'}
Username: ${currentUser.username}
Email: ${currentUser.email}
Role: ${currentUser.role}
Bio: ${currentUser.bio || 'No bio provided'}
Joined: ${new Date(currentUser.created_at).toLocaleDateString()}
            `;

            alert(profileInfo);
        }

        // Check if user has required role for current page
        function checkPageAccess() {
            const currentPath = window.location.pathname;

            if (currentPath.startsWith('/admin/') && (!currentUser || currentUser.role !== 'admin')) {
                showAlert('Access denied. Admin privileges required.', 'danger');
                window.location.href = '/';
                return false;
            }

            if (currentPath.startsWith('/creator/') && (!currentUser || currentUser.role !== 'creator')) {
                showAlert('Access denied. Creator account required.', 'danger');
                window.location.href = '/';
                return false;
            }

            if (currentPath.startsWith('/buyer/') && (!currentUser || currentUser.role !== 'buyer')) {
                showAlert('Access denied. Buyer account required.', 'danger');
                window.location.href = '/';
                return false;
            }

            return true;
        }

        // Enhanced API call function with authentication
        async function apiCall(url, method = 'GET', data = null) {
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            };

            // Add authorization header if we have a token
            if (authToken) {
                options.headers['Authorization'] = `Bearer ${authToken}`;
            }

            if (data) {
                options.body = JSON.stringify(data);
            }

            try {
                console.log(`API Call: ${method} ${url}`, data);
                const response = await fetch(url, options);
                const result = await response.json();

                console.log(`API Response:`, result);

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
                    status: response.status
                };
            } catch (error) {
                console.error('API call failed:', error);
                return {
                    success: false,
                    error: error.message
                };
            }
        }

        // Show alert messages
        function showAlert(message, type = 'info', containerId = 'alerts') {
            console.log(`Alert: ${type} - ${message}`);

            const alertsContainer = document.getElementById(containerId);
            if (!alertsContainer) {
                // If no specific container, try to find a general alerts container
                const generalContainer = document.getElementById('alerts');
                if (!generalContainer) {
                    // Create a temporary alert at the top of the page
                    const tempAlert = document.createElement('div');
                    tempAlert.className = `alert alert-${type} alert-dismissible fade show`;
                    tempAlert.style.position = 'fixed';
                    tempAlert.style.top = '10px';
                    tempAlert.style.right = '10px';
                    tempAlert.style.zIndex = '9999';
                    tempAlert.innerHTML = `
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(tempAlert);

                    setTimeout(() => {
                        if (tempAlert.parentNode) {
                            tempAlert.parentNode.removeChild(tempAlert);
                        }
                    }, 5000);
                    return;
                }
            }

            const container = alertsContainer || document.getElementById('alerts');
            if (!container) return;

            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            container.innerHTML = alertHtml;

            // Auto dismiss after 5 seconds
            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }

        // Test individual services
        async function testService(serviceName) {
            const statusElement = document.getElementById(`${serviceName}-status`);
            if (!statusElement) return;

            statusElement.textContent = 'Testing...';
            statusElement.className = 'badge bg-warning status-badge';

            try {
                const response = await fetch(`${services[serviceName]}/api/test`);
                if (response.ok) {
                    statusElement.textContent = 'Online';
                    statusElement.className = 'badge bg-success status-badge';
                } else {
                    throw new Error('Service unavailable');
                }
            } catch (error) {
                statusElement.textContent = 'Offline';
                statusElement.className = 'badge bg-danger status-badge';
            }
        }

        // Test all services
        async function testAllServices() {
            Object.keys(services).forEach(testService);
        }

        // Updated login handler for your layouts/app.blade.php

document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    
    console.log('=== LOGIN ATTEMPT ===');
    console.log('Email:', email);
    
    try {
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                email: email,
                password: password
            })
        });
        
        console.log('Login response status:', response.status);
        const result = await response.json();
        console.log('Login response data:', result);
        
        // Check for success in the response
        if (response.ok && result.success && result.token && result.user) {
            console.log('=== LOGIN SUCCESS ===');
            console.log('Token:', result.token);
            console.log('User:', result.user);
            
            // Store authentication data
            localStorage.setItem('auth_token', result.token);
            localStorage.setItem('auth_user', JSON.stringify(result.user));
            
            // Update global variables
            authToken = result.token;
            currentUser = result.user;
            
            console.log('Stored user:', currentUser);
            console.log('Stored token:', authToken);
            
            // Update UI immediately
            updateAuthUI(true);
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
            if (modal) {
                modal.hide();
                console.log('Modal closed');
            }
            
            // Show success message
            showAlert(`Welcome back, ${result.user.name || result.user.username}!`, 'success');
            
            // Reset form
            document.getElementById('loginForm').reset();
            
            // Redirect to dashboard
            console.log('Redirecting to dashboard for role:', result.user.role);
            setTimeout(() => {
                redirectToDashboard(result.user.role);
            }, 1500);
            
        } else {
            console.log('=== LOGIN FAILED ===');
            console.log('Response ok:', response.ok);
            console.log('Result success:', result.success);
            console.log('Has token:', !!result.token);
            console.log('Has user:', !!result.user);
            
            const errorMsg = result.message || 'Login failed. Please check your credentials.';
            showAlert(errorMsg, 'danger', 'loginAlerts');
        }
        
    } catch (error) {
        console.error('=== LOGIN ERROR ===');
        console.error('Login error:', error);
        showAlert('Error connecting to server. Please try again.', 'danger', 'loginAlerts');
    }
});
        // REGISTER FORM HANDLER - This was missing!
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const password = document.getElementById('registerPassword').value;
            const passwordConfirm = document.getElementById('registerPasswordConfirm').value;

            // Check password confirmation
            if (password !== passwordConfirm) {
                showAlert('Passwords do not match.', 'danger', 'registerAlerts');
                return;
            }

            const userData = {
                name: document.getElementById('registerName').value,
                username: document.getElementById('registerUsername').value,
                email: document.getElementById('registerEmail').value,
                password: password,
                password_confirmation: passwordConfirm,
                role: document.getElementById('registerRole').value,
                bio: document.getElementById('registerBio').value
            };

            console.log('=== REGISTER ATTEMPT ===');
            console.log('User data:', userData);

            try {
                const result = await apiCall('/api/auth/register', 'POST', userData);

                console.log('=== REGISTER RESULT ===');
                console.log('Result:', result);

                if (result.success && result.data.token && result.data.user) {
                    console.log('=== REGISTER SUCCESS ===');

                    // Store authentication data
                    localStorage.setItem('auth_token', result.data.token);
                    localStorage.setItem('auth_user', JSON.stringify(result.data.user));

                    // Update global variables
                    authToken = result.data.token;
                    currentUser = result.data.user;

                    // Update UI
                    updateAuthUI(true);

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                    if (modal) {
                        modal.hide();
                    }

                    // Show success message
                    showAlert(
                        `Welcome to Digital Marketplace, ${result.data.user.name || result.data.user.username}!`,
                        'success');

                    // Reset form
                    document.getElementById('registerForm').reset();

                    // Redirect to dashboard
                    setTimeout(() => {
                        redirectToDashboard(result.data.user.role);
                    }, 1500);

                } else {
                    console.log('=== REGISTER FAILED ===');
                    const errorMsg = result.data?.message || 'Registration failed. Please try again.';
                    showAlert(errorMsg, 'danger', 'registerAlerts');
                }
            } catch (error) {
                console.error('=== REGISTER ERROR ===');
                console.error('Registration error:', error);
                showAlert('Error connecting to server. Please try again.', 'danger', 'registerAlerts');
            }
        });

        // Auto-suggest dashboard when logged in user visits home page
        document.addEventListener('DOMContentLoaded', function() {
            // If user is logged in and on home page, suggest going to dashboard
            if (currentUser && window.location.pathname === '/') {
                setTimeout(() => {
                    const roleText = currentUser.role === 'admin' ? 'Admin' :
                        currentUser.role === 'creator' ? 'Creator' : 'Buyer';

                    console.log('User is logged in on home page, showing dashboard suggestion');

                    // Show a non-intrusive suggestion
                    const suggestionHtml = `
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <strong>Welcome back, ${currentUser.name || currentUser.username}!</strong> 
                            Would you like to go to your <a href="#" onclick="goToDashboard()" class="alert-link">${roleText} Dashboard</a>?
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;

                    // Insert at top of main content
                    const mainContent = document.querySelector('main .container');
                    if (mainContent) {
                        mainContent.insertAdjacentHTML('afterbegin', suggestionHtml);
                    }
                }, 2000);
            }
        });
        <!--test for login modal -->
        // SIMPLE DEBUG LOGIN TEST - Add this temporarily to test

// Replace your login form handler with this simple version for testing
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    console.log('=== LOGIN FORM SUBMITTED ===');
    
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    
    console.log('Email:', email);
    console.log('Password length:', password.length);
    
    // Test 1: Check if we can make a simple API call
    console.log('Testing API call...');
    
    try {
        const testResponse = await fetch('/api/test');
        const testResult = await testResponse.json();
        console.log('API Test Result:', testResult);
    } catch (error) {
        console.error('API Test Failed:', error);
    }
    
    // Test 2: Try the actual login
    console.log('Attempting login...');
    
    try {
        const loginData = {
            email: email,
            password: password
        };
        
        console.log('Sending login data:', loginData);
        
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(loginData)
        });
        
        console.log('Login response status:', response.status);
        console.log('Login response ok:', response.ok);
        
        const result = await response.json();
        console.log('Login response data:', result);
        
        if (response.ok && result.token && result.user) {
            console.log('=== LOGIN SUCCESS ===');
            console.log('Token:', result.token);
            console.log('User:', result.user);
            
            // Store data
            localStorage.setItem('auth_token', result.token);
            localStorage.setItem('auth_user', JSON.stringify(result.user));
            
            // Update globals
            authToken = result.token;
            currentUser = result.user;
            
            console.log('Data stored in localStorage');
            console.log('Global authToken:', authToken);
            console.log('Global currentUser:', currentUser);
            
            // Test redirect immediately
            console.log('Testing immediate redirect...');
            const role = result.user.role;
            console.log('User role:', role);
            
            // Try different redirect approaches
            if (role === 'admin') {
                console.log('Attempting redirect to admin dashboard...');
                
                // Method 1: Direct redirect
                window.location.href = '/admin/dashboard';
                
                // If that doesn't work, try these:
                // window.location.assign('/admin/dashboard');
                // window.location.replace('/admin/dashboard');
            } else {
                console.log('User is not admin, role is:', role);
            }
            
        } else {
            console.log('=== LOGIN FAILED ===');
            console.log('Response was not ok or missing data');
            console.log('Response ok:', response.ok);
            console.log('Has token:', !!result.token);
            console.log('Has user:', !!result.user);
            
            alert('Login failed: ' + (result.message || 'Unknown error'));
        }
        
    } catch (error) {
        console.error('=== LOGIN ERROR ===');
        console.error('Error details:', error);
        alert('Login error: ' + error.message);
    }
});
    </script>

    @yield('scripts')
</body>

</html>
