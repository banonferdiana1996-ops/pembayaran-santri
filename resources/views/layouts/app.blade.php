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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">

    <div id="preloader" class="bg-landing position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="z-index: 9999;">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
            <div class="fw-bold fs-4">SIP SANTRI PPDS</div>
            <div class="small opacity-75">Memuat aplikasi...</div>
        </div>
    </div>

    <nav class="main-header navbar navbar-expand navbar-white navbar-light sticky-top">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item d-none d-md-flex align-items-center me-3">
                <span class="clock-widget text-muted small"></span>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-user-circle fs-4 text-primary"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <div class="dropdown-item text-muted">
                        <strong>{{ auth()->user()->name }}</strong><br>
                        <span class="text-capitalize small">{{ auth()->user()->getRoleNames()->first() ?? '-' }}</span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('account.profile') }}" class="dropdown-item"><i class="fas fa-user-gear me-2"></i>Ubah Profil</a>
                    <a href="{{ route('account.password') }}" class="dropdown-item"><i class="fas fa-key me-2"></i>Ubah Password</a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i>Keluar
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('dashboard') }}" class="brand-link text-decoration-none">
            <img src="{{ \App\Support\Setting::get('logo', '/img/icon-192.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .9">
            <span class="brand-text fw-light">{{ config('app.name') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-gauge-high"></i><p>Dashboard</p>
                        </a>
                    </li>

                    @role('admin')
                    <li class="nav-header">Master Data</li>
                    <li class="nav-item {{ request()->routeIs('tahun-ajaran*') ? 'menu-open' : '' }}">
                        <a href="{{ route('tahun-ajaran.index') }}" class="nav-link {{ request()->routeIs('tahun-ajaran*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i><p>Tahun Ajaran</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('kelas*') ? 'menu-open' : '' }}">
                        <a href="{{ route('kelas.index') }}" class="nav-link {{ request()->routeIs('kelas*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-school"></i><p>Kelas</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('santri*') ? 'menu-open' : '' }}">
                        <a href="{{ route('santri.index') }}" class="nav-link {{ request()->routeIs('santri*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-graduate"></i><p>Santri</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('users*') ? 'menu-open' : '' }}">
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i><p>Pengguna</p>
                        </a>
                    </li>
                    @endrole

                    @role('admin|bendahara')
                    <li class="nav-header">Pembayaran</li>
                    <li class="nav-item {{ request()->routeIs('jenis-pembayaran*') ? 'menu-open' : '' }}">
                        <a href="{{ route('jenis-pembayaran.index') }}" class="nav-link {{ request()->routeIs('jenis-pembayaran*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tags"></i><p>Jenis Pembayaran</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('tagihan*') ? 'menu-open' : '' }}">
                        <a href="{{ route('tagihan.index') }}" class="nav-link {{ request()->routeIs('tagihan*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice"></i><p>Tagihan</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('pembayaran*') ? 'menu-open' : '' }}">
                        <a href="{{ route('pembayaran.index') }}" class="nav-link {{ request()->routeIs('pembayaran*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-money-bill-wave"></i><p>Pembayaran</p>
                        </a>
                    </li>
                    @endrole

                    @role('admin|bendahara')
                    <li class="nav-header">Keuangan</li>
                    <li class="nav-item {{ request()->routeIs('income*') ? 'menu-open' : '' }}">
                        <a href="{{ route('income.index') }}" class="nav-link {{ request()->routeIs('income*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hand-holding-dollar"></i><p>Pemasukan</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('expense*') ? 'menu-open' : '' }}">
                        <a href="{{ route('expense.index') }}" class="nav-link {{ request()->routeIs('expense*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hand-holding-usd"></i><p>Pengeluaran</p>
                        </a>
                    </li>
                    @endrole

                    @role('admin|bendahara')
                    <li class="nav-header">Laporan</li>
                    <li class="nav-item {{ request()->routeIs('report*') ? 'menu-open' : '' }}">
                        <a href="{{ route('report.index') }}" class="nav-link {{ request()->routeIs('report*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-lines"></i><p>Laporan</p>
                        </a>
                    </li>
                    @endrole

                    @role('admin')
                    <li class="nav-header">Pengaturan</li>
                    <li class="nav-item {{ request()->routeIs('announcement*') ? 'menu-open' : '' }}">
                        <a href="{{ route('announcement.index') }}" class="nav-link {{ request()->routeIs('announcement*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-bullhorn"></i><p>Pengumuman</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('setting*') ? 'menu-open' : '' }}">
                        <a href="{{ route('setting.index') }}" class="nav-link {{ request()->routeIs('setting*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-gear"></i><p>Pengaturan</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('backup*') ? 'menu-open' : '' }}">
                        <a href="{{ route('backup.index') }}" class="nav-link {{ request()->routeIs('backup*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i><p>Backup</p>
                        </a>
                    </li>
                    @endrole
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <strong>SIP SANTRI PPDS</strong> — Sistem Informasi Pembayaran Santri
        <div class="float-end d-none d-sm-inline">&copy; {{ date('Y') }}</div>
    </footer>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
<script defer src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script defer src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script defer src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script defer src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script defer src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script defer src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script defer src="{{ asset('js/app.js') }}"></script>
<script>
    (function () {
        var preloader = document.getElementById('preloader');
        function hidePreloader() {
            if (!preloader || preloader.getAttribute('data-hidden')) return;
            preloader.setAttribute('data-hidden', '1');
            preloader.style.transition = 'opacity .4s';
            preloader.style.opacity = '0';
            setTimeout(function () { preloader.remove(); }, 500);
        }
        window.addEventListener('load', hidePreloader);
        setTimeout(hidePreloader, 2000);
    })();
</script>
@stack('scripts')
</body>
</html>
