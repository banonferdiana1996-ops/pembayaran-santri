@extends('layouts.app')

@section('title', 'Pembayaran')

@section('page-title', 'Pembayaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Pembayaran</li>
@endsection

@section('content')
    <div class="card card-soft border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-money-bill-transfer text-primary me-2"></i>Riwayat Pembayaran</h5>
            <a href="{{ route('pembayaran.create') }}" class="btn btn-primary-grad">
                <i class="fas fa-plus me-1"></i>Buat Pembayaran
            </a>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pembayaran.index') }}" class="row g-2 align-items-end mb-3">
                <div class="col-md-3">
                    <label class="form-label small">Santri</label>
                    <select class="form-select form-select-sm select2" name="santri_id">
                        <option value="">-- Semua --</option>
                        @foreach ($santris as $santri)
                            <option value="{{ $santri->id }}" @selected($selectedSantri === $santri->id)>{{ $santri->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Jenis Pembayaran</label>
                    <select class="form-select form-select-sm select2" name="jenis_pembayaran_id">
                        <option value="">-- Semua --</option>
                        @foreach ($jenisPembayarans as $jenis)
                            <option value="{{ $jenis->id }}" @selected($selectedJenis === $jenis->id)>{{ $jenis->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Dari</label>
                    <input type="date" class="form-control form-control-sm" name="dari" value="{{ $dari }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Sampai</label>
                    <input type="date" class="form-control form-control-sm" name="sampai" value="{{ $sampai }}">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill"><i class="fas fa-filter me-1"></i>Filter</button>
                    <a href="{{ route('pembayaran.index') }}" class="btn btn-sm btn-light"><i class="fas fa-undo"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Santri</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th class="text-end">Nominal</th>
                            <th>Petugas</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembayarans as $pembayaran)
                            <tr>
                                <td class="text-muted small">{{ $pembayaran->nomor }}</td>
                                <td class="fw-semibold">{{ $pembayaran->santri?->nama_lengkap }}</td>
                                <td>{{ $pembayaran->jenisPembayaran?->nama }}</td>
                                <td class="small">{{ $pembayaran->tanggal_bayar?->translatedFormat('d M Y') }}</td>
                                <td>
                                    @if ($pembayaran->metode === 'tunai')
                                        <span class="badge badge-soft-success">Tunai</span>
                                    @else
                                        <span class="badge badge-soft-info">Transfer</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">{{ formatRupiah($pembayaran->nominal) }}</td>
                                <td class="small text-muted">{{ $pembayaran->user?->name }}</td>
                                <td class="text-end">
                                    <a href="{{ route('pembayaran.kwitansi', $pembayaran) }}" class="btn btn-sm btn-info rounded-3" title="Lihat Kwitansi">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                    <a href="{{ route('pembayaran.unduh', $pembayaran) }}" class="btn btn-sm btn-secondary rounded-3" title="Unduh PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <form id="delete-{{ $pembayaran->id }}" action="{{ route('pembayaran.destroy', $pembayaran) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded-3" onclick="confirmDelete('delete-{{ $pembayaran->id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function () {
        $('#datatable').DataTable({
            responsive: true,
            autoWidth: false,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
            pageLength: 10,
            order: [[0, 'desc']]
        });
        });
</script>
@endpush
