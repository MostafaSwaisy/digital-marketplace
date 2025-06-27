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
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ url('/users') }}">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}"
                            href="{{ url('/products') }}">
                            <i class="fas fa-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('orders*') ? 'active' : '' }}" href="{{ url('/orders') }}">
                            <i class="fas fa-shopping-cart"></i> Orders
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <!-- Authentication Links -->
                    <li class="nav-item" id="loginNavItem">
                        <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </li>
                    <li class="nav-item dropdown d-none" id="userNavDropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <span id="currentUserName">User</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="showProfile()"><i
                                        class="fas fa-user"></i> Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#" onclick="logout()"><i
                                        class="fas fa-sign-out-alt"></i> Logout</a></li>
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

        // Update authentication UI
        function updateAuthUI(isLoggedIn) {
            const loginNavItem = document.getElementById('loginNavItem');
            const userNavDropdown = document.getElementById('userNavDropdown');
            const currentUserName = document.getElementById('currentUserName');

            if (isLoggedIn && currentUser) {
                loginNavItem.classList.add('d-none');
                userNavDropdown.classList.remove('d-none');
                currentUserName.textContent = currentUser.name || currentUser.username;
            } else {
                loginNavItem.classList.remove('d-none');
                userNavDropdown.classList.add('d-none');
            }
        }

        // Login form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email,
                        password
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    // Store authentication data
                    localStorage.setItem('auth_token', result.access_token);
                    localStorage.setItem('auth_user', JSON.stringify(result.user));

                    authToken = result.access_token;
                    currentUser = result.user;

                    // Update UI
                    updateAuthUI(true);

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                    modal.hide();

                    // Reset form
                    document.getElementById('loginForm').reset();

                    showAlert('Login successful! Welcome back, ' + result.user.name, 'success');
                } else {
                    document.getElementById('loginAlerts').innerHTML =
                        `<div class="alert alert-danger">${result.error || 'Login failed'}</div>`;
                }
            } catch (error) {
                document.getElementById('loginAlerts').innerHTML =
                    `<div class="alert alert-danger">Login error: ${error.message}</div>`;
            }
        });

        // Register form submission
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = {
                name: document.getElementById('registerName').value,
                username: document.getElementById('registerUsername').value,
                email: document.getElementById('registerEmail').value,
                password: document.getElementById('registerPassword').value,
                password_confirmation: document.getElementById('registerPasswordConfirm').value,
                role: document.getElementById('registerRole').value,
                bio: document.getElementById('registerBio').value
            };

            try {
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (response.ok) {
                    // Store authentication data
                    localStorage.setItem('auth_token', result.access_token);
                    localStorage.setItem('auth_user', JSON.stringify(result.user));

                    authToken = result.access_token;
                    currentUser = result.user;

                    // Update UI
                    updateAuthUI(true);

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                    modal.hide();

                    // Reset form
                    document.getElementById('registerForm').reset();

                    showAlert('Registration successful! Welcome, ' + result.user.name, 'success');
                } else {
                    const errors = result.errors;
                    let errorHtml = '<div class="alert alert-danger">';
                    if (errors) {
                        for (const [field, messages] of Object.entries(errors)) {
                            errorHtml += messages.join('<br>') + '<br>';
                        }
                    } else {
                        errorHtml += result.message || 'Registration failed';
                    }
                    errorHtml += '</div>';
                    document.getElementById('registerAlerts').innerHTML = errorHtml;
                }
            } catch (error) {
                document.getElementById('registerAlerts').innerHTML =
                    `<div class="alert alert-danger">Registration error: ${error.message}</div>`;
            }
        });

        // Logout function
        async function logout() {
            try {
                // Call logout endpoint
                if (authToken) {
                    await fetch('/api/auth/logout', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + authToken,
                            'Accept': 'application/json'
                        }
                    });
                }
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                // Clear local storage
                localStorage.removeItem('auth_token');
                localStorage.removeItem('auth_user');

                authToken = null;
                currentUser = null;

                // Update UI
                updateAuthUI(false);

                showAlert('Logged out successfully', 'info');
            }
        }

        // Show profile function
        function showProfile() {
            if (currentUser) {
                alert(
                    `Profile Information:\nName: ${currentUser.name}\nUsername: ${currentUser.username}\nEmail: ${currentUser.email}\nRole: ${currentUser.role}`
                    );
            }
        }

        // Show modals
        function showLoginModal() {
            const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
            if (registerModal) registerModal.hide();

            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }

        function showRegisterModal() {
            const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
            if (loginModal) loginModal.hide();

            const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
            registerModal.show();
        }

        // Update apiCall function to include auth token
        async function apiCall(url, method = 'GET', data = null) {
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            };

            // Add auth token if available
            if (authToken) {
                options.headers['Authorization'] = 'Bearer ' + authToken;
            }

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
