@extends('layouts.app')

@section('title', 'Buat Pembayaran')

@section('page-title', 'Buat Pembayaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pembayaran.index') }}">Pembayaran</a></li>
    <li class="breadcrumb-item active">Buat Pembayaran</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card card-soft border-0">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-user text-primary me-2"></i>Pilih Santri</h5>
                </div>
                <div class="card-body">
                    <select id="santri" class="form-select select2">
                        <option value="">-- Pilih Santri --</option>
                        @foreach ($santris as $santri)
                            <option value="{{ $santri->id }}">{{ $santri->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    <div id="santriInfo" class="mt-3 small text-muted d-none">
                        <hr class="my-2">
                        <div id="infoKelas" class="mb-1"></div>
                        <div id="infoTagihan" class="fw-semibold"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-soft border-0">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-file-invoice text-primary me-2"></i>Tagihan Belum Lunas</h5>
                </div>
                <div class="card-body">
                    <div id="emptyState" class="text-center text-muted py-5">
                        <i class="fas fa-user-clock fa-3x mb-3 d-block opacity-50"></i>
                        Pilih santri untuk melihat tagihan yang belum lunas.
                    </div>
                    <div id="tagihanWrap" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Jenis</th>
                                        <th>Periode</th>
                                        <th class="text-end">Sisa Tagihan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tagihanList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formBayar" data-ajax>
                    @csrf
                    <input type="hidden" id="tagihan_id" name="tagihan_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2 text-primary"></i>Catat Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tagihan</label>
                            <div id="tagihanSummary" class="border rounded-3 p-2 bg-light small">
                                <div id="summaryNomor" class="fw-semibold"></div>
                                <div id="summaryJenis" class="text-muted"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal Dibayar</label>
                            <input type="number" class="form-control" id="nominal" name="nominal" min="1" required>
                            <div class="form-text" id="sisaInfo"></div>
                        </div>
                        <div class="mb-3">
                            <label for="metode" class="form-label">Metode</label>
                            <select class="form-select" id="metode" name="metode" required>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_bayar" class="form-label">Tanggal Bayar</label>
                            <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-2">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-grad"><i class="fas fa-check-circle me-1"></i>Simpan & Cetak Kwitansi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const tagihanUrl = '{{ route('pembayaran.tagihan-belum-lunas', ':id') }}';
        const storeUrl = '{{ route('pembayaran.store') }}';

        function formatRp(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        }

        function loadTagihan(santriId) {
            if (!santriId) return;
            fetch(tagihanUrl.replace(':id', santriId), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    const list = $('#tagihanList').empty();
                    $('#emptyState').addClass('d-none');

                    if (!data.tagihans.length) {
                        $('#emptyState').removeClass('d-none').html('<i class="fas fa-circle-check fa-3x mb-3 d-block text-success opacity-50"></i>Tidak ada tagihan belum lunas.');
                        $('#tagihanWrap').addClass('d-none');
                        $('#santriInfo').addClass('d-none');
                        return;
                    }

                    $('#tagihanWrap').removeClass('d-none');
                    $('#infoKelas').text('');
                    $('#infoTagihan').text(data.tagihans.length + ' tagihan belum lunas');
                    $('#santriInfo').removeClass('d-none');

                    data.tagihans.forEach(t => {
                        const tr = $('<tr>');
                        $('<td>').addClass('text-muted small').text(t.nomor).appendTo(tr);
                        $('<td>').text(t.nama).appendTo(tr);
                        $('<td>').addClass('small').text(t.periode).appendTo(tr);
                        $('<td>').addClass('text-end fw-semibold').text(formatRp(t.sisa)).appendTo(tr);
                        const td = $('<td>').addClass('text-center');
                        $('<button>').addClass('btn btn-sm btn-primary-grad rounded-3')
                            .html('<i class="fas fa-cash-register me-1"></i>Bayar')
                            .on('click', () => openBayar(t))
                            .appendTo(td);
                        td.appendTo(tr);
                        tr.appendTo(list);
                    });
                })
                .catch(() => showToast('Gagal memuat tagihan.', 'error'));
        }

        function openBayar(t) {
            $('#tagihan_id').val(t.id);
            $('#summaryNomor').text('Nomor: ' + t.nomor);
            $('#summaryJenis').text(t.nama + ' — Periode: ' + t.periode);
            $('#nominal').val(t.sisa);
            $('#nominal').attr('max', t.sisa);
            $('#sisaInfo').text('Sisa tagihan: ' + formatRp(t.sisa) + '. Maksimal yang dapat dibayar: ' + formatRp(t.sisa) + '.');
            $('#formBayar').attr('action', storeUrl).attr('method', 'POST');
            $('#formBayar input[name="_method"]').remove();
            $('#modalBayar').modal('show');
        }

        $('#santri').on('change', function () {
            loadTagihan($(this).val());
        });
    </script>
@endpush
