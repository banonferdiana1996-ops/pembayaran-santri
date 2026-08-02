@extends('layouts.app')

@section('title', 'Ubah Password')

@section('page-title', 'Ubah Password')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Ubah Password</li>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-circle-check me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-circle-exclamation me-1"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-soft border-0">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-key text-primary me-2"></i>Ganti Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('account.updatePassword') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" autocomplete="current-password" required>
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" minlength="8" autocomplete="new-password" required>
                            <div class="form-text">Minimal 8 karakter.</div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation" autocomplete="new-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary-grad"><i class="fas fa-save me-1"></i>Perbarui Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
