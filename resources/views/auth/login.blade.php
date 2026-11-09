@extends('layouts.app')

@section('title', 'Sign In - IETI System')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm border-0 p-4" style="width: 100%; max-width: 450px;">
        <div class="text-center mb-4">
            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                <i class="fas fa-sign-in-alt"></i>
            </div>
            <h3 class="fw-bold">Sign In</h3>
            <p class="text-muted small">Access your IETI System account</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label fw-bold">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Enter username" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="captcha_answer" class="form-label fw-bold">Math CAPTCHA: What is {{ $num1 }} + {{ $num2 }}?</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calculator"></i></span>
                    <input type="number" name="captcha_answer" id="captcha_answer" class="form-control @error('captcha_answer') is-invalid @enderror" placeholder="Answer" required>
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label for="remember" class="form-check-label small">Remember Me</label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-dark fw-bold">
                    <i class="fas fa-sign-in-alt me-1"></i> Login
                </button>
            </div>
        </form>

        <div class="text-center mt-3">
            <p class="small text-muted">Don't have an account? <a href="{{ route('register') }}" class="text-decoration-none fw-bold">Register here</a></p>
        </div>
    </div>
</div>
@endsection