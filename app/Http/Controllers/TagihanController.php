<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagihanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tagihan::query()->with(['santri', 'jenisPembayaran', 'tahunAjaran']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('jenis_pembayaran_id')) {
            $query->where('jenis_pembayaran_id', $request->integer('jenis_pembayaran_id'));
        }

        if ($request->filled('periode_bulan')) {
            $query->where('periode_bulan', $request->integer('periode_bulan'));
        }

        return view('tagihan.index', [
            'tagihans' => $query->latest()->get(),
            'status' => $request->string('status')->toString(),
            'jenisPembayarans' => JenisPembayaran::all(),
            'tahunAjarans' => TahunAjaran::all(),
            'kelas' => Kelas::all(),
            'selectedJenis' => $request->integer('jenis_pembayaran_id'),
            'selectedBulan' => $request->integer('periode_bulan'),
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'jenis_pembayaran_id' => ['required', 'exists:jenis_pembayarans,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'periode_bulan' => ['nullable', 'integer', 'between:1,12'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'nominal' => ['nullable', 'integer', 'min:0'],
            'tanggal_jatuh_tempo' => ['nullable', 'date'],
        ]);

        $jenis = JenisPembayaran::findOrFail($data['jenis_pembayaran_id']);
        $tahunAjaran = TahunAjaran::findOrFail($data['tahun_ajaran_id']);

        if ($jenis->is_bulanan && empty($data['periode_bulan'])) {
            return response()->json([
                'success' => false,
                'message' => 'Periode bulan wajib diisi untuk pembayaran bulanan.',
            ], 422);
        }

        $santris = Santri::query()
            ->where('status', 'aktif')
            ->when(! empty($data['kelas_id']), fn ($q) => $q->where('kelas_id', $data['kelas_id']))
            ->get();

        if ($santris->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada santri aktif yang ditemukan.',
            ], 422);
        }

        $nominal = $data['nominal'] ?? $jenis->nominal;
        $bulan = $data['periode_bulan'] ?? null;
        $tahun = $tahunAjaran->tanggal_mulai?->format('Y') ?? now()->format('Y');
        $created = 0;
        $skipped = 0;

        foreach ($santris as $santri) {
            $result = Tagihan::firstOrCreate(
                [
                    'santri_id' => $santri->id,
                    'jenis_pembayaran_id' => $jenis->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'periode_bulan' => $bulan,
                ],
                [
                    'nomor' => 'TGR-'.$tahun.str_pad((string) $santri->id, 4, '0', STR_PAD_LEFT).$jenis->kode.($bulan ?? ''),
                    'nominal' => $nominal,
                    'status' => Tagihan::STATUS_BELUM_LUNAS,
                    'tanggal_jatuh_tempo' => $data['tanggal_jatuh_tempo']
                        ?? ($bulan ? sprintf('%s-%02d-10', $tahun, $bulan) : null),
                    'keterangan' => trim(
                        $jenis->nama
                        .($bulan ? ' bulan '.bulanIndonesia($bulan) : '')
                        .' '.$tahunAjaran->nama
                    ),
                ]
            );

            $result->wasRecentlyCreated ? $created++ : $skipped++;
        }

        return $this->jsonSuccess("Tagihan berhasil dibuat untuk {$created} santri".($skipped ? " ({$skipped} sudah ada)" : ''));
    }

    public function destroy(Tagihan $tagihan): JsonResponse
    {
        if ($tagihan->pembayarans()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak dapat dihapus karena sudah memiliki pembayaran.',
            ], 422);
        }

        $tagihan->delete();

        return $this->jsonSuccess('Tagihan berhasil dihapus.');
    }
}
