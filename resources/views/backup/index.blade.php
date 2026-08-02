@extends('layouts.app')

@section('title', 'Backup')

@section('page-title', 'Backup Database')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Backup</li>
@endsection

@section('content')
    <div class="card card-soft border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-database text-primary me-2"></i>Data Backup</h5>
            <form id="formBackup" data-ajax action="{{ route('backup.store') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary-grad" id="btnBackup">
                    <i class="fas fa-database me-1"></i>Buat Backup Sekarang
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="alert alert-info small">
                <i class="fas fa-circle-info me-1"></i>
                Backup dibuat menggunakan <code>mysqldump</code> dan disimpan di <code>storage/app/backups</code>.
                Unduh secara berkala dan simpan di lokasi aman.
            </div>

            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th class="text-end">Ukuran</th>
                            <th>Dibuat Oleh</th>
                            <th>Waktu</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($backups as $backup)
                            <tr>
                                <td class="fw-semibold small"><i class="fas fa-file-code text-muted me-1"></i>{{ $backup->nama_file }}</td>
                                <td class="text-end small">{{ number_format($backup->ukuran / 1024, 1) }} KB</td>
                                <td class="small text-muted">{{ $backup->user?->name ?? '-' }}</td>
                                <td class="small">{{ $backup->created_at->translatedFormat('d M Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('backup.unduh', $backup) }}" class="btn btn-sm btn-success rounded-3" title="Unduh">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form id="delete-{{ $backup->id }}" action="{{ route('backup.destroy', $backup) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded-3" onclick="confirmDelete('delete-{{ $backup->id }}')">
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
        $('#datatable').DataTable({
            responsive: true,
            autoWidth: false,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
            pageLength: 10,
            order: [[3, 'desc']]
        });
    </script>
@endpush
