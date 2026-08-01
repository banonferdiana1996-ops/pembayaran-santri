<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::aktif() ?? TahunAjaran::firstOrFail();

        $daftar = [
            ['7A', '7'], ['7B', '7'], ['7C', '7'],
            ['8A', '8'], ['8B', '8'], ['8C', '8'],
            ['9A', '9'], ['9B', '9'], ['9C', '9'],
        ];

        foreach ($daftar as [$nama, $tingkat]) {
            Kelas::firstOrCreate(
                ['nama_kelas' => $nama, 'tahun_ajaran_id' => $tahunAjaran->id],
                ['tingkat' => $tingkat, 'kuota' => 30]
            );
        }
    }
}
