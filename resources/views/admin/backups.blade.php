@extends('layouts.app')

@section('title', 'System Backups - IETI System')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold"><i class="fas fa-database me-2 text-info"></i> System & Database Backups</h2>
            <p class="text-muted">Manage system restoration points and database backups.</p>
        </div>
        <div class="col-md-4 text-end">
            <form action="{{ route('admin.backups.create.db') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-dark shadow-sm fw-bold">
                    <i class="fas fa-plus-circle me-1"></i> Generate New Backup
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-file-archive me-2"></i> Available Backup Files</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>File Name</th>
                            <th>Size</th>
                            <th>Created Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                        <tr>
                            <td class="fw-bold">{{ $backup['name'] }}</td>
                            <td>{{ number_format($backup['size'] / 1024, 2) }} KB</td>
                            <td>{{ date('Y-m-d H:i:s', $backup['date']) }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.backups.download', $backup['name']) }}" class="btn btn-sm btn-info text-white me-1">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <form action="{{ route('admin.backups.delete', $backup['name']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this backup?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No backup files found. Click "Generate New Backup" to create one.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection