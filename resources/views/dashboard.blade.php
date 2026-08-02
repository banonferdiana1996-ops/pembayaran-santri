@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    @if ($role === 'keuangan')
        {{-- ===== Kartu Statistik Admin / Bendahara ===== --}}
        <div class="row g-3">
            <div class="col-6 col-xl-3">
                <div class="card card-soft border-0 h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-4 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                            <i class="fas fa-user-graduate fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Santri</div>
                            <div class="fw-bold fs-4">{{ $totalSantri }}</div>
                            <div class="small text-success"><i class="fas fa-circle me-1"></i>{{ $santriAktif }} aktif</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card card-soft border-0 h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-4 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                            <i class="fas fa-file-invoice fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Tagihan Belum Lunas</div>
                            <div class="fw-bold fs-4">{{ $tagihanBelumLunas }}</div>
                            <div class="small text-warning">{{ formatRupiah($tagihanNominal) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card card-soft border-0 h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-4 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                            <i class="fas fa-money-bill-trend-up fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Pemasukan Kas</div>
                            <div class="fw-bold fs-4">{{ formatRupiah($pemasukanKas) }}</div>
                            <div class="small text-success">{{ formatRupiah($totalPembayaran) }} dari pembayaran</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card card-soft border-0 h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-4 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                            <i class="fas fa-scale-balanced fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Saldo Kas</div>
                            <div class="fw-bold fs-4 {{ $saldo < 0 ? 'text-danger' : 'text-success' }}">{{ formatRupiah($saldo) }}</div>
                            <div class="small text-danger">{{ formatRupiah($pengeluaran) }} pengeluaran</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Grafik + Pembayaran Terbaru ===== --}}
        <div class="row g-3 mt-1">
            <div class="col-lg-7">
                <div class="card card-soft border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-chart-area text-primary me-2"></i>Arus Kas 6 Bulan Terakhir</h5>
                    </div>
                    <div class="card-body">
                        <div id="cashflowChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card card-soft border-0">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>Pembayaran Terbaru</h5>
                        @if (Route::has('pembayaran.index'))
                            <a href="{{ route('pembayaran.index') }}" class="btn btn-sm btn-light rounded-3">Lihat semua</a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse ($pembayaranTerbaru as $pembayaran)
                                <div class="list-group-item d-flex align-items-center gap-3 py-3">
                                    <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="fas fa-circle-check"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold text-truncate">{{ $pembayaran->santri?->nama_lengkap ?? '-' }}</div>
                                        <div class="small text-muted">
                                            {{ $pembayaran->jenisPembayaran?->nama }} &middot; {{ $pembayaran->tanggal_bayar->translatedFormat('d M Y') }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">{{ formatRupiah($pembayaran->nominal) }}</div>
                                        <div class="small text-muted">{{ $pembayaran->metode }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">Belum ada pembayaran.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($role === 'santri')
        {{-- ===== Dashboard Santri ===== --}}
        <div class="row g-3">
            <div class="col-6 col-xl-3">
                <div class="card card-soft border-0 h-100">
                    <div class="card-body text-center">
                        <div class="text-muted small">Total Tagihan</div>
                        <div class="fw-bold fs-4">{{ $tagihans->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card card-soft border-0 h-100">
                    <div class="card-body text-center">
                        <div class="text-muted small">Belum Lunas</div>
                        <div class="fw-bold fs-4 text-danger">{{ $tagihans->where('status', '!=', 'lunas')->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card card-soft border-0 h-100">
                    <div class="card-body text-center">
                        <div class="text-muted small">Total Dibayar</div>
                        <div class="fw-bold fs-4 text-success">{{ formatRupiah($pembayarans->sum('nominal')) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card card-soft border-0 h-100">
                    <div class="card-body text-center">
                        <div class="text-muted small">Sisa Tagihan</div>
                        <div class="fw-bold fs-4 text-warning">{{ formatRupiah($tagihans->sum(fn ($t) => $t->sisa)) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-soft border-0 mt-3">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-file-invoice text-primary me-2"></i>Daftar Tagihan Saya</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No.</th>
                                <th>Jenis Pembayaran</th>
                                <th>Periode</th>
                                <th class="text-end">Nominal</th>
                                <th class="text-end">Dibayar</th>
                                <th class="text-end">Sisa</th>
                                <th class="pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tagihans as $tagihan)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $tagihan->nomor }}</td>
                                    <td>{{ $tagihan->jenisPembayaran?->nama }}</td>
                                    <td>
                                        @if ($tagihan->periode_bulan)
                                            {{ bulanIndonesia($tagihan->periode_bulan) }} {{ $tagihan->tahunAjaran?->nama }}
                                        @else
                                            <span class="text-muted">Sekali bayar</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ formatRupiah($tagihan->nominal) }}</td>
                                    <td class="text-end text-success">{{ formatRupiah($tagihan->total_dibayar) }}</td>
                                    <td class="text-end {{ $tagihan->sisa > 0 ? 'text-danger' : '' }}">{{ formatRupiah($tagihan->sisa) }}</td>
                                    <td class="pe-4">
                                        @if ($tagihan->status === 'lunas')
                                            <span class="badge badge-soft-success">Lunas</span>
                                        @elseif ($tagihan->status === 'dibatalkan')
                                            <span class="badge badge-soft-warning">Dibatalkan</span>
                                        @else
                                            <span class="badge badge-soft-danger">Belum Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada tagihan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif ($role === 'wali')
        {{-- ===== Dashboard Wali ===== --}}
        <div class="row g-3">
            @forelse ($anakAsuh as $santri)
                <div class="col-md-6 col-xl-4">
                    <div class="card card-soft border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                    <i class="fas fa-user-graduate fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $santri->nama_lengkap }}</div>
                                    <div class="small text-muted">{{ $santri->kelas?->nama ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="row text-center g-2">
                                <div class="col-4">
                                    <div class="rounded-3 bg-light py-2">
                                        <div class="small text-muted">Tagihan</div>
                                        <div class="fw-bold">{{ $santri->tagihans_count ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="rounded-3 bg-light py-2">
                                        <div class="small text-muted">Belum Lunas</div>
                                        <div class="fw-bold text-danger">{{ $santri->tagihan_belum_lunas }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="rounded-3 bg-light py-2">
                                        <div class="small text-muted">Sisa</div>
                                        <div class="fw-bold text-warning">{{ formatRupiah($santri->total_sisa) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card card-soft border-0">
                        <div class="card-body text-center text-muted py-5">
                            <i class="fas fa-users-slash fs-1 mb-3 d-block"></i>
                            Belum ada santri yang diasuh.
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    @endif
@endsection

@push('scripts')
    @if ($role === 'keuangan')
        <script defer src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.1/dist/apexcharts.min.js"></script>
        <script>
            window.addEventListener('DOMContentLoaded', function () {
            const labels = @json($chartLabels);
            const pemasukan = @json($chartPemasukan);
            const pengeluaran = @json($chartPengeluaran);

            const options = {
                series: [
                    { name: 'Pemasukan', data: pemasukan },
                    { name: 'Pengeluaran', data: pengeluaran }
                ],
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    foreColor: '#64748b'
                },
                colors: ['#10b981', '#ef4444'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02 } },
                grid: { borderColor: '#e8eef8' },
                xaxis: { categories: labels },
                yaxis: {
                    labels: {
                        formatter: (val) => 'Rp ' + Number(val).toLocaleString('id-ID')
                    }
                },
                tooltip: {
                    y: { formatter: (val) => 'Rp ' + Number(val).toLocaleString('id-ID') }
                }
            };

            new ApexCharts(document.querySelector('#cashflowChart'), options).render();
            });
        </script>
    @endif
@endpush
