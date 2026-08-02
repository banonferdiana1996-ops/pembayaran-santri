<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Services\WhatsappService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pembayaran::query()->with(['santri', 'jenisPembayaran', 'user', 'tagihan']);

        if ($request->filled('santri_id')) {
            $query->where('santri_id', $request->integer('santri_id'));
        }

        if ($request->filled('jenis_pembayaran_id')) {
            $query->where('jenis_pembayaran_id', $request->integer('jenis_pembayaran_id'));
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal_bayar', '>=', $request->date('dari'));
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_bayar', '<=', $request->date('sampai'));
        }

        return view('pembayaran.index', [
            'pembayarans' => $query->latest()->get(),
            'santris' => Santri::orderBy('nama_lengkap')->get(),
            'jenisPembayarans' => JenisPembayaran::all(),
            'selectedSantri' => $request->integer('santri_id'),
            'selectedJenis' => $request->integer('jenis_pembayaran_id'),
            'dari' => $request->input('dari'),
            'sampai' => $request->input('sampai'),
        ]);
    }

    public function create(): View
    {
        return view('pembayaran.create', [
            'santris' => Santri::orderBy('nama_lengkap')->get(),
        ]);
    }

    public function tagihanBelumLunas(Santri $santri): JsonResponse
    {
        $tagihans = $santri->tagihanAktif()
            ->with('jenisPembayaran')
            ->get()
            ->map(fn (Tagihan $tagihan) => [
                'id' => $tagihan->id,
                'nomor' => $tagihan->nomor,
                'nama' => $tagihan->jenisPembayaran?->nama,
                'periode' => $tagihan->periode_bulan
                    ? bulanIndonesia($tagihan->periode_bulan)
                    : 'Sekali bayar',
                'nominal' => $tagihan->nominal,
                'sisa' => $tagihan->sisa,
                'jatuh_tempo' => $tagihan->tanggal_jatuh_tempo?->format('d/m/Y'),
            ]);

        return response()->json(['tagihans' => $tagihans]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tagihan_id' => ['required', 'exists:tagihans,id'],
            'nominal' => ['required', 'integer', 'min:1'],
            'metode' => ['required', 'in:tunai,transfer'],
            'tanggal_bayar' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $tagihan = Tagihan::with('santri')->findOrFail($data['tagihan_id']);

        if ($tagihan->status !== Tagihan::STATUS_BELUM_LUNAS) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan ini sudah lunas atau dibatalkan.',
            ], 422);
        }

        if ($data['nominal'] > $tagihan->sisa) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal melebihi sisa tagihan ('.formatRupiah($tagihan->sisa).').',
            ], 422);
        }

        $pembayaran = DB::transaction(function () use ($data, $tagihan) {
            $nomor = 'PMB-'.now()->format('Y').str_pad(
                (string) (Pembayaran::whereYear('created_at', now()->year)->count() + 1),
                6,
                '0',
                STR_PAD_LEFT
            );

            $pembayaran = Pembayaran::create([
                'nomor' => $nomor,
                'tagihan_id' => $tagihan->id,
                'santri_id' => $tagihan->santri_id,
                'jenis_pembayaran_id' => $tagihan->jenis_pembayaran_id,
                'user_id' => auth()->id(),
                'nominal' => $data['nominal'],
                'metode' => $data['metode'],
                'tanggal_bayar' => $data['tanggal_bayar'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            if ($tagihan->pembayarans()->sum('nominal') >= $tagihan->nominal) {
                $tagihan->update(['status' => Tagihan::STATUS_LUNAS]);
            }

            return $pembayaran;
        });

        app(WhatsappService::class)->sendPembayaranNotification($pembayaran);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dicatat.',
            'redirect' => route('pembayaran.kwitansi', $pembayaran),
        ]);
    }

    public function kwitansi(Pembayaran $pembayaran): View
    {
        $pembayaran->load(['santri', 'jenisPembayaran', 'user', 'tagihan']);

        return view('pembayaran.kwitansi', ['pembayaran' => $pembayaran]);
    }

    public function unduhKwitansi(Pembayaran $pembayaran)
    {
        $pembayaran->load(['santri', 'jenisPembayaran', 'user', 'tagihan']);

        $pdf = Pdf::loadView('pembayaran.kwitansi-pdf', ['pembayaran' => $pembayaran]);

        return $pdf->download('kwitansi-'.$pembayaran->nomor.'.pdf');
    }

    public function destroy(Pembayaran $pembayaran): JsonResponse
    {
        DB::transaction(function () use ($pembayaran) {
            $tagihan = $pembayaran->tagihan;
            $pembayaran->delete();

            if ($tagihan && $tagihan->status === Tagihan::STATUS_LUNAS) {
                $tagihan->update([
                    'status' => $tagihan->pembayarans()->sum('nominal') >= $tagihan->nominal
                        ? Tagihan::STATUS_LUNAS
                        : Tagihan::STATUS_BELUM_LUNAS,
                ]);
            }
        });

        return $this->jsonSuccess('Pembayaran berhasil dihapus.');
    }
}
