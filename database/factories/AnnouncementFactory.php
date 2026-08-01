<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'judul' => fake()->sentence(5),
            'isi' => fake()->paragraph(3),
            'tanggal' => fake()->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
            'scope' => fake()->randomElement(['landing', 'dashboard', 'semua']),
            'is_active' => true,
        ];
    }

    public function landing(): static
    {
        return $this->state(fn () => ['scope' => 'landing']);
    }
}
