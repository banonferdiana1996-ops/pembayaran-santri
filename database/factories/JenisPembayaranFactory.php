<?php

namespace Database\Factories;

use App\Models\JenisPembayaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JenisPembayaran>
 */
class JenisPembayaranFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode' => fake()->unique()->lexify('JP-????'),
            'nama' => fake()->randomElement(['SPP', 'Uang Dormitori', 'Uang Makan', 'Uang Buku', 'Uang Seragam']),
            'nominal' => fake()->randomElement([100000, 150000, 200000, 250000, 300000]),
            'is_bulanan' => fake()->boolean(60),
            'is_active' => true,
            'keterangan' => fake()->optional()->sentence(),
        ];
    }

    public function bulanan(int $nominal = 150000): static
    {
        return $this->state(fn () => [
            'is_bulanan' => true,
            'nominal' => $nominal,
        ]);
    }
}
