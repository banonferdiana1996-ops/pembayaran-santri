<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement(['Listrik', 'Air', 'Konsumsi Rapat', 'ATK', 'Perbaikan Gedung', 'Gaji Karyawan']),
            'jumlah' => fake()->randomElement([50000, 150000, 300000, 750000]),
            'tanggal' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'kategori' => fake()->randomElement(['operasional', 'sarana', 'gaji', 'lainnya']),
            'deskripsi' => fake()->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
