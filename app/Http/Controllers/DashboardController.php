<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $data = match (true) {
            $user->hasAnyRole(['admin', 'bendahara']) => $this->dataKeuangan(),
            $user->hasRole('wali') => $this->dataWali($user),
            default => $this->dataSantri($user),
        };

        return view('dashboard', $data);
    }

    private function dataKeuangan(): array
    {
        $pemasukanKas = (int) Income::sum('jumlah') + (int) Pembayaran::sum('nominal');
        $pengeluaran = (int) Expense::sum('jumlah');

        [$labels, $pemasukan, $pengeluaranChart] = $this->grafikBulanan();

        return [
            'role' => 'keuangan',
            'totalSantri' => Santri::count(),
            'santriAktif' => Santri::where('status', 'aktif')->count(),
            'tagihanBelumLunas' => Tagihan::where('status', Tagihan::STATUS_BELUM_LUNAS)->count(),
            'tagihanNominal' => (int) Tagihan::where('status', '!=', Tagihan::STATUS_DIBATALKAN)->sum('nominal'),
            'totalPembayaran' => (int) Pembayaran::sum('nominal'),
            'pemasukanKas' => $pemasukanKas,
            'pengeluaran' => $pengeluaran,
            'saldo' => $pemasukanKas - $pengeluaran,
            'pembayaranTerbaru' => Pembayaran::query()
                ->with(['santri', 'jenisPembayaran', 'user'])
                ->latest()
                ->limit(8)
                ->get(),
            'chartLabels' => $labels,
            'chartPemasukan' => $pemasukan,
            'chartPengeluaran' => $pengeluaranChart,
        ];
    }

    private function grafikBulanan(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => now()->startOfMonth()->subMonthsNoOverflow($i));

        $labels = $months->map(fn ($m) => bulanIndonesia((int) $m->format('n')))->values();

        $pemasukan = $months->map(function ($m) {
            return (int) Pembayaran::whereYear('tanggal_bayar', $m->year)
                ->whereMonth('tanggal_bayar', $m->month)
                ->sum('nominal')
                + (int) Income::whereYear('tanggal', $m->year)
                    ->whereMonth('tanggal', $m->month)
                    ->sum('jumlah');
        })->values();

        $pengeluaran = $months->map(function ($m) {
            return (int) Expense::whereYear('tanggal', $m->year)
                ->whereMonth('tanggal', $m->month)
                ->sum('jumlah');
        })->values();

        return [$labels, $pemasukan, $pengeluaran];
    }

    private function dataSantri(mixed $user): array
    {
        $santri = $user->santri;

        return [
            'role' => 'santri',
            'santri' => $santri,
            'tagihans' => $santri?->tagihans()->with(['jenisPembayaran', 'tahunAjaran'])->latest()->get() ?? collect(),
            'pembayarans' => $santri?->pembayarans()->with(['jenisPembayaran'])->latest()->limit(8)->get() ?? collect(),
        ];
    }

    private function dataWali(mixed $user): array
    {
        $anakAsuh = $user->anakAsuh()->with('kelas')->get();

        $anakAsuh->each(function (Santri $santri) {
            $santri->setAttribute('tagihan_belum_lunas', $santri->tagihanAktif()->count());
            $santri->setAttribute('total_sisa', $santri->total_sisa);
        });

        return [
            'role' => 'wali',
            'anakAsuh' => $anakAsuh,
        ];
    }
}
