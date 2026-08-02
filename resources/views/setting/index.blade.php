@extends('layouts.app')

@section('title', 'Pengaturan')

@section('page-title', 'Pengaturan Aplikasi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Pengaturan</li>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-circle-check me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('setting.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card card-soft border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-building text-primary me-2"></i>Profil Institusi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nama_institusi" class="form-label">Nama Institusi</label>
                                <input type="text" class="form-control @error('nama_institusi') is-invalid @enderror" id="nama_institusi"
                                       name="nama_institusi" value="{{ old('nama_institusi', \App\Support\Setting::get('nama_institusi', config('app.name'))) }}" required>
                                @error('nama_institusi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2">{{ old('alamat', \App\Support\Setting::get('alamat', '')) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="telepon" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" maxlength="30"
                                       value="{{ old('telepon', \App\Support\Setting::get('telepon', '')) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                       value="{{ old('email', \App\Support\Setting::get('email', '')) }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-soft border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-image text-primary me-2"></i>Logo & Ikon</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img id="logoPreview" src="{{ \App\Support\Setting::get('logo', '/img/icon-192.png') }}"
                                 alt="Logo" class="rounded-4 shadow-sm" style="width: 90px; height: 90px; object-fit: cover;">
                            <div class="mt-2 small text-muted">Logo</div>
                        </div>
                        <div class="mb-3">
                            <input type="file" class="form-control form-control-sm" id="logo" name="logo" accept="image/*">
                        </div>
                        <div class="text-center mb-3">
                            <img id="faviconPreview" src="{{ \App\Support\Setting::get('favicon', '/img/icon-192.png') }}"
                                 alt="Favicon" class="rounded-4 shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                            <div class="mt-2 small text-muted">Favicon</div>
                        </div>
                        <div class="mb-2">
                            <input type="file" class="form-control form-control-sm" id="favicon" name="favicon" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-0">
            <div class="col-lg-8">
                <div class="card card-soft border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 fw-semibold"><i class="fab fa-whatsapp text-primary me-2"></i>Notifikasi WhatsApp</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="wa_enabled" name="wa_enabled" value="1"
                                   {{ \App\Support\Setting::get('wa_enabled', false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="wa_enabled">Aktifkan notifikasi otomatis</label>
                            <div class="form-text">Kirim notifikasi WhatsApp ke No. HP wali saat pembayaran selesai dicatat.</div>
                        </div>
                        <div class="mb-3">
                            <label for="wa_api_url" class="form-label">URL Gateway</label>
                            <input type="url" class="form-control" id="wa_api_url" name="wa_api_url"
                                   placeholder="https://api.fonnte.com/send"
                                   value="{{ old('wa_api_url', \App\Support\Setting::get('wa_api_url', 'https://api.fonnte.com/send')) }}">
                            <div class="form-text">Gunakan endpoint API yang kompatibel dengan Fonnte (POST + header Authorization).</div>
                        </div>
                        <div class="mb-3">
                            <label for="wa_api_token" class="form-label">Token API</label>
                            <input type="password" class="form-control" id="wa_api_token" name="wa_api_token"
                                   placeholder="Token API gateway WhatsApp"
                                   value="{{ old('wa_api_token', \App\Support\Setting::get('wa_api_token', '')) }}">
                            <div class="form-text">Token yang diterima dari penyedia layanan gateway WhatsApp Anda.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mt-3">
            <a href="{{ route('dashboard') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary-grad"><i class="fas fa-save me-1"></i>Simpan Pengaturan</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function () {
        function previewImage(input, target) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => $(target).attr('src', e.target.result);
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#logo').on('change', function () { previewImage(this, '#logoPreview'); });
        $('#favicon').on('change', function () { previewImage(this, '#faviconPreview'); });
        });
</script>
@endpush
