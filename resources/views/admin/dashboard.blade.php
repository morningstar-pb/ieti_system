@extends('layouts.app')

@section('title', 'Admin Dashboard - IETI System')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold"><i class="fas fa-tachometer-alt me-2 text-info"></i>Admin Dashboard</h2>
            <p class="text-muted small">Welcome back, {{ Auth::user()->username }}! System overview and quick management console.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users me-2"></i>Total Users</h5>
                    <h3 class="fw-bold mb-0">{{ $totalUsers ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-shield-alt me-2"></i>Active Session</h5>
                    <h3 class="fw-bold mb-0">Secure</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-dark text-white shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-database me-2"></i>System Backups</h5>
                    <a href="{{ route('admin.backups') }}" class="btn btn-outline-light btn-sm mt-1">Manage Backups</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users Management Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Recent Users Management</h5>
            <a href="{{ route('admin.users') }}" class="btn btn-info btn-sm text-white fw-bold">View All Users</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users ?? $recentUsers ?? [] as $index => $user)
                            <tr>
                                <td class="fw-bold">{{ method_exists($users ?? null, 'firstItem') ? $users->firstItem() + $index : $index + 1 }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Deactivated</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <!-- Toggle Status Button with Modal Trigger -->
                                    <button type="button" class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmActionModal" 
                                            data-action-url="{{ route('admin.users.toggle', $user->id) }}" 
                                            data-action-method="PATCH"
                                            data-action-title="{{ $user->is_active ? 'Deactivate User Account' : 'Activate User Account' }}"
                                            data-action-message="Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} user <strong>{{ $user->username }}</strong>?"
                                            data-btn-class="{{ $user->is_active ? 'btn-warning' : 'btn-success' }}"
                                            data-btn-text="{{ $user->is_active ? 'Yes, Deactivate' : 'Yes, Activate' }}"
                                            title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>

                                    <!-- Delete Button with Modal Trigger -->
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmActionModal" 
                                            data-action-url="{{ route('admin.users.destroy', $user->id) }}" 
                                            data-action-method="DELETE"
                                            data-action-title="Delete User Account"
                                            data-action-message="Are you sure you want to permanently delete user <strong>{{ $user->username }}</strong>? This action cannot be undone."
                                            data-btn-class="btn-danger"
                                            data-btn-text="Yes, Delete"
                                            title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($users ?? null, 'links'))
                <div class="d-flex justify-content-center mt-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- System Audit Trail / History Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>System Activity History (Audit Trail)</h5>
            <a href="{{ route('admin.audit') }}" class="btn btn-outline-light btn-sm">View Full Audit Log</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle small">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Action Type</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditTrails ?? [] as $audit)
                            <tr>
                                <td>{{ $audit->created_at->format('M d, Y h:i:s A') }}</td>
                                <td class="fw-bold">{{ $audit->user ? $audit->user->username : 'System / Guest' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $audit->action_type }}</span>
                                </td>
                                <td>{{ $audit->description }}</td>
                                <td class="text-muted">{{ $audit->ip_address }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No audit history recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Universal Confirmation Modal -->
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="confirmActionForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="confirmActionMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="confirmActionModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmActionText" class="mb-0">Are you sure you want to perform this action?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="confirmActionBtn" class="btn btn-sm text-white">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Script handler -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const confirmModal = document.getElementById('confirmActionModal');
        confirmModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const url = button.getAttribute('data-action-url');
            const method = button.getAttribute('data-action-method');
            const title = button.getAttribute('data-action-title');
            const message = button.getAttribute('data-action-message');
            const btnClass = button.getAttribute('data-btn-class');
            const btnText = button.getAttribute('data-btn-text');

            const form = document.getElementById('confirmActionForm');
            const methodInput = document.getElementById('confirmActionMethod');
            const modalTitle = document.getElementById('confirmActionModalLabel');
            const modalText = document.getElementById('confirmActionText');
            const confirmBtn = document.getElementById('confirmActionBtn');

            form.setAttribute('action', url);
            methodInput.value = method;
            modalTitle.textContent = title;
            modalText.innerHTML = message;
            confirmBtn.className = 'btn btn-sm ' + btnClass;
            confirmBtn.textContent = btnText;
        });
    });
</script>
@endsection