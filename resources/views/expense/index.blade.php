@extends('layouts.app')

@section('title', 'Pengeluaran')

@section('page-title', 'Pengeluaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Pengeluaran</li>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-4 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;"><i class="fas fa-circle-dollar-to-slot"></i></div>
                    <div>
                        <div class="text-muted small">Total Pengeluaran (filter)</div>
                        <div class="fs-4 fw-bold">{{ formatRupiah($total) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-soft border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-arrow-up text-danger me-2"></i>Data Pengeluaran</h5>
            <button class="btn btn-primary-grad" onclick="openCreate()">
                <i class="fas fa-plus me-1"></i>Tambah
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('expense.index') }}" class="row g-2 align-items-end mb-3">
                <div class="col-md-3">
                    <label class="form-label small">Kategori</label>
                    <select class="form-select form-select-sm select2" name="kategori" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        @foreach (['operasional', 'sarana', 'gaji', 'lainnya'] as $k)
                            <option value="{{ $k }}" @selected($kategori === $k)>{{ ucfirst($k) }}</option>
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
                    <a href="{{ route('expense.index') }}" class="btn btn-sm btn-light"><i class="fas fa-undo"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-end">Jumlah</th>
                            <th>Petugas</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $expense)
                            @php
                                $edit = [
                                    'id' => $expense->id,
                                    'nama' => $expense->nama,
                                    'jumlah' => $expense->jumlah,
                                    'tanggal' => $expense->tanggal->format('Y-m-d'),
                                    'kategori' => $expense->kategori,
                                    'deskripsi' => $expense->deskripsi,
                                ];
                            @endphp
                            <tr>
                                <td class="small">{{ $expense->tanggal->translatedFormat('d M Y') }}</td>
                                <td class="fw-semibold">{{ $expense->nama }}</td>
                                <td>
                                    @php
                                        $badge = ['operasional' => 'badge-soft-info', 'sarana' => 'badge-soft-warning', 'gaji' => 'badge-soft-success', 'lainnya' => 'badge-soft-danger'];
                                    @endphp
                                    <span class="badge {{ $badge[$expense->kategori] ?? 'bg-secondary text-white' }}">{{ ucfirst($expense->kategori) }}</span>
                                </td>
                                <td class="text-muted small">{{ $expense->deskripsi ?: '-' }}</td>
                                <td class="text-end fw-semibold text-danger">{{ formatRupiah($expense->jumlah) }}</td>
                                <td class="small text-muted">{{ $expense->user?->name }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-info rounded-3" data-edit='@json($edit)' onclick="openEdit(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form id="delete-{{ $expense->id }}" action="{{ route('expense.destroy', $expense) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded-3" onclick="confirmDelete('delete-{{ $expense->id }}')">
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
                <form id="formExpense" data-ajax>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Pengeluaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" maxlength="100" required>
                            </div>
                            <div class="col-md-6">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="operasional">Operasional</option>
                                    <option value="sarana">Sarana</option>
                                    <option value="gaji">Gaji</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2"></textarea>
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

        const storeUrl = '{{ route('expense.store') }}';

        function openCreate() {
            $('#formExpense')[0].reset();
            $('#formExpense input[name="_method"]').remove();
            $('#formExpense').attr('action', storeUrl).attr('method', 'POST');
            $('#tanggal').val('{{ now()->format('Y-m-d') }}');
            $('#modalTitle').text('Tambah Pengeluaran');
            $('#modalForm').modal('show');
        }

        function openEdit(btn) {
            const d = $(btn).data('edit');
            $('#formExpense')[0].reset();
            $('#formExpense input[name="_method"]').remove();
            $('#formExpense').attr('action', '/expense/' + d.id).attr('method', 'POST');
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#formExpense');
            $('#nama').val(d.nama);
            $('#kategori').val(d.kategori);
            $('#jumlah').val(d.jumlah);
            $('#tanggal').val(d.tanggal);
            $('#deskripsi').val(d.deskripsi || '');
            $('#modalTitle').text('Ubah Pengeluaran');
            $('#modalForm').modal('show');
        }
        window.openCreate = openCreate;
        window.openEdit = openEdit;
        });
</script>
@endpush
