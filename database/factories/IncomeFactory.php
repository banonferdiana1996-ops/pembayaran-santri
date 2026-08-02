<?php

namespace Database\Factories;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Income>
 */
class IncomeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sumber' => fake()->randomElement(['donasi', 'infaq', 'lainnya']),
            'jumlah' => fake()->randomElement([100000, 250000, 500000, 1000000]),
            'tanggal' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'keterangan' => fake()->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
