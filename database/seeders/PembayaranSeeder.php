<?php

namespace Database\Seeders;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $bendahara = User::role('bendahara')->first() ?? User::firstOrFail();
        $tagihans = Tagihan::query()->with('santri')->get();

        $no = 1;

        foreach ($tagihans as $tagihan) {
            $rand = fake()->randomFloat(0, 0, 1);

            if ($rand < 0.5) {
                // Lunas penuh
                $this->bayar($tagihan, $tagihan->nominal, $bendahara, $no++);
            } elseif ($rand < 0.7) {
                // Cicilan 50%
                $this->bayar($tagihan, intdiv($tagihan->nominal, 2), $bendahara, $no++);
            }
        }
    }

    private function bayar(Tagihan $tagihan, int $nominal, User $bendahara, int $no): void
    {
        $pembayaran = Pembayaran::create([
            'nomor' => 'PMB-'.date('Y').str_pad((string) $no, 6, '0', STR_PAD_LEFT),
            'tagihan_id' => $tagihan->id,
            'santri_id' => $tagihan->santri_id,
            'jenis_pembayaran_id' => $tagihan->jenis_pembayaran_id,
            'user_id' => $bendahara->id,
            'nominal' => $nominal,
            'metode' => ['tunai', 'transfer'][$no % 2],
            'tanggal_bayar' => now()->subDays($no % 10)->format('Y-m-d'),
            'keterangan' => 'Pembayaran '.($tagihan->jenisPembayaran?->nama ?? 'tagihan'),
        ]);

        $totalDibayar = $tagihan->pembayarans()->sum('nominal');

        if ($totalDibayar >= $tagihan->nominal) {
            $tagihan->update(['status' => 'lunas']);
        }
    }
}
