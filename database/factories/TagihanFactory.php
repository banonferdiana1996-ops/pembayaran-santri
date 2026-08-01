<?php

namespace Database\Factories;

use App\Models\JenisPembayaran;
use App\Models\Santri;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tagihan>
 */
class TagihanFactory extends Factory
{
    public function definition(): array
    {
        $jenis = JenisPembayaran::factory()->create();

        return [
            'nomor' => fake()->unique()->numerify('TGR-########'),
            'santri_id' => Santri::factory(),
            'jenis_pembayaran_id' => $jenis->id,
            'tahun_ajaran_id' => TahunAjaran::factory(),
            'periode_bulan' => fake()->optional()->numberBetween(1, 12),
            'nominal' => fake()->randomElement([100000, 150000, 200000]),
            'status' => 'belum_lunas',
            'tanggal_jatuh_tempo' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'keterangan' => fake()->optional()->sentence(),
        ];
    }

    public function bulanan(int $bulan, int $nominal): static
    {
        return $this->state(fn () => [
            'periode_bulan' => $bulan,
            'nominal' => $nominal,
        ]);
    }

    public function lunas(): static
    {
        return $this->state(fn () => ['status' => 'lunas']);
    }
}
