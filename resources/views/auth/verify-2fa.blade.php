@extends('layouts.app')

@section('title', 'Verify 2FA - IETI System')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm p-4">
            <div class="text-center mb-4">
                <div class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 55px; height: 55px;">
                    <i class="fas fa-shield-alt fa-lg text-info"></i>
                </div>
                <h3 class="fw-bold">Two-Factor Authentication</h3>
                <p class="text-muted small">Enter the 6-digit code from your Google Authenticator app</p>
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

            <form action="{{ route('2fa.verify.submit') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-bold">Authentication Code</label>
                    <input type="text" name="code" class="form-control text-center fs-4 tracking-widest" placeholder="000000" maxlength="6" required autofocus autocomplete="one-time-code">
                </div>

                <button type="submit" class="btn btn-dark w-100 py-2 mb-3 fw-bold shadow-sm">
                    <i class="fas fa-check-circle me-1"></i> Verify Code
                </button>
            </form>
        </div>
    </div>
</div>
@endsection