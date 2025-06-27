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

            } else {
                loginNavItem.classList.remove('d-none');
                userNavDropdown.classList.add('d-none');
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

            return true;
        }

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
