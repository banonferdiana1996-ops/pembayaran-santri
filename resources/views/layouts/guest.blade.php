<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="{{ \App\Support\Setting::get('favicon', '/img/icon-192.png') }}">
    <meta name="theme-color" content="#2563eb">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="bg-landing">
<div class="container-fluid min-vh-100 d-flex flex-column">
    <div class="row flex-grow-1 align-items-center justify-content-center py-4">
        <div class="col-lg-5 col-md-7 col-sm-9 col-12">
            <div class="text-center text-white mb-4 fade-in-up">
                <img src="{{ \App\Support\Setting::get('logo', '/img/icon-192.png') }}" alt="Logo" class="rounded-4 shadow-lg mb-3" style="width: 90px; height: 90px; object-fit: cover;">
                <h2 class="fw-bold mb-1">{{ config('app.name') }}</h2>
                <p class="opacity-75 mb-0">{{ \App\Support\Setting::get('nama_institusi', 'Pondok Pesantren Darussalam') }}</p>
            </div>
            <div class="glass p-4 p-md-5 fade-in-up" style="animation-delay: .15s">
                @yield('content')
            </div>
            <div class="text-center text-white-50 small mt-3 fade-in-up" style="animation-delay: .25s">
                &copy; {{ date('Y') }} {{ \App\Support\Setting::get('nama_institusi', 'Pondok Pesantren Darussalam') }}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
