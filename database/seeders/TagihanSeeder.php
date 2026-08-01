<?php

namespace Database\Seeders;

use App\Models\JenisPembayaran;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TagihanSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::firstOrFail();
        $jenisBulanan = JenisPembayaran::query()->where('is_bulanan', true)->where('is_active', true)->get();

        if ($jenisBulanan->isEmpty()) {
            $jenisBulanan = collect([JenisPembayaran::factory()->bulanan()->create()]);
        }

        $santris = Santri::query()->where('status', 'aktif')->get();

        foreach ($santris as $santri) {
            foreach ($jenisBulanan as $jenis) {
                foreach ([1, 2, 3] as $bulan) {
                    Tagihan::firstOrCreate(
                        [
                            'santri_id' => $santri->id,
                            'jenis_pembayaran_id' => $jenis->id,
                            'tahun_ajaran_id' => $tahunAjaran->id,
                            'periode_bulan' => $bulan,
                        ],
                        [
                            'nomor' => 'TGR-' . date('Y') . str_pad((string) $santri->id, 4, '0', STR_PAD_LEFT) . $jenis->kode . $bulan,
                            'nominal' => $jenis->nominal,
                            'status' => 'belum_lunas',
                            'tanggal_jatuh_tempo' => sprintf('%s-%02d-10', $tahunAjaran->tanggal_mulai->format('Y'), $bulan),
                            'keterangan' => "{$jenis->nama} bulan " . bulanIndonesia($bulan) . ' ' . $tahunAjaran->nama,
                        ]
                    );
                }
            }
        }
    }
}
