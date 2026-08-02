<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ $jenis === 'pembayaran' ? 'Rekap Pembayaran' : 'Keuangan' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; margin: 0; }
        .container { padding: 28px 32px; }
        .header { text-align: center; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; margin-bottom: 16px; }
        .header h2 { margin: 0; font-size: 18px; color: #0d6efd; }
        .header p { margin: 2px 0 0; color: #6b7280; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; }
        th { background: #eef2ff; }
        .num { text-align: right; }
        .total-row td { font-weight: bold; background: #f3f4f6; }
        .summary { width: 100%; margin-bottom: 16px; }
        .summary td { border: none; padding: 2px 4px; }
        .footer { margin-top: 22px; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ config('app.name') }}</h2>
            <p>Pondok Pesantren Darussalam Putri</p>
            <p><strong>LAPORAN {{ $jenis === 'pembayaran' ? 'REKAP PEMBAYARAN' : 'KEUANGAN' }}</strong></p>
            <p>Periode: {{ $dari->translatedFormat('d F Y') }} s/d {{ $sampai->translatedFormat('d F Y') }}</p>
        </div>

        @if ($jenis === 'keuangan')
            <table class="summary">
                <tr>
                    <td>Total Pemasukan: <strong>{{ formatRupiah($totalMasuk) }}</strong></td>
                    <td>Total Pengeluaran: <strong>{{ formatRupiah($totalKeluar) }}</strong></td>
                    <td>Selisih: <strong>{{ formatRupiah($selisih) }}</strong></td>
                </tr>
            </table>
            <table>
                <thead>
                    <tr>
                        <th style="width:12%">Tanggal</th>
                        <th>Keterangan</th>
                        <th class="num">Pemasukan</th>
                        <th class="num">Pengeluaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row['tanggal'] }}</td>
                            <td>{{ $row['keterangan'] }}</td>
                            <td class="num">{{ $row['pemasukan'] ? number_format($row['pemasukan'], 0, ',', '.') : '-' }}</td>
                            <td class="num">{{ $row['pengeluaran'] ? number_format($row['pengeluaran'], 0, ',', '.') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Tidak ada data pada periode ini.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2" style="text-align:right">TOTAL</td>
                        <td class="num">{{ number_format($totalMasuk, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($totalKeluar, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:6%">No</th>
                        <th>Nama Santri</th>
                        <th>Kelas</th>
                        <th class="num" style="width:15%">Jumlah Transaksi</th>
                        <th class="num" style="width:22%">Total Dibayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $i => $row)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $row['nama'] }}</td>
                            <td>{{ $row['kelas'] }}</td>
                            <td class="num">{{ $row['jumlah_transaksi'] }}</td>
                            <td class="num">{{ number_format($row['total'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Tidak ada data pada periode ini.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" style="text-align:right">TOTAL ({{ $totalSantri }} santri)</td>
                        <td class="num">{{ $rows->sum('jumlah_transaksi') }}</td>
                        <td class="num">{{ number_format($totalDibayar, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <div class="footer">
            Dicetak oleh {{ auth()->user()->name }} pada {{ now()->translatedFormat('d F Y H:i') }} — {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
