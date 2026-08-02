<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pembayaran>
 */
class PembayaranFactory extends Factory
{
    public function definition(): array
    {
        $tagihan = Tagihan::factory()->create();
        $santri = Santri::find($tagihan->santri_id);

        return [
            'nomor' => fake()->unique()->numerify('PMB-########'),
            'tagihan_id' => $tagihan->id,
            'santri_id' => $santri->id,
            'jenis_pembayaran_id' => $tagihan->jenis_pembayaran_id,
            'user_id' => User::factory(),
            'nominal' => $tagihan->nominal,
            'metode' => fake()->randomElement(['tunai', 'transfer']),
            'tanggal_bayar' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'keterangan' => fake()->optional()->sentence(),
        ];
    }

    public function untukTagihan(Tagihan $tagihan, int $nominal = 0): static
    {
        return $this->state(function () use ($tagihan, $nominal) {
            $santri = $tagihan->santri;

            return [
                'tagihan_id' => $tagihan->id,
                'santri_id' => $santri->id,
                'jenis_pembayaran_id' => $tagihan->jenis_pembayaran_id,
                'nominal' => $nominal > 0 ? $nominal : $tagihan->nominal,
            ];
        });
    }
}
