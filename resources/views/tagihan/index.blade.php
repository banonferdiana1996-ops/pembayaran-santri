@extends('layouts.app')

@section('title', 'Tagihan')

@section('page-title', 'Tagihan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Tagihan</li>
@endsection

@section('content')
    <div class="card card-soft border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-file-invoice text-primary me-2"></i>Data Tagihan</h5>
            <button class="btn btn-primary-grad" onclick="openGenerate()">
                <i class="fas fa-wand-magic-sparkles me-1"></i>Generate Tagihan
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('tagihan.index') }}" class="row g-2 align-items-end mb-3">
                <div class="col-md-3">
                    <label class="form-label small">Jenis Pembayaran</label>
                    <select class="form-select form-select-sm select2" name="jenis_pembayaran_id" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        @foreach ($jenisPembayarans as $jenis)
                            <option value="{{ $jenis->id }}" @selected($selectedJenis === $jenis->id)>{{ $jenis->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Periode Bulan</label>
                    <select class="form-select form-select-sm" name="periode_bulan" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        @foreach (range(1, 12) as $bulan)
                            <option value="{{ $bulan }}" @selected($selectedBulan === $bulan)>{{ bulanIndonesia($bulan) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        <option value="belum_lunas" @selected($status === 'belum_lunas')>Belum Lunas</option>
                        <option value="lunas" @selected($status === 'lunas')>Lunas</option>
                        <option value="dibatalkan" @selected($status === 'dibatalkan')>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3 d-grid">
                    <a href="{{ route('tagihan.index') }}" class="btn btn-light">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Santri</th>
                            <th>Jenis</th>
                            <th>Periode</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-end">Dibayar</th>
                            <th class="text-end">Sisa</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tagihans as $tagihan)
                            <tr>
                                <td class="text-muted small">{{ $tagihan->nomor }}</td>
                                <td class="fw-semibold">{{ $tagihan->santri?->nama_lengkap }}</td>
                                <td>{{ $tagihan->jenisPembayaran?->nama }}</td>
                                <td class="small">
                                    @if ($tagihan->periode_bulan)
                                        {{ bulanIndonesia($tagihan->periode_bulan) }} {{ $tagihan->tahunAjaran?->nama }}
                                    @else
                                        <span class="text-muted">Sekali bayar</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ formatRupiah($tagihan->nominal) }}</td>
                                <td class="text-end text-success">{{ formatRupiah($tagihan->total_dibayar) }}</td>
                                <td class="text-end {{ $tagihan->sisa > 0 ? 'text-danger' : '' }}">{{ formatRupiah($tagihan->sisa) }}</td>
                                <td>
                                    @if ($tagihan->status === 'lunas')
                                        <span class="badge badge-soft-success">Lunas</span>
                                    @elseif ($tagihan->status === 'dibatalkan')
                                        <span class="badge badge-soft-warning">Dibatalkan</span>
                                    @else
                                        <span class="badge badge-soft-danger">Belum Lunas</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form id="delete-{{ $tagihan->id }}" action="{{ route('tagihan.destroy', $tagihan) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded-3" onclick="confirmDelete('delete-{{ $tagihan->id }}')">
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

    <div class="modal fade" id="modalGenerate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formGenerate" data-ajax>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-wand-magic-sparkles me-2 text-primary"></i>Generate Tagihan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="jenis_pembayaran_id" class="form-label">Jenis Pembayaran</label>
                            <select class="form-select select2" id="jenis_pembayaran_id" name="jenis_pembayaran_id" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach ($jenisPembayarans as $jenis)
                                    <option value="{{ $jenis->id }}" data-bulanan="{{ $jenis->is_bulanan ? 1 : 0 }}">{{ $jenis->nama }} ({{ formatRupiah($jenis->nominal) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                            <select class="form-select select2" id="tahun_ajaran_id" name="tahun_ajaran_id" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahunAjarans as $tahunAjaran)
                                    <option value="{{ $tahunAjaran->id }}" @selected($tahunAjaran->is_active)>{{ $tahunAjaran->nama }}@if ($tahunAjaran->is_active) (Aktif)@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="bulanWrap">
                            <label for="periode_bulan" class="form-label">Periode Bulan</label>
                            <select class="form-select" id="periode_bulan" name="periode_bulan">
                                <option value="">-- Pilih Bulan --</option>
                                @foreach (range(1, 12) as $bulan)
                                    <option value="{{ $bulan }}">{{ bulanIndonesia($bulan) }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Untuk pembayaran bulanan.</div>
                        </div>
                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Kelas (opsional)</label>
                            <select class="form-select select2" id="kelas_id" name="kelas_id">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($kelas as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nominal" class="form-label">Nominal (opsional)</label>
                                <input type="number" class="form-control" id="nominal" name="nominal" min="0">
                                <div class="form-text">Kosongkan = nominal default jenis.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_jatuh_tempo" class="form-label">Jatuh Tempo (opsional)</label>
                                <input type="date" class="form-control" id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-grad"><i class="fas fa-wand-magic-sparkles me-1"></i>Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#datatable').DataTable({
            responsive: true,
            autoWidth: false,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
            pageLength: 10
        });

        function openGenerate() {
            $('#formGenerate')[0].reset();
            $('#formGenerate').attr('action', '{{ route('tagihan.generate') }}').attr('method', 'POST');
            $('#modalGenerate').modal('show');
        }

        $('#jenis_pembayaran_id').on('change', function () {
            const isBulanan = $(this).find(':selected').data('bulanan');
            $('#bulanWrap').toggle(!!isBulanan);
            if (!isBulanan) $('#periode_bulan').val('');
        });
    </script>
@endpush
