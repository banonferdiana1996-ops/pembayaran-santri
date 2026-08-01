<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Backup>
 */
class BackupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_file' => fake()->unique()->numerify('backup-########.sql'),
            'ukuran' => fake()->numberBetween(5000, 500000),
            'user_id' => User::factory(),
        ];
    }
}
