<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kelas>
 */
class KelasFactory extends Factory
{
    public function definition(): array
    {
        $tingkat = fake()->randomElement(['7', '8', '9']);

        return [
            'nama_kelas' => "{$tingkat}-".fake()->randomElement(['A', 'B', 'C']),
            'tingkat' => $tingkat,
            'tahun_ajaran_id' => TahunAjaran::factory(),
            'kuota' => fake()->numberBetween(20, 35),
        ];
    }
}
