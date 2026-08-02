<?php

namespace App\Http\Controllers;

use App\Exports\LaporanKeuanganExport;
use App\Exports\LaporanPembayaranExport;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $jenis = in_array($request->input('jenis'), ['keuangan', 'pembayaran'], true)
            ? $request->string('jenis')->toString()
            : 'keuangan';

        $dari = $request->date('dari') ?? now()->startOfMonth();
        $sampai = $request->date('sampai') ?? now()->endOfMonth();

        $data = $jenis === 'pembayaran'
            ? $this->dataPembayaran($dari, $sampai)
            : $this->dataKeuangan($dari, $sampai);

        return view('laporan.index', array_merge([
            'jenis' => $jenis,
            'dari' => $dari->format('Y-m-d'),
            'sampai' => $sampai->format('Y-m-d'),
        ], $data));
    }

    public function unduhPdf(Request $request)
    {
        $jenis = in_array($request->input('jenis'), ['keuangan', 'pembayaran'], true)
            ? $request->string('jenis')->toString()
            : 'keuangan';

        $dari = $request->date('dari') ?? now()->startOfMonth();
        $sampai = $request->date('sampai') ?? now()->endOfMonth();

        $data = $jenis === 'pembayaran'
            ? $this->dataPembayaran($dari, $sampai)
            : $this->dataKeuangan($dari, $sampai);

        $pdf = Pdf::loadView('laporan.pdf', array_merge([
            'jenis' => $jenis,
            'dari' => $dari,
            'sampai' => $sampai,
        ], $data));

        return $pdf->download('laporan-'.($jenis === 'pembayaran' ? 'pembayaran' : 'keuangan').'-'.$dari->format('Ymd').'-'.$sampai->format('Ymd').'.pdf');
    }

    public function unduhExcel(Request $request)
    {
        $jenis = in_array($request->input('jenis'), ['keuangan', 'pembayaran'], true)
            ? $request->string('jenis')->toString()
            : 'keuangan';

        $dari = $request->date('dari') ?? now()->startOfMonth();
        $sampai = $request->date('sampai') ?? now()->endOfMonth();

        if ($jenis === 'pembayaran') {
            return Excel::download(new LaporanPembayaranExport($this->dataPembayaran($dari, $sampai)), 'laporan-pembayaran.xlsx');
        }

        return Excel::download(new LaporanKeuanganExport($this->dataKeuangan($dari, $sampai)), 'laporan-keuangan.xlsx');
    }

    protected function dataKeuangan($dari, $sampai): array
    {
        $pembayarans = Pembayaran::whereBetween('tanggal_bayar', [$dari, $sampai])
            ->with(['santri', 'jenisPembayaran'])
            ->get();

        $incomes = Income::whereBetween('tanggal', [$dari, $sampai])->get();
        $expenses = Expense::whereBetween('tanggal', [$dari, $sampai])->get();

        $rows = collect();

        foreach ($pembayarans as $p) {
            $rows->push([
                'tanggal' => $p->tanggal_bayar->format('d/m/Y'),
                'keterangan' => 'Pembayaran '.$p->nomor.' - '.($p->santri?->nama_lengkap ?? '-').' ('.($p->jenisPembayaran?->nama ?? '-').')',
                'pemasukan' => $p->nominal,
                'pengeluaran' => 0,
            ]);
        }

        foreach ($incomes as $i) {
            $rows->push([
                'tanggal' => $i->tanggal->format('d/m/Y'),
                'keterangan' => 'Pemasukan '.ucfirst($i->sumber).($i->keterangan ? ' - '.$i->keterangan : ''),
                'pemasukan' => $i->jumlah,
                'pengeluaran' => 0,
            ]);
        }

        foreach ($expenses as $e) {
            $rows->push([
                'tanggal' => $e->tanggal->format('d/m/Y'),
                'keterangan' => 'Pengeluaran '.$e->nama.' ('.ucfirst($e->kategori).')'.($e->deskripsi ? ' - '.$e->deskripsi : ''),
                'pemasukan' => 0,
                'pengeluaran' => $e->jumlah,
            ]);
        }

        $rows = $rows->sortBy(fn ($r) => $r['tanggal'])->values();

        $totalMasuk = $rows->sum('pemasukan');
        $totalKeluar = $rows->sum('pengeluaran');

        return [
            'rows' => $rows,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'selisih' => $totalMasuk - $totalKeluar,
        ];
    }

    protected function dataPembayaran($dari, $sampai): array
    {
        $rows = Pembayaran::whereBetween('tanggal_bayar', [$dari, $sampai])
            ->with(['santri.kelas'])
            ->get()
            ->groupBy('santri_id')
            ->map(function (Collection $items) {
                $santri = $items->first()->santri;

                return [
                    'nama' => $santri?->nama_lengkap ?? '-',
                    'kelas' => $santri?->kelas?->nama_kelas ?? '-',
                    'jumlah_transaksi' => $items->count(),
                    'total' => (int) $items->sum('nominal'),
                ];
            })
            ->values()
            ->sortBy('nama')
            ->values();

        return [
            'rows' => $rows,
            'totalSantri' => $rows->count(),
            'totalDibayar' => (int) $rows->sum('total'),
        ];
    }
}
