@extends('layouts.app')

@section('title', 'Pemasukan')

@section('page-title', 'Pemasukan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Pemasukan</li>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-4 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;"><i class="fas fa-circle-dollar-to-slot"></i></div>
                    <div>
                        <div class="text-muted small">Total Pemasukan (filter)</div>
                        <div class="fs-4 fw-bold">{{ formatRupiah($total) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-soft border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-arrow-down text-success me-2"></i>Data Pemasukan</h5>
            <button class="btn btn-primary-grad" onclick="openCreate()">
                <i class="fas fa-plus me-1"></i>Tambah
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('income.index') }}" class="row g-2 align-items-end mb-3">
                <div class="col-md-3">
                    <label class="form-label small">Sumber</label>
                    <select class="form-select form-select-sm select2" name="sumber" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        @foreach (['donasi', 'infaq', 'lainnya'] as $s)
                            <option value="{{ $s }}" @selected($sumber === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
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
                    <button type="submit" class="btn btn-sm btn-primary flex-fill"><i class="fas fa-filter me-1"></i>Filter</button>
                    <a href="{{ route('income.index') }}" class="btn btn-sm btn-light"><i class="fas fa-undo"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Sumber</th>
                            <th>Keterangan</th>
                            <th class="text-end">Jumlah</th>
                            <th>Petugas</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($incomes as $income)
                            @php
                                $edit = [
                                    'id' => $income->id,
                                    'sumber' => $income->sumber,
                                    'jumlah' => $income->jumlah,
                                    'tanggal' => $income->tanggal->format('Y-m-d'),
                                    'keterangan' => $income->keterangan,
                                ];
                            @endphp
                            <tr>
                                <td class="small">{{ $income->tanggal->translatedFormat('d M Y') }}</td>
                                <td>
                                    @php
                                        $badge = ['donasi' => 'badge-soft-info', 'infaq' => 'badge-soft-success', 'lainnya' => 'badge-soft-warning'];
                                    @endphp
                                    <span class="badge {{ $badge[$income->sumber] ?? 'bg-secondary text-white' }}">{{ ucfirst($income->sumber) }}</span>
                                </td>
                                <td class="text-muted small">{{ $income->keterangan ?: '-' }}</td>
                                <td class="text-end fw-semibold text-success">{{ formatRupiah($income->jumlah) }}</td>
                                <td class="small text-muted">{{ $income->user?->name }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-info rounded-3" data-edit='@json($edit)' onclick="openEdit(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form id="delete-{{ $income->id }}" action="{{ route('income.destroy', $income) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded-3" onclick="confirmDelete('delete-{{ $income->id }}')">
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

    <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formIncome" data-ajax>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Pemasukan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="sumber" class="form-label">Sumber</label>
                                <select class="form-select" id="sumber" name="sumber" required>
                                    <option value="donasi">Donasi</option>
                                    <option value="infaq">Infaq</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                            </div>
                            <div class="col-12">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-grad"><i class="fas fa-save me-1"></i>Simpan</button>
                    </div>
                </form>
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

        const storeUrl = '{{ route('income.store') }}';

        function openCreate() {
            $('#formIncome')[0].reset();
            $('#formIncome input[name="_method"]').remove();
            $('#formIncome').attr('action', storeUrl).attr('method', 'POST');
            $('#tanggal').val('{{ now()->format('Y-m-d') }}');
            $('#modalTitle').text('Tambah Pemasukan');
            $('#modalForm').modal('show');
        }

        function openEdit(btn) {
            const d = $(btn).data('edit');
            $('#formIncome')[0].reset();
            $('#formIncome input[name="_method"]').remove();
            $('#formIncome').attr('action', '/income/' + d.id).attr('method', 'POST');
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#formIncome');
            $('#sumber').val(d.sumber);
            $('#tanggal').val(d.tanggal);
            $('#jumlah').val(d.jumlah);
            $('#keterangan').val(d.keterangan || '');
            $('#modalTitle').text('Ubah Pemasukan');
            $('#modalForm').modal('show');
        }
        window.openCreate = openCreate;
        window.openEdit = openEdit;
        });
</script>
@endpush
