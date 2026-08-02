@extends('layouts.app')

@section('title', 'Pengumuman')

@section('page-title', 'Pengumuman')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Pengumuman</li>
@endsection

@section('content')
    <div class="card card-soft border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-bullhorn text-primary me-2"></i>Data Pengumuman</h5>
            <button class="btn btn-primary-grad" onclick="openCreate()">
                <i class="fas fa-plus me-1"></i>Tambah
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Judul</th>
                            <th>Isi</th>
                            <th>Scope</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($announcements as $announcement)
                            @php
                                $edit = [
                                    'id' => $announcement->id,
                                    'judul' => $announcement->judul,
                                    'isi' => $announcement->isi,
                                    'tanggal' => $announcement->tanggal->format('Y-m-d'),
                                    'scope' => $announcement->scope,
                                    'is_active' => $announcement->is_active,
                                ];
                            @endphp
                            <tr>
                                <td class="small">{{ $announcement->tanggal->translatedFormat('d M Y') }}</td>
                                <td class="fw-semibold">{{ $announcement->judul }}</td>
                                <td class="text-muted small">
                                    <span class="d-inline-block text-truncate" style="max-width: 320px;">{{ $announcement->isi }}</span>
                                </td>
                                <td>
                                    @php
                                        $badge = ['landing' => 'badge-soft-info', 'dashboard' => 'badge-soft-warning', 'semua' => 'badge-soft-success'];
                                    @endphp
                                    <span class="badge {{ $badge[$announcement->scope] ?? 'bg-secondary text-white' }}">{{ ucfirst($announcement->scope) }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($announcement->is_active)
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @else
                                        <span class="badge badge-soft-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-info rounded-3" data-edit='@json($edit)' onclick="openEdit(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form id="delete-{{ $announcement->id }}" action="{{ route('announcement.destroy', $announcement) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded-3" onclick="confirmDelete('delete-{{ $announcement->id }}')">
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formAnnouncement" data-ajax>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Pengumuman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="judul" class="form-label">Judul</label>
                                <input type="text" class="form-control" id="judul" name="judul" maxlength="100" required>
                            </div>
                            <div class="col-md-4">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="scope" class="form-label">Scope</label>
                                <select class="form-select" id="scope" name="scope" required>
                                    <option value="semua">Semua (Landing & Dashboard)</option>
                                    <option value="dashboard">Dashboard Saja</option>
                                    <option value="landing">Landing Saja</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">&nbsp;</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="isi" class="form-label">Isi Pengumuman</label>
                                <textarea class="form-control" id="isi" name="isi" rows="4" required></textarea>
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

        const storeUrl = '{{ route('announcement.store') }}';

        function openCreate() {
            $('#formAnnouncement')[0].reset();
            $('#formAnnouncement input[name="_method"]').remove();
            $('#formAnnouncement').attr('action', storeUrl).attr('method', 'POST');
            $('#tanggal').val('{{ now()->format('Y-m-d') }}');
            $('#is_active').prop('checked', true);
            $('#modalTitle').text('Tambah Pengumuman');
            $('#modalForm').modal('show');
        }

        function openEdit(btn) {
            const d = $(btn).data('edit');
            $('#formAnnouncement')[0].reset();
            $('#formAnnouncement input[name="_method"]').remove();
            $('#formAnnouncement').attr('action', '/pengumuman/' + d.id).attr('method', 'POST');
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#formAnnouncement');
            $('#judul').val(d.judul);
            $('#tanggal').val(d.tanggal);
            $('#scope').val(d.scope);
            $('#is_active').prop('checked', !!d.is_active);
            $('#isi').val(d.isi);
            $('#modalTitle').text('Ubah Pengumuman');
            $('#modalForm').modal('show');
        }
        window.openCreate = openCreate;
        window.openEdit = openEdit;
        });
</script>
@endpush
