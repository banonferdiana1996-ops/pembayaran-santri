@extends('layouts.app')

@section('title', 'Jenis Pembayaran')

@section('page-title', 'Jenis Pembayaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Jenis Pembayaran</li>
@endsection

@section('content')
    <div class="card card-soft border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-tags text-primary me-2"></i>Data Jenis Pembayaran</h5>
            <button class="btn btn-primary-grad" onclick="openCreate()">
                <i class="fas fa-plus me-1"></i>Tambah
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center">Tipe</th>
                            <th>Keterangan</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jenisPembayarans as $jenis)
                            @php
                                $edit = [
                                    'id' => $jenis->id,
                                    'kode' => $jenis->kode,
                                    'nama' => $jenis->nama,
                                    'nominal' => $jenis->nominal,
                                    'is_bulanan' => $jenis->is_bulanan,
                                    'is_active' => $jenis->is_active,
                                    'keterangan' => $jenis->keterangan,
                                ];
                            @endphp
                            <tr>
                                <td><span class="badge bg-primary-subtle text-primary fw-semibold">{{ $jenis->kode }}</span></td>
                                <td class="fw-semibold">{{ $jenis->nama }}</td>
                                <td class="text-end">{{ formatRupiah($jenis->nominal) }}</td>
                                <td class="text-center">
                                    @if ($jenis->is_bulanan)
                                        <span class="badge badge-soft-info">Bulanan</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Sekali bayar</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $jenis->keterangan ?: '-' }}</td>
                                <td class="text-center">
                                    @if ($jenis->is_active)
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @else
                                        <span class="badge badge-soft-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-info rounded-3" data-edit='@json($edit)' onclick="openEdit(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form id="delete-{{ $jenis->id }}" action="{{ route('jenis-pembayaran.destroy', $jenis) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded-3" onclick="confirmDelete('delete-{{ $jenis->id }}')">
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
                <form id="formJenis" data-ajax>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Jenis Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="kode" class="form-label">Kode</label>
                                <input type="text" class="form-control text-uppercase" id="kode" name="kode" placeholder="cth: SPP" maxlength="20" required>
                            </div>
                            <div class="col-md-8">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nominal" class="form-label">Nominal (Rp)</label>
                                <input type="number" class="form-control" id="nominal" name="nominal" min="0" step="500" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">&nbsp;</label>
                                <div class="form-check form-switch d-inline-block me-3">
                                    <input class="form-check-input" type="checkbox" id="is_bulanan" name="is_bulanan" value="1">
                                    <label class="form-check-label" for="is_bulanan">Bulanan</label>
                                </div>
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                </div>
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
        $('#datatable').DataTable({
            responsive: true,
            autoWidth: false,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
            pageLength: 10
        });

        const storeUrl = '{{ route('jenis-pembayaran.store') }}';

        function openCreate() {
            $('#formJenis')[0].reset();
            $('#formJenis input[name="_method"]').remove();
            $('#formJenis').attr('action', storeUrl).attr('method', 'POST');
            $('#is_active').prop('checked', true);
            $('#modalTitle').text('Tambah Jenis Pembayaran');
            $('#modalForm').modal('show');
        }

        function openEdit(btn) {
            const d = $(btn).data('edit');
            $('#formJenis')[0].reset();
            $('#formJenis input[name="_method"]').remove();
            $('#formJenis').attr('action', '/jenis-pembayaran/' + d.id).attr('method', 'POST');
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#formJenis');
            $('#kode').val(d.kode);
            $('#nama').val(d.nama);
            $('#nominal').val(d.nominal);
            $('#is_bulanan').prop('checked', !!d.is_bulanan);
            $('#is_active').prop('checked', !!d.is_active);
            $('#keterangan').val(d.keterangan || '');
            $('#modalTitle').text('Ubah Jenis Pembayaran');
            $('#modalForm').modal('show');
        }
    </script>
@endpush
