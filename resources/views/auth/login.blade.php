@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <h1 class="h4 fw-bold text-center mb-1">Masuk ke Akun Anda</h1>
    <p class="text-muted text-center small mb-4">Silakan masuk menggunakan email dan password Anda.</p>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-circle-exclamation me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-envelope text-primary"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                       name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
            </div>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <label for="password" class="form-label">Password</label>
            </div>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-lock text-primary"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                       name="password" placeholder="••••••••" required>
                <button type="button" class="input-group-text bg-white border-start-0" id="togglePassword" tabindex="-1">
                    <i class="fas fa-eye text-muted"></i>
                </button>
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
            <label class="form-check-label" for="remember">Ingat saya</label>
        </div>

        <button type="submit" class="btn btn-primary-grad w-100 py-2 fw-semibold">
            <i class="fas fa-right-to-bracket me-2"></i>Masuk
        </button>
    </form>
@endsection

@push('scripts')
    <script>
        $('#togglePassword').on('click', function () {
            const $input = $('#password');
            const $icon = $(this).find('i');
            const type = $input.attr('type') === 'password' ? 'text' : 'password';
            $input.attr('type', type);
            $icon.toggleClass('fa-eye fa-eye-slash');
        });
    </script>
@endpush
