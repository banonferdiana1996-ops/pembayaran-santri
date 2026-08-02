@extends('layouts.app')

@section('title', 'Kwitansi')

@section('page-title', 'Kwitansi Pembayaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pembayaran.index') }}">Pembayaran</a></li>
    <li class="breadcrumb-item active">Kwitansi</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card card-soft border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="fw-bold text-primary mb-0">{{ config('app.name') }}</h4>
                            <small class="text-muted">Pondok Pesantren Darussalam Putri</small>
                        </div>
                        <div class="text-end">
                            <div class="display-6 text-primary"><i class="fas fa-receipt"></i></div>
                            <strong>KWITANSI</strong>
                        </div>
                    </div>

                    <hr>

                    <div class="row small mb-3">
                        <div class="col-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted w-25">No.</td>
                                    <td class="w-75 fw-semibold">: {{ $pembayaran->nomor }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal</td>
                                    <td class="fw-semibold">: {{ $pembayaran->tanggal_bayar?->translatedFormat('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Santri</td>
                                    <td class="fw-semibold">: {{ $pembayaran->santri?->nama_lengkap }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kelas</td>
                                    <td>: {{ $pembayaran->santri?->kelas?->nama_kelas ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted w-25">Jenis</td>
                                    <td class="w-75">: {{ $pembayaran->jenisPembayaran?->nama }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Periode</td>
                                    <td>:
                                        @if ($pembayaran->tagihan?->periode_bulan)
                                            {{ bulanIndonesia($pembayaran->tagihan->periode_bulan) }} {{ $pembayaran->tagihan->tahunAjaran?->nama }}
                                        @else
                                            Sekali bayar
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Metode</td>
                                    <td>: {{ $pembayaran->metode === 'tunai' ? 'Tunai' : 'Transfer' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="border rounded-3 p-3 bg-light mb-2 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Telah diterima sebesar</span>
                        <span class="fs-3 fw-bold text-primary">{{ formatRupiah($pembayaran->nominal) }}</span>
                    </div>
                    <p class="small text-muted fst-italic mb-3">{{ terbilang($pembayaran->nominal) }} rupiah</p>

                    <div class="row mt-4">
                        <div class="col-4">
                            <small class="text-muted">Petugas,</small>
                            <div class="mt-5 pt-1 border-top small">
                                <strong>{{ $pembayaran->user?->name ?? '-' }}</strong>
                            </div>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4 text-center">
                            <small class="text-muted">Wali Santri,</small>
                            <div class="mt-5 pt-1 border-top small">
                                <strong>{{ $pembayaran->santri?->nama_wali ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-4 text-center">
                    <a href="{{ route('pembayaran.unduh', $pembayaran) }}" class="btn btn-primary-grad">
                        <i class="fas fa-file-pdf me-1"></i>Unduh PDF
                    </a>
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
