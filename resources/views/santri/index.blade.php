@extends('layouts.app')

@section('title', 'Data Santri')

@section('page-title', 'Data Santri')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Data Santri</li>
@endsection

@section('content')
    <div class="card card-soft border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-user-graduate text-primary me-2"></i>Data Santri</h5>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-primary" id="btnCetakKartu" onclick="cetakKartuTerpilih()" disabled>
                    <i class="fas fa-id-card me-1"></i>Cetak Kartu
                </button>
                <button class="btn btn-outline-danger" id="btnHapusMasal" onclick="hapusTerpilih()" disabled>
                    <i class="fas fa-trash me-1"></i>Hapus Terpilih
                </button>
                <button class="btn btn-outline-secondary" onclick="openImport()">
                    <i class="fas fa-file-import me-1"></i>Upload Data
                </button>
                <button class="btn btn-primary-grad" onclick="openCreate()">
                    <i class="fas fa-plus me-1"></i>Tambah Santri
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 36px;">
                                <input type="checkbox" class="form-check-input" id="pilihSemua" title="Pilih semua">
                            </th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>JK</th>
                            <th>Kelas</th>
                            <th>Wali</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($santris as $santri)
                            @php
                                $edit = [
                                    'id' => $santri->id,
                                    'nis' => $santri->nis,
                                    'nama_lengkap' => $santri->nama_lengkap,
                                    'jenis_kelamin' => $santri->jenis_kelamin,
                                    'tempat_lahir' => $santri->tempat_lahir,
                                    'tanggal_lahir' => $santri->tanggal_lahir?->format('Y-m-d'),
                                    'alamat' => $santri->alamat,
                                    'nama_ayah' => $santri->nama_ayah,
                                    'nama_ibu' => $santri->nama_ibu,
                                    'no_hp_wali' => $santri->no_hp_wali,
                                    'kelas_id' => $santri->kelas_id,
                                    'user_id' => $santri->user_id,
                                    'status' => $santri->status,
                                    'tanggal_masuk' => $santri->tanggal_masuk?->format('Y-m-d'),
                                    'tanggal_lulus' => $santri->tanggal_lulus?->format('Y-m-d'),
                                    'foto' => $santri->foto,
                                ];
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <input type="checkbox" class="form-check-input pilih-santri" value="{{ $santri->id }}" data-nis="{{ $santri->nis }}">
                                </td>
                                <td class="text-muted">{{ $santri->nis }}</td>
                                <td class="fw-semibold">{{ $santri->nama_lengkap }}</td>
                                <td>{{ $santri->jenis_kelamin }}</td>
                                <td>{{ $santri->kelas?->nama_kelas ?? '-' }}</td>
                                <td class="small text-muted">{{ $santri->nama_ayah ?: '-' }}</td>
                                <td>
                                    @if ($santri->status === 'aktif')
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @elseif ($santri->status === 'lulus')
                                        <span class="badge badge-soft-info">Lulus</span>
                                    @else
                                        <span class="badge badge-soft-warning">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('santri.kartu', $santri) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-3" title="Cetak Kartu">
                                        <i class="fas fa-id-card"></i>
                                    </a>
                                    <button class="btn btn-sm btn-info rounded-3" data-edit='@json($edit)' onclick="openEdit(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form id="delete-{{ $santri->id }}" action="{{ route('santri.destroy', $santri) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded-3" onclick="confirmDelete('delete-{{ $santri->id }}')">
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
                <form id="formSantri" data-ajax>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Santri</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="nis" class="form-label">NIS</label>
                                <input type="text" class="form-control" id="nis" name="nis" placeholder="cth: 2026001" required>
                            </div>
                            <div class="col-md-8">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            </div>
                            <div class="col-md-4">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                    <option value="lulus">Lulus</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="kelas_id" class="form-label">Kelas</label>
                                <select class="form-select select2" id="kelas_id" name="kelas_id">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelas as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama_kelas }} ({{ $item->tahunAjaran?->nama }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir">
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                            </div>
                            <div class="col-12">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="nama_ayah" class="form-label">Nama Ayah</label>
                                <input type="text" class="form-control" id="nama_ayah" name="nama_ayah">
                            </div>
                            <div class="col-md-6">
                                <label for="nama_ibu" class="form-label">Nama Ibu</label>
                                <input type="text" class="form-control" id="nama_ibu" name="nama_ibu">
                            </div>
                            <div class="col-md-6">
                                <label for="no_hp_wali" class="form-label">No. HP Wali</label>
                                <input type="text" class="form-control" id="no_hp_wali" name="no_hp_wali" placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">Akun Login</label>
                                <select class="form-select select2" id="user_id" name="user_id">
                                    <option value="">-- Tanpa Akun --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk">
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_lulus" class="form-label">Tanggal Lulus</label>
                                <input type="date" class="form-control" id="tanggal_lulus" name="tanggal_lulus">
                            </div>
                            <div class="col-md-6">
                                <label for="foto" class="form-label">Foto</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                <input type="hidden" name="foto_sekarang" id="foto_sekarang">
                                <div class="form-check mt-2 d-none" id="hapusFotoWrap">
                                    <input class="form-check-input" type="checkbox" id="hapus_foto" name="hapus_foto" value="1">
                                    <label class="form-check-label" for="hapus_foto">Hapus foto saat ini</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">Preview</label>
                                <img id="previewFoto" src="" alt="Preview" class="rounded-3 object-fit-cover" style="width: 72px; height: 72px; display: none;">
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

    <div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formImport" data-ajax enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Data Santri</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info small">
                            <i class="fas fa-circle-info me-1"></i>
                            Gunakan format Excel (.xlsx/.xls/.csv). Kolom yang diperlukan: NIS, Nama Lengkap, Jenis Kelamin (L/P). Kolom lain opsional.
                            <a href="{{ route('santri.import.template') }}" class="fw-semibold d-inline-block mt-1">
                                <i class="fas fa-download me-1"></i>Unduh Template
                            </a>
                        </div>
                        <div class="mb-3">
                            <label for="file_import" class="form-label">File Data Santri</label>
                            <input type="file" class="form-control" id="file_import" name="file" accept=".xlsx,.xls,.csv" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-grad"><i class="fas fa-file-import me-1"></i>Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function () {
        // ===== Pilihan massal =====
        const selected = new Set();
        const $btnCetak = $('#btnCetakKartu');
        const $btnHapus = $('#btnHapusMasal');

        function syncBulk() {
            const count = selected.size;
            $btnCetak.prop('disabled', count === 0);
            $btnHapus.prop('disabled', count === 0);
            $btnCetak.text('Cetak Kartu' + (count ? ' (' + count + ')' : ''));
            $btnHapus.text('Hapus Terpilih' + (count ? ' (' + count + ')' : ''));
        }

        const dt = $('#datatable').DataTable({
            responsive: true,
            autoWidth: false,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
            pageLength: 10
        });

        dt.on('draw', function () {
            $('.pilih-santri:visible').each(function () {
                this.checked = selected.has($(this).val());
            });
            syncBulk();
        });

        const storeUrl = '{{ route('santri.store') }}';

        $(document).on('change', '.pilih-santri', function () {
            const id = $(this).val();
            this.checked ? selected.add(id) : selected.delete(id);
            syncBulk();
        });

        $('#pilihSemua').on('change', function () {
            const checked = this.checked;
            $('.pilih-santri:visible').each(function () {
                this.checked = checked;
                checked ? selected.add($(this).val()) : selected.delete($(this).val());
            });
            syncBulk();
        });

        window.cetakKartuTerpilih = function () {
            if (!selected.size) return;
            window.open('{{ route('santri.cetak-kartu') }}?ids=' + Array.from(selected).join(','), '_blank');
        };

        window.hapusTerpilih = async function () {
            if (!selected.size) return;
            const confirm = await Swal.fire({
                title: 'Hapus data terpilih?',
                text: selected.size + ' data santri akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });
            if (!confirm.isConfirmed) return;

            const formData = new FormData();
            Array.from(selected).forEach(id => formData.append('ids[]', id));

            try {
                const res = await fetch('{{ route('santri.hapus-masal') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    body: formData
                });
                const json = await res.json();
                if (json.success) {
                    showToast('success', json.message);
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast('error', json.message || 'Terjadi kesalahan!');
                }
            } catch (e) {
                showToast('error', 'Terjadi kesalahan server!');
            }
        };

        // ===== Modal import =====
        window.openImport = function () {
            $('#formImport')[0].reset();
            $('#modalImport').modal('show');
        };

        $('#foto').on('change', function () {
            const [file] = this.files;
            if (!file) return;
            $('#previewFoto').attr('src', URL.createObjectURL(file)).show();
        });

        function openCreate() {
            $('#formSantri')[0].reset();
            $('#formSantri input[name="_method"]').remove();
            $('#formSantri').attr('action', storeUrl).attr('method', 'POST');
            $('#foto_sekarang').val('');
            $('#previewFoto').hide();
            $('#hapusFotoWrap').addClass('d-none');
            $('#modalTitle').text('Tambah Santri');
            $('#modalForm').modal('show');
        }

        function openEdit(btn) {
            const d = $(btn).data('edit');
            $('#formSantri')[0].reset();
            $('#formSantri input[name="_method"]').remove();
            $('#formSantri').attr('action', '/santri/' + d.id).attr('method', 'POST');
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#formSantri');
            $('#nis').val(d.nis);
            $('#nama_lengkap').val(d.nama_lengkap);
            $('#jenis_kelamin').val(d.jenis_kelamin);
            $('#status').val(d.status);
            $('#kelas_id').val(d.kelas_id || '').trigger('change');
            $('#tempat_lahir').val(d.tempat_lahir || '');
            $('#tanggal_lahir').val(d.tanggal_lahir || '');
            $('#alamat').val(d.alamat || '');
            $('#nama_ayah').val(d.nama_ayah || '');
            $('#nama_ibu').val(d.nama_ibu || '');
            $('#no_hp_wali').val(d.no_hp_wali || '');
            $('#user_id').val(d.user_id || '').trigger('change');
            $('#tanggal_masuk').val(d.tanggal_masuk || '');
            $('#tanggal_lulus').val(d.tanggal_lulus || '');
            $('#foto_sekarang').val(d.foto || '');
            if (d.foto) {
                $('#previewFoto').attr('src', '/' + d.foto).show();
                $('#hapusFotoWrap').removeClass('d-none');
            } else {
                $('#previewFoto').hide();
                $('#hapusFotoWrap').addClass('d-none');
            }
            $('#modalTitle').text('Ubah Santri');
            $('#modalForm').modal('show');
        }
        window.openCreate = openCreate;
        window.openEdit = openEdit;
        });
</script>
@endpush
