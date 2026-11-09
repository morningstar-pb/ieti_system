@extends('layouts.app')

@section('title', 'Dashboard - IETI System')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <!-- Welcome Banner -->
        <div class="card bg-dark text-white p-4 mb-4 shadow">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1">Welcome back, {{ Auth::user()->name ?? Auth::user()->username }}! 👋</h2>
                    <p class="text-light opacity-75 mb-0">IETI College of Science and Technology - Equipment & Borrowing System</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-info text-dark px-3 py-2 fs-6 fw-bold text-uppercase">Role: {{ Auth::user()->role }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions Grid -->
        <h4 class="fw-bold mb-3 text-secondary"><i class="fas fa-th-large me-2"></i>System Modules</h4>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-top border-primary border-4">
                    <div class="card-body text-center p-4">
                        <div class="text-primary mb-3"><i class="fas fa-boxes fa-3x"></i></div>
                        <h5 class="fw-bold">Inventory Menu</h5>
                        <p class="text-muted small">Browse available school tools and equipment for borrowing.</p>
                        <button class="btn btn-outline-dark btn-sm w-100 mt-2" disabled>Coming Soon</button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-top border-success border-4">
                    <div class="card-body text-center p-4">
                        <div class="text-success mb-3"><i class="fas fa-handshake fa-3x"></i></div>
                        <h5 class="fw-bold">Borrowing Requests</h5>
                        <p class="text-muted small">Submit and track active item borrowing requests easily.</p>
                        <button class="btn btn-outline-dark btn-sm w-100 mt-2" disabled>Coming Soon</button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-top border-info border-4">
                    <div class="card-body text-center p-4">
                        <div class="text-info mb-3"><i class="fas fa-shield-alt fa-3x"></i></div>
                        <h5 class="fw-bold">2FA Security</h5>
                        <p class="text-muted small">Manage your Google Authenticator protection settings.</p>
                        <a href="{{ route('2fa.enable') }}" class="btn btn-dark btn-sm w-100 mt-2">Manage 2FA</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection