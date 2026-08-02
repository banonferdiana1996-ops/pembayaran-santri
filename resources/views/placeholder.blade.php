@extends('layouts.app')

@section('title', $modul)

@section('page-title', $modul)

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ $modul }}</li>
@endsection

@section('content')
    <div class="card card-soft border-0">
        <div class="card-body text-center py-5">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 88px; height: 88px;">
                <i class="fas fa-hammer fs-2"></i>
            </div>
            <h4 class="fw-bold mb-1">Modul {{ $modul }} Sedang Dibangun</h4>
            <p class="text-muted mb-4">Modul ini akan tersedia pada tahap pengembangan berikutnya.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary-grad px-4">
                <i class="fas fa-gauge-high me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
@endsection
