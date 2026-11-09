@extends('layouts.app')

@section('title', 'Register - IETI System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <div class="text-center mb-4">
                    <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-user-plus fa-lg"></i>
                    </div>
                    <h3 class="fw-bold">Create Account</h3>
                    <p class="text-muted small">Register a new IETI System account</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.submit') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="Choose a username" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="At least 6 characters" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Math CAPTCHA: What is {{ $num1 }} + {{ $num2 }}?</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-calculator text-muted"></i></span>
                            <!-- CRITICAL: name MUST be captcha_answer -->
                            <input type="number" name="captcha_answer" class="form-control" placeholder="Answer" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-info w-100 py-2 fw-bold text-white shadow-sm">
                        <i class="fas fa-user-check me-1"></i> Register & Setup 2FA
                    </button>
                </form>

                <div class="text-center mt-3 small">
                    <span class="text-muted">Already have an account?</span> <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Sign in here</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection