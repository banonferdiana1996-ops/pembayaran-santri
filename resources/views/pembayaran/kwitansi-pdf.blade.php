<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi {{ $pembayaran->nomor }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 0;
        }
        .container { padding: 30px 36px; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 12px;
        }
        .brand { font-size: 20px; font-weight: bold; color: #0d6efd; }
        .brand small { display: block; font-size: 11px; color: #6b7280; font-weight: normal; }
        .title { text-align: right; font-size: 20px; font-weight: bold; letter-spacing: 2px; }
        .meta { width: 100%; margin: 14px 0; border-collapse: collapse; }
        .meta td { padding: 2px 4px; vertical-align: top; }
        .meta .label { color: #6b7280; width: 70px; }
        .amount {
            border: 1px dashed #0d6efd;
            border-radius: 6px;
            padding: 12px 16px;
            background: #f8fbff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }
        .amount .value { font-size: 20px; font-weight: bold; color: #0d6efd; }
        .terbilang { font-style: italic; color: #6b7280; font-size: 11px; margin-top: 4px; }
        .sign { width: 100%; margin-top: 46px; }
        .sign td { width: 33%; vertical-align: top; font-size: 11px; color: #6b7280; }
        .sign .line { border-top: 1px solid #374151; margin-top: 34px; padding-top: 4px; color: #1f2937; font-weight: bold; }
        .footer { margin-top: 26px; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="brand">
                {{ config('app.name') }}
                <small>Pondok Pesantren Darussalam Putri</small>
            </div>
            <div class="title">KWITANSI</div>
        </div>

        <table class="meta">
            <tr>
                <td class="label">No.</td>
                <td><strong>{{ $pembayaran->nomor }}</strong></td>
                <td class="label">Jenis</td>
                <td>{{ $pembayaran->jenisPembayaran?->nama }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal</td>
                <td>{{ $pembayaran->tanggal_bayar?->translatedFormat('d F Y') }}</td>
                <td class="label">Periode</td>
                <td>
                    @if ($pembayaran->tagihan?->periode_bulan)
                        {{ bulanIndonesia($pembayaran->tagihan->periode_bulan) }} {{ $pembayaran->tagihan->tahunAjaran?->nama }}
                    @else
                        Sekali bayar
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Santri</td>
                <td><strong>{{ $pembayaran->santri?->nama_lengkap }}</strong></td>
                <td class="label">Kelas</td>
                <td>{{ $pembayaran->santri?->kelas?->nama_kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Metode</td>
                <td>{{ $pembayaran->metode === 'tunai' ? 'Tunai' : 'Transfer' }}</td>
                <td class="label">Petugas</td>
                <td>{{ $pembayaran->user?->name ?? '-' }}</td>
            </tr>
        </table>

        <div class="amount">
            <span>Telah diterima sebesar</span>
            <span class="value">{{ formatRupiah($pembayaran->nominal) }}</span>
        </div>
        <div class="terbilang">{{ terbilang($pembayaran->nominal) }} rupiah</div>

        <table class="sign">
            <tr>
                <td>
                    Petugas,
                    <div class="line">{{ $pembayaran->user?->name ?? '-' }}</div>
                </td>
                <td></td>
                <td>
                    Wali Santri,
                    <div class="line">{{ $pembayaran->santri?->nama_wali ?? '-' }}</div>
                </td>
            </tr>
        </table>

        <div class="footer">Dokumen ini dibuat otomatis oleh {{ config('app.name') }} pada {{ now()->translatedFormat('d F Y H:i') }}.</div>
    </div>
</body>
</html>
