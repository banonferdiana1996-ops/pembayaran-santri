@extends('layouts.app')

@section('title', 'Laporan')

@section('page-title', 'Laporan Keuangan & Pembayaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Laporan</li>
@endsection

@section('content')
    <div class="card card-soft border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('report.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Jenis Laporan</label>
                    <select class="form-select form-select-sm" name="jenis">
                        <option value="keuangan" @selected($jenis === 'keuangan')>Laporan Keuangan (Arus Kas)</option>
                        <option value="pembayaran" @selected($jenis === 'pembayaran')>Laporan Rekap Pembayaran</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Dari</label>
                    <input type="date" class="form-control form-control-sm" name="dari" value="{{ $dari }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Sampai</label>
                    <input type="date" class="form-control form-control-sm" name="sampai" value="{{ $sampai }}">
                </div>
                <div class="col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill"><i class="fas fa-filter me-1"></i>Tampilkan</button>
                </div>
            </form>
            <hr>

            @if ($jenis === 'keuangan')
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="text-muted small">Total Pemasukan</div>
                                <div class="fs-4 fw-bold text-success">{{ formatRupiah($totalMasuk) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="text-muted small">Total Pengeluaran</div>
                                <div class="fs-4 fw-bold text-danger">{{ formatRupiah($totalKeluar) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="text-muted small">Selisih (Saldo)</div>
                                <div class="fs-4 fw-bold {{ $selisih >= 0 ? 'text-success' : 'text-danger' }}">{{ formatRupiah($selisih) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="datatable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th class="text-end">Pemasukan</th>
                                <th class="text-end">Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr>
                                    <td class="small">{{ $row['tanggal'] }}</td>
                                    <td class="small">{{ $row['keterangan'] }}</td>
                                    <td class="text-end text-success">{{ $row['pemasukan'] ? formatRupiah($row['pemasukan']) : '-' }}</td>
                                    <td class="text-end text-danger">{{ $row['pengeluaran'] ? formatRupiah($row['pengeluaran']) : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="2" class="text-end">TOTAL</td>
                                <td class="text-end">{{ formatRupiah($totalMasuk) }}</td>
                                <td class="text-end">{{ formatRupiah($totalKeluar) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="text-muted small">Santri Melakukan Pembayaran</div>
                                <div class="fs-4 fw-bold text-primary">{{ $totalSantri }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="text-muted small">Total Dibayar</div>
                                <div class="fs-4 fw-bold text-success">{{ formatRupiah($totalDibayar) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="datatable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Santri</th>
                                <th>Kelas</th>
                                <th class="text-center">Jumlah Transaksi</th>
                                <th class="text-end">Total Dibayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-semibold">{{ $row['nama'] }}</td>
                                    <td>{{ $row['kelas'] }}</td>
                                    <td class="text-center">{{ $row['jumlah_transaksi'] }}</td>
                                    <td class="text-end text-success">{{ formatRupiah($row['total']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="d-flex gap-2 mt-3 justify-content-end">
                <a href="{{ route('report.pdf', ['jenis' => $jenis, 'dari' => $dari, 'sampai' => $sampai]) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-1"></i>Unduh PDF
                </a>
                <a href="{{ route('report.excel', ['jenis' => $jenis, 'dari' => $dari, 'sampai' => $sampai]) }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i>Unduh Excel
                </a>
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
            pageLength: 25
        });
    </script>
@endpush
