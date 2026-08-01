<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Database\Seeder;

class SantriSeeder extends Seeder
{
    public function run(): void
    {
        $kelasList = Kelas::all();

        if ($kelasList->isEmpty()) {
            $this->call(KelasSeeder::class);
            $kelasList = Kelas::all();
        }

        $wali = User::role('wali')->first();
        $santriAkun = User::where('email', 'santri1@ppds.test')->first();

        $santris = Santri::factory()->count(10)->create(['kelas_id' => $kelasList->random()->id]);

        if ($santriAkun && $santris->isNotEmpty()) {
            $santris->first()->update(['user_id' => $santriAkun->id, 'nama_lengkap' => $santriAkun->name]);
        }

        if ($wali) {
            foreach ($santris->take(2) as $santri) {
                $santri->wali()->syncWithoutDetaching([$wali->id => ['hubungan' => 'ayah']]);
            }
        }
    }
}
