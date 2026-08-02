@extends('layouts.app')

@section('title', 'Kelas')

@section('page-title', 'Kelas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Kelas</li>
@endsection

@section('content')
    <div class="card card-soft border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-school text-primary me-2"></i>Data Kelas</h5>
            <button class="btn btn-primary-grad" onclick="openCreate()">
                <i class="fas fa-plus me-1"></i>Tambah
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama Kelas</th>
                            <th>Tingkat</th>
                            <th>Tahun Ajaran</th>
                            <th class="text-center">Kuota</th>
                            <th class="text-center">Santri</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kelas as $item)
                            @php
                                $edit = [
                                    'id' => $item->id,
                                    'nama_kelas' => $item->nama_kelas,
                                    'tingkat' => $item->tingkat,
                                    'tahun_ajaran_id' => $item->tahun_ajaran_id,
                                    'kuota' => $item->kuota,
                                ];
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $item->nama_kelas }}</td>
                                <td><span class="badge badge-soft-info">{{ $item->tingkat }}</span></td>
                                <td>{{ $item->tahunAjaran?->nama }}</td>
                                <td class="text-center">{{ $item->kuota }}</td>
                                <td class="text-center">
                                    @php $jumlah = $item->santri_count ?? $item->santri()->count(); @endphp
                                    {{ $jumlah }}
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-info rounded-3" data-edit='@json($edit)' onclick="openEdit(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form id="delete-{{ $item->id }}" action="{{ route('kelas.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded-3" onclick="confirmDelete('delete-{{ $item->id }}')">
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
                <form id="formKelas" data-ajax>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_kelas" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" placeholder="cth: Kelas 1A" required>
                        </div>
                        <div class="mb-3">
                            <label for="tingkat" class="form-label">Tingkat</label>
                            <input type="text" class="form-control" id="tingkat" name="tingkat" placeholder="cth: 1 / Ula / Ibtida" required>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                            <select class="form-select select2" id="tahun_ajaran_id" name="tahun_ajaran_id" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahunAjarans as $tahunAjaran)
                                    <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kuota" class="form-label">Kuota</label>
                            <input type="number" class="form-control" id="kuota" name="kuota" min="1" max="999" value="30" required>
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

        const storeUrl = '{{ route('kelas.store') }}';

        function openCreate() {
            $('#formKelas')[0].reset();
            $('#formKelas input[name="_method"]').remove();
            $('#formKelas').attr('action', storeUrl).attr('method', 'POST');
            $('#modalTitle').text('Tambah Kelas');
            $('#modalForm').modal('show');
        }

        function openEdit(btn) {
            const d = $(btn).data('edit');
            $('#formKelas')[0].reset();
            $('#formKelas input[name="_method"]').remove();
            $('#formKelas').attr('action', '/kelas/' + d.id).attr('method', 'POST');
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#formKelas');
            $('#nama_kelas').val(d.nama_kelas);
            $('#tingkat').val(d.tingkat);
            $('#tahun_ajaran_id').val(d.tahun_ajaran_id).trigger('change');
            $('#kuota').val(d.kuota);
            $('#modalTitle').text('Ubah Kelas');
            $('#modalForm').modal('show');
        }
    </script>
@endpush
