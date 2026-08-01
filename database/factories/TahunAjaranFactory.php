<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TahunAjaran>
 */
class TahunAjaranFactory extends Factory
{
    public function definition(): array
    {
        $year = fake()->numberBetween(2024, 2026);
        $next = $year + 1;

        return [
            'nama' => "{$year}/{$next}",
            'tanggal_mulai' => "{$year}-07-01",
            'tanggal_selesai' => "{$next}-06-30",
            'is_active' => false,
        ];
    }

    public function aktif(): static
    {
        return $this->state(fn () => ['is_active' => true]);
    }
}
