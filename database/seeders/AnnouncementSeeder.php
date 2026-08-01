<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        Announcement::updateOrCreate(
            ['judul' => 'Pembayaran SPP bulan baru telah dibuka'],
            [
                'isi' => 'Seluruh wali santri diharapkan melakukan pembayaran SPP tepat waktu paling lambat tanggal 10 setiap bulannya. Terima kasih.',
                'tanggal' => now()->format('Y-m-d'),
                'scope' => 'semua',
                'is_active' => true,
            ]
        );

        Announcement::updateOrCreate(
            ['judul' => 'Jadwal Ujian Akhir Semester'],
            [
                'isi' => 'Ujian Akhir Semester Genap akan dilaksanakan pada minggu ke-3 bulan depan. Mohon persiapan santri ditingkatkan.',
                'tanggal' => now()->format('Y-m-d'),
                'scope' => 'dashboard',
                'is_active' => true,
            ]
        );
    }
}
