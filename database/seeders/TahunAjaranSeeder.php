<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        TahunAjaran::query()->update(['is_active' => false]);

        TahunAjaran::updateOrCreate(
            ['nama' => '2025/2026'],
            ['tanggal_mulai' => '2025-07-01', 'tanggal_selesai' => '2026-06-30', 'is_active' => false]
        );

        TahunAjaran::updateOrCreate(
            ['nama' => '2026/2027'],
            ['tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]
        );
    }
}
