@extends('layouts.app')

@section('title', 'Enable 2FA - IETI System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold"><i class="fas fa-shield-alt text-info me-2"></i> Enable Two-Factor Authentication</h3>
                    <p class="text-muted small">Scan the QR code below with your Google Authenticator app.</p>
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

                <div class="text-center mb-4">
                    <div class="d-inline-block p-2 bg-white rounded shadow-sm">
                        {!! $qrCodeSvg !!}
                    </div>
                    <p class="mt-3 text-muted small">Secret Key: <code class="fw-bold text-dark">{{ $secret }}</code></p>
                </div>

                <form action="{{ route('2fa.enable.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Enter 6-digit Code from App</label>
                        <input type="text" name="code" class="form-control text-center fs-4 tracking-widest" placeholder="000000" maxlength="6" required autofocus autocomplete="one-time-code">
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-2 fw-bold shadow-sm">
                        <i class="fas fa-check-circle me-1"></i> Verify and Enable 2FA
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection