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
                        <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}"
                            href="{{ url('/products') }}">
                            <i class="fas fa-box"></i> Browse Products
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
                        <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}"
                            href="{{ url('/admin/users') }}">
                            <i class="fas fa-users"></i> Manage Users
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

                    <!-- Shared Links (for logged-in users) -->
                    <li class="nav-item authenticated-only d-none">
                        <a class="nav-link {{ request()->is('orders*') ? 'active' : '' }}"
                            href="{{ url('/orders') }}">
                            <i class="fas fa-list"></i> All Orders
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
        // Complete Authentication Functions - Add this to layouts/app.blade.php after the existing scripts

        // Show register modal
        // Show register modal
        function showRegisterModal() {
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
            // Hide register modal if open
            const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
            if (registerModal) {
                registerModal.hide();
            }

            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }

        // Login form submission handler
        // Login form submission handler
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            console.log('Attempting login for:', email);

            try {
                const result = await apiCall('/api/auth/login', 'POST', {
                    email: email,
                    password: password
                });

                console.log('Login result:', result);

                if (result.success && result.data.token && result.data.user) {
                    // 1. Store authentication data
                    localStorage.setItem('auth_token', result.data.token);
                    localStorage.setItem('auth_user', JSON.stringify(result.data.user));

                    // 2. Update global variables
                    authToken = result.data.token;
                    currentUser = result.data.user;

                    // 3. Update UI immediately (this fixes the navbar)
                    updateAuthUI(true);

                    // 4. Close modal (this fixes the modal staying open)
                    const modal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                    if (modal) {
                        modal.hide();
                    }

                    // 5. Show success message
                    showAlert(`Welcome back, ${result.data.user.name || result.data.user.username}!`,
                    'success');

                    // 6. Reset form
                    document.getElementById('loginForm').reset();

                    // 7. Redirect to dashboard based on role (this fixes the redirect)
                    setTimeout(() => {
                        redirectToDashboard(result.data.user.role);
                    }, 1500);

                } else {
                    const errorMsg = result.data?.message || 'Login failed. Please check your credentials.';
                    showAlert(errorMsg, 'danger', 'loginAlerts');
                }
            } catch (error) {
                console.error('Login error:', error);
                showAlert('Error connecting to server. Please try again.', 'danger', 'loginAlerts');
            }
        });

        // Register form submission handler
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

            console.log('Attempting registration for:', userData.email);

            try {
                const result = await apiCall('/api/auth/register', 'POST', userData);

                console.log('Registration result:', result);

                if (result.success && result.data.token && result.data.user) {
                    // Store authentication data
                    localStorage.setItem('auth_token', result.data.token);
                    localStorage.setItem('auth_user', JSON.stringify(result.data.user));

                    // Update global variables
                    authToken = result.data.token;
                    currentUser = result.data.user;

                    // Update UI immediately
                    updateAuthUI(true);

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                    modal.hide();

                    // Show success message
                    showAlert(
                        `Welcome to Digital Marketplace, ${result.data.user.name || result.data.user.username}!`,
                        'success');

                    // Reset form
                    document.getElementById('registerForm').reset();

                    // Redirect to appropriate dashboard after delay
                    setTimeout(() => {
                        redirectToDashboard(result.data.user.role);
                    }, 1500);

                } else {
                    const errorMsg = result.data?.message || 'Registration failed. Please try again.';
                    showAlert(errorMsg, 'danger', 'registerAlerts');
                }
            } catch (error) {
                console.error('Registration error:', error);
                showAlert('Error connecting to server. Please try again.', 'danger', 'registerAlerts');
            }
        });

        // Logout function
        async function logout() {
            if (!confirm('Are you sure you want to logout?')) {
                return;
            }

            try {
                // Call logout API (optional - for server-side cleanup)
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

        // Show user profile (placeholder)
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
            // TODO: Replace with a proper profile modal
        }

        // Redirect function (you need this too)
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
        // Auto-suggest dashboard when logged in user visits home page
        document.addEventListener('DOMContentLoaded', function() {
            // If user is logged in and on home page, suggest going to dashboard
            if (currentUser && window.location.pathname === '/') {
                setTimeout(() => {
                    const roleText = currentUser.role === 'admin' ? 'Admin' :
                        currentUser.role === 'creator' ? 'Creator' : 'Buyer';

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

        // Handle browser back/forward navigation
        window.addEventListener('popstate', function() {
            checkPageAccess();
        });

        // Refresh token periodically (optional enhancement)
        setInterval(async function() {
            if (authToken && currentUser) {
                try {
                    const result = await apiCall('/api/auth/me');
                    if (!result.success) {
                        console.log('Token validation failed, logging out...');
                        logout();
                    }
                } catch (error) {
                    console.log('Token refresh check failed');
                }
            }
        }, 300000); // Check every 5 minutes
        // Global authentication state
        let currentUser = null;
        let authToken = null;

        // Check if user is logged in on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkAuthStatus();
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
        // Make sure you also have the enhanced updateAuthUI function
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
                // Hide login buttons, show user dropdown
                loginNavItem.classList.add('d-none');
                userNavDropdown.classList.remove('d-none');

                // Update user info in navbar
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

                console.log('UI updated for logged in user:', currentUser.role);

            } else {
                // Show login buttons, hide user dropdown
                loginNavItem.classList.remove('d-none');
                userNavDropdown.classList.add('d-none');

                console.log('UI updated for logged out state');
            }
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

        // Check if user has required role for current page
        // Enhanced Page Protection - Add this to layouts/app.blade.php

        // Enhanced checkPageAccess function with better protection
        function checkPageAccess() {
            const currentPath = window.location.pathname;

            // Skip protection for public pages
            const publicPages = ['/', '/products', '/login', '/register'];
            if (publicPages.includes(currentPath)) {
                return true;
            }

            // If user is not logged in and trying to access protected pages
            if (!currentUser || !authToken) {
                const protectedPages = ['/admin/', '/creator/', '/buyer/', '/orders'];
                const isProtectedPage = protectedPages.some(page => currentPath.startsWith(page));

                if (isProtectedPage) {
                    showAlert('Please login to access this page.', 'warning');

                    // Show login modal instead of redirecting
                    setTimeout(() => {
                        showLoginModal();
                    }, 1000);

                    // Redirect to home after a delay
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);

                    return false;
                }
            }

            // Role-based access control
            if (currentUser && authToken) {
                // Admin-only pages
                if (currentPath.startsWith('/admin/')) {
                    if (currentUser.role !== 'admin') {
                        showAlert('Access denied. Admin privileges required.', 'danger');
                        redirectToUserDashboard();
                        return false;
                    }
                }

                // Creator-only pages
                if (currentPath.startsWith('/creator/')) {
                    if (currentUser.role !== 'creator') {
                        showAlert('Access denied. Creator account required.', 'danger');
                        redirectToUserDashboard();
                        return false;
                    }
                }

                // Buyer-only pages
                if (currentPath.startsWith('/buyer/')) {
                    if (currentUser.role !== 'buyer') {
                        showAlert('Access denied. Buyer account required.', 'danger');
                        redirectToUserDashboard();
                        return false;
                    }
                }

                // Special case: /orders page (accessible by admin and buyers)
                if (currentPath === '/orders') {
                    if (!['admin', 'buyer'].includes(currentUser.role)) {
                        showAlert('Access denied. You cannot view orders.', 'danger');
                        redirectToUserDashboard();
                        return false;
                    }
                }

                // Special case: /products management (admin and creators)
                if (currentPath === '/products' && currentUser.role === 'buyer') {
                    // Buyers should go to browse page, not management page
                    window.location.href = '/products/browse';
                    return false;
                }
            }

            return true;
        }

        // Helper function to redirect user to their appropriate dashboard
        function redirectToUserDashboard() {
            if (!currentUser) {
                window.location.href = '/';
                return;
            }

            const dashboards = {
                'admin': '/admin/dashboard',
                'creator': '/creator/dashboard',
                'buyer': '/buyer/dashboard'
            };

            const userDashboard = dashboards[currentUser.role] || '/';

            setTimeout(() => {
                window.location.href = userDashboard;
            }, 1500);
        }

        // Enhanced auth status checking with token validation
        async function validateAuthToken() {
            if (!authToken || !currentUser) {
                return false;
            }

            try {
                // Try to validate token with backend
                const result = await apiCall('/api/auth/me');

                if (!result.success) {
                    // Token is invalid, clear auth and redirect
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('auth_user');
                    authToken = null;
                    currentUser = null;
                    updateAuthUI(false);

                    showAlert('Your session has expired. Please login again.', 'warning');
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);

                    return false;
                }

                return true;
            } catch (error) {
                console.log('Token validation failed:', error);
                return false;
            }
        }

        // Run comprehensive auth checks on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check auth status first
            checkAuthStatus();

            // Then validate token and check page access
            setTimeout(async () => {
                if (authToken) {
                    const isValidToken = await validateAuthToken();
                    if (!isValidToken) {
                        return; // Token validation already handled redirect
                    }
                }

                // Check page access permissions
                checkPageAccess();
            }, 100);
        });

        // Check auth on page visibility change (when user comes back to tab)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && authToken) {
                validateAuthToken();
            }
        });

        // Check auth when user navigates (browser back/forward)
        window.addEventListener('popstate', function() {
            setTimeout(() => {
                checkPageAccess();
            }, 100);
        });

        // Monitor for manual URL changes
        let currentUrl = window.location.href;
        setInterval(() => {
            if (currentUrl !== window.location.href) {
                currentUrl = window.location.href;
                setTimeout(() => {
                    checkPageAccess();
                }, 100);
            }
        }, 500);

        // Auto-refresh token validation every 5 minutes
        setInterval(async () => {
            if (authToken && currentUser) {
                await validateAuthToken();
            }
        }, 300000); // 5 minut

        // Run access check on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(checkPageAccess, 100); // Small delay to ensure auth state is loaded
        });

        // Rest of the authentication functions remain the same...
        // (Keep all the existing login, register, logout functions)
    </script>
    <script>
        // Global configuration
        const services = {
            gateway: 'http://localhost:8000',
            user: 'http://localhost:8001',
            product: 'http://localhost:8002',
            order: 'http://localhost:8003'
        };

        // CSRF token for Laravel
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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

        // Helper function for API calls
        async function apiCall(url, method = 'GET', data = null) {
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            };

            if (data) {
                options.body = JSON.stringify(data);
            }

            try {
                const response = await fetch(url, options);
                const result = await response.json();
                return {
                    success: response.ok,
                    data: result,
                    status: response.status
                };
            } catch (error) {
                return {
                    success: false,
                    error: error.message
                };
            }
        }

        // Show alert messages
        function showAlert(message, type = 'info', containerId = 'alerts') {
            const alertsContainer = document.getElementById(containerId);
            if (!alertsContainer) return;

            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            alertsContainer.innerHTML = alertHtml;

            // Auto dismiss after 5 seconds
            setTimeout(() => {
                const alert = alertsContainer.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }
    </script>

    @yield('scripts')
</body>

</html>
