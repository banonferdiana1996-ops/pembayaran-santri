@extends('layouts.app')

@section('title', 'Pengaturan User')

@section('page-title', 'Pengaturan User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Pengaturan User</li>
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
        <div class="col-lg-8">
            <div class="card card-soft border-0">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-user-gear text-primary me-2"></i>Profil Saya</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('account.updateProfile') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                   name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                   name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">No. HP</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                   name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary-grad"><i class="fas fa-save me-1"></i>Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
