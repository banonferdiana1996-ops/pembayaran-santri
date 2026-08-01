<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Santri>
 */
class SantriFactory extends Factory
{
    public function definition(): array
    {
        $jk = fake()->randomElement(['L', 'P']);

        return [
            'nis' => fake()->unique()->numerify(date('Y') . '###'),
            'user_id' => null,
            'kelas_id' => Kelas::factory(),
            'nama_lengkap' => fake()->name($jk === 'L' ? 'male' : 'female'),
            'jenis_kelamin' => $jk,
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-15 years', '-11 years')->format('Y-m-d'),
            'alamat' => fake()->address(),
            'nama_ayah' => fake()->name('male'),
            'nama_ibu' => fake()->name('female'),
            'no_hp_wali' => fake()->numerify('08##########'),
            'foto' => null,
            'status' => 'aktif',
            'tanggal_masuk' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'tanggal_lulus' => null,
        ];
    }

    public function lulus(): static
    {
        return $this->state(fn () => [
            'status' => 'lulus',
            'tanggal_lulus' => now()->format('Y-m-d'),
        ]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['status' => 'nonaktif']);
    }

    public function denganAkun(): static
    {
        return $this->state(function () {
            $user = User::factory()->create(['is_active' => true]);

            return ['user_id' => $user->id];
        });
    }
}
