@extends('layouts.app')

@section('title', 'Users Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-users"></i> Users Management</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-plus"></i> Create User
                </button>
            </div>
        </div>
    </div>

    <!-- Alerts Container -->
    <div id="alerts"></div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Loading users...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createUserForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="userName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="userName" required>
                        </div>
                        <div class="mb-3">
                            <label for="userUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="userUsername" required>
                        </div>
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="userEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="userPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="userPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="userRole" class="form-label">Role</label>
                            <select class="form-control" id="userRole" required>
                                <option value="">Select Role</option>
                                <option value="creator">Creator</option>
                                <option value="buyer">Buyer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="userBio" class="form-label">Bio (Optional)</label>
                            <textarea class="form-control" id="userBio" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm">
                    <div class="modal-body">
                        <input type="hidden" id="editUserId">
                        <div class="mb-3">
                            <label for="editUserName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editUserName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUserUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="editUserUsername" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUserEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editUserEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUserRole" class="form-label">Role</label>
                            <select class="form-control" id="editUserRole" required>
                                <option value="">Select Role</option>
                                <option value="creator">Creator</option>
                                <option value="buyer">Buyer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editUserBio" class="form-label">Bio (Optional)</label>
                            <textarea class="form-control" id="editUserBio" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="editUserVerified">
                                <label class="form-check-label" for="editUserVerified">
                                    Verified User
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let users = [];

        // Load users on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
        });

        // Load all users
        async function loadUsers() {
            try {
                const result = await apiCall('/api/users');

                if (result.success) {
                    users = Array.isArray(result.data) ? result.data : [];
                    displayUsers();
                } else {
                    showAlert('Failed to load users', 'danger');
                    document.getElementById('usersTableBody').innerHTML = `
                    <tr><td colspan="7" class="text-center text-muted">Failed to load users</td></tr>
                `;
                }
            } catch (error) {
                showAlert('Error loading users: ' + error.message, 'danger');
                document.getElementById('usersTableBody').innerHTML = `
                <tr><td colspan="7" class="text-center text-muted">Error loading users</td></tr>
            `;
            }
        }

        // Update this function to debug the data
        function displayUsers() {
            const tbody = document.getElementById('usersTableBody');

            // Debug: Log the users data to see what we're getting
            console.log('Users data:', users);

            if (users.length === 0) {
                tbody.innerHTML = `
            <tr><td colspan="7" class="text-center text-muted">No users found</td></tr>
        `;
                return;
            }

            tbody.innerHTML = users.map(user => {
                // Debug: Log individual user data
                console.log('User:', user);

                return `
            <tr>
                <td>${user.id}</td>
                <td>${user.name || user.username || 'No Name'}</td>
                <td>${user.username}</td>
                <td>${user.email}</td>
                <td>
                    <span class="badge bg-${getRoleBadgeColor(user.role)}">${user.role}</span>
                    ${user.is_verified ? '<span class="badge bg-success ms-1">Verified</span>' : ''}
                </td>
                <td>${formatDate(user.created_at)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewUser(${user.id})" title="View User">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="editUser(${user.id})" title="Edit User">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id})" title="Delete User">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
            }).join('');
        }
        // Create user form submission
        document.getElementById('createUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const userData = {
                name: document.getElementById('userName').value,
                username: document.getElementById('userUsername').value,
                email: document.getElementById('userEmail').value,
                password: document.getElementById('userPassword').value,
                role: document.getElementById('userRole').value,
                bio: document.getElementById('userBio').value
            };

            try {
                const result = await apiCall('/api/users', 'POST', userData);

                if (result.success) {
                    showAlert('User created successfully!', 'success');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createUserModal'));
                    modal.hide();

                    // Reset form
                    document.getElementById('createUserForm').reset();

                    // Reload users
                    loadUsers();
                } else {
                    const errorMsg = result.data?.message || 'Failed to create user';
                    showAlert(errorMsg, 'danger');
                }
            } catch (error) {
                showAlert('Error creating user: ' + error.message, 'danger');
            }
        });

        // Edit user form submission
        document.getElementById('editUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const userId = document.getElementById('editUserId').value;
            const userData = {
                name: document.getElementById('editUserName').value,
                username: document.getElementById('editUserUsername').value,
                email: document.getElementById('editUserEmail').value,
                role: document.getElementById('editUserRole').value,
                bio: document.getElementById('editUserBio').value,
                is_verified: document.getElementById('editUserVerified').checked
            };

            try {
                const result = await apiCall(`/api/users/${userId}`, 'PUT', userData);

                if (result.success) {
                    showAlert('User updated successfully!', 'success');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                    modal.hide();

                    // Reload users
                    loadUsers();
                } else {
                    const errorMsg = result.data?.message || 'Failed to update user';
                    showAlert(errorMsg, 'danger');
                }
            } catch (error) {
                showAlert('Error updating user: ' + error.message, 'danger');
            }
        });

        // Helper functions
        function getRoleBadgeColor(role) {
            const colors = {
                'admin': 'danger',
                'creator': 'success',
                'buyer': 'primary'
            };
            return colors[role] || 'secondary';
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        }

        function viewUser(userId) {
            const user = users.find(u => u.id === userId);
            if (user) {
                const userDetails = `
                User Details:
                Name: ${user.name}
                Username: ${user.username}
                Email: ${user.email}
                Role: ${user.role}
                Bio: ${user.bio || 'N/A'}
                Verified: ${user.is_verified ? 'Yes' : 'No'}
                Created: ${formatDate(user.created_at)}
            `;
                alert(userDetails);
            }
        }

        function editUser(userId) {
            const user = users.find(u => u.id === userId);
            if (user) {
                // Populate edit form
                document.getElementById('editUserId').value = user.id;
                document.getElementById('editUserName').value = user.name || '';
                document.getElementById('editUserUsername').value = user.username;
                document.getElementById('editUserEmail').value = user.email;
                document.getElementById('editUserRole').value = user.role;
                document.getElementById('editUserBio').value = user.bio || '';
                document.getElementById('editUserVerified').checked = user.is_verified || false;

                // Show edit modal
                const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                modal.show();
            }
        }

        async function deleteUser(userId) {
            const user = users.find(u => u.id === userId);
            if (user && confirm(`Are you sure you want to delete user "${user.name || user.username}"?`)) {
                try {
                    const result = await apiCall(`/api/users/${userId}`, 'DELETE');

                    if (result.success) {
                        showAlert('User deleted successfully!', 'success');
                        // Reload users
                        loadUsers();
                    } else {
                        const errorMsg = result.data?.message || 'Failed to delete user';
                        showAlert(errorMsg, 'danger');
                    }
                } catch (error) {
                    showAlert('Error deleting user: ' + error.message, 'danger');
                }
            }
        }
    </script>
@endsection
