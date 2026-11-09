@extends('layouts.app')

@section('title', 'Manage Users - IETI System')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold"><i class="fas fa-users-cog me-2 text-info"></i>User Management</h2>
            <p class="text-muted small">Manage user accounts, toggle activation status, update roles, or delete users.</p>
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

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td class="fw-bold">{{ $users->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($user->profile_picture)
                                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Avatar" class="rounded-circle me-2" width="30" height="30" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ strtoupper(substr($user->username, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span>{{ $user->username }}</span>
                                    </div>
                                </td>
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
                                <td class="small text-muted">{{ $user->created_at->format('M d, Y h:i A') }}</td>
                                <td class="text-end">
                                    <!-- Toggle Status Button triggers modal -->
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

                                    <!-- Delete Button triggers modal -->
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmActionModal" 
                                            data-action-url="{{ route('admin.users.destroy', $user->id) }}" 
                                            data-action-method="DELETE"
                                            data-action-title="Delete User Account"
                                            data-action-message="Are you sure you want to permanently delete user <strong>{{ $user->username }}</strong>? This action cannot be undone."
                                            data-btn-class="btn-danger"
                                            data-btn-text="Yes, Delete"
                                            title="Delete User">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
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