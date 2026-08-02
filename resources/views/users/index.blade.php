@extends('layouts.app')

@section('title', 'Pengguna')

@section('page-title', 'Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Pengguna</li>
@endsection

@section('content')
    <div class="card card-soft border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold"><i class="fas fa-users text-primary me-2"></i>Data Pengguna</h5>
            <button class="btn btn-primary-grad" onclick="openCreate()">
                <i class="fas fa-plus me-1"></i>Tambah Pengguna
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            @php
                                $edit = [
                                    'id' => $user->id,
                                    'name' => $user->name,
                                    'email' => $user->email,
                                    'phone' => $user->phone,
                                    'role' => $user->roles->first()?->name,
                                    'is_active' => $user->is_active,
                                ];
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="text-muted">{{ $user->phone ?: '-' }}</td>
                                <td>
                                    @foreach ($user->roles as $role)
                                        <span class="badge badge-soft-info text-capitalize">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="badge badge-soft-success"><i class="fas fa-circle me-1"></i>Aktif</span>
                                    @else
                                        <span class="badge badge-soft-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-info rounded-3" data-edit='@json($edit)' onclick="openEdit(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <form id="delete-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger rounded-3" onclick="confirmDelete('delete-{{ $user->id }}')">
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
                <form id="formUser" data-ajax>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Pengguna</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="passwordWrap">
                            <label for="password" class="form-label" id="passwordLabel">Password</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="8" autocomplete="new-password">
                            <div class="form-text">Minimal 8 karakter. Kosongkan saat mengubah agar tetap sama.</div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Akun aktif</label>
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
            pageLength: 10
        });

        const storeUrl = '{{ route('users.store') }}';

        function openCreate() {
            $('#formUser')[0].reset();
            $('#formUser input[name="_method"]').remove();
            $('#formUser').attr('action', storeUrl).attr('method', 'POST');
            $('#password').prop('required', true);
            $('#passwordLabel').text('Password');
            $('#passwordWrap .form-text').text('Minimal 8 karakter.');
            $('#is_active').prop('checked', true);
            $('#modalTitle').text('Tambah Pengguna');
            $('#modalForm').modal('show');
        }

        function openEdit(btn) {
            const d = $(btn).data('edit');
            $('#formUser')[0].reset();
            $('#formUser input[name="_method"]').remove();
            $('#formUser').attr('action', '/users/' + d.id).attr('method', 'POST');
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#formUser');
            $('#name').val(d.name);
            $('#email').val(d.email);
            $('#phone').val(d.phone || '');
            $('#role').val(d.role || '');
            $('#password').prop('required', false);
            $('#passwordLabel').text('Password (opsional)');
            $('#passwordWrap .form-text').text('Kosongkan jika tidak ingin mengubah password.');
            $('#is_active').prop('checked', !!d.is_active);
            $('#modalTitle').text('Ubah Pengguna');
            $('#modalForm').modal('show');
        }
        window.openCreate = openCreate;
        window.openEdit = openEdit;
        });
</script>
@endpush
