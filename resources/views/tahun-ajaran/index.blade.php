@extends('layouts.app')

@section('title', 'Tahun Ajaran')

@section('page-title', 'Tahun Ajaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Tahun Ajaran</li>
@endsection

@section('content')
    <div class="card card-soft border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-calendar-alt text-primary me-2"></i>Data Tahun Ajaran</h5>
            <button class="btn btn-primary-grad" onclick="openCreate()">
                <i class="fas fa-plus me-1"></i>Tambah
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tahunAjarans as $tahunAjaran)
                            @php
                                $edit = [
                                    'id' => $tahunAjaran->id,
                                    'nama' => $tahunAjaran->nama,
                                    'tanggal_mulai' => $tahunAjaran->tanggal_mulai->format('Y-m-d'),
                                    'tanggal_selesai' => $tahunAjaran->tanggal_selesai->format('Y-m-d'),
                                    'is_active' => $tahunAjaran->is_active,
                                ];
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $tahunAjaran->nama }}</td>
                                <td>{{ $tahunAjaran->tanggal_mulai->translatedFormat('d M Y') }}</td>
                                <td>{{ $tahunAjaran->tanggal_selesai->translatedFormat('d M Y') }}</td>
                                <td>
                                    @if ($tahunAjaran->is_active)
                                        <span class="badge badge-soft-success"><i class="fas fa-circle me-1"></i>Aktif</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-info rounded-3" data-edit='@json($edit)' onclick="openEdit(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form id="delete-{{ $tahunAjaran->id }}" action="{{ route('tahun-ajaran.destroy', $tahunAjaran) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded-3" onclick="confirmDelete('delete-{{ $tahunAjaran->id }}')">
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
                <form id="formTahunAjaran" data-ajax>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Tahun Ajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Tahun Ajaran</label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="cth: 2025/2026" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                            </div>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1">
                            <label class="form-check-label" for="is_active">Jadikan tahun ajaran aktif</label>
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

        const storeUrl = '{{ route('tahun-ajaran.store') }}';

        function openCreate() {
            $('#formTahunAjaran')[0].reset();
            $('#formTahunAjaran input[name="_method"]').remove();
            $('#formTahunAjaran').attr('action', storeUrl).attr('method', 'POST');
            $('#modalTitle').text('Tambah Tahun Ajaran');
            $('#modalForm').modal('show');
        }

        function openEdit(btn) {
            const d = $(btn).data('edit');
            $('#formTahunAjaran')[0].reset();
            $('#formTahunAjaran input[name="_method"]').remove();
            $('#formTahunAjaran').attr('action', '/tahun-ajaran/' + d.id).attr('method', 'POST');
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#formTahunAjaran');
            $('#nama').val(d.nama);
            $('#tanggal_mulai').val(d.tanggal_mulai);
            $('#tanggal_selesai').val(d.tanggal_selesai);
            $('#is_active').prop('checked', !!d.is_active);
            $('#modalTitle').text('Ubah Tahun Ajaran');
            $('#modalForm').modal('show');
        }
    </script>
@endpush
