<?php

namespace Database\Seeders;

use App\Models\JenisPembayaran;
use Illuminate\Database\Seeder;

class JenisPembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => 'SPP', 'nama' => 'SPP Bulanan', 'nominal' => 150000, 'is_bulanan' => true, 'keterangan' => 'Sumbangan Pembinaan Pendidikan per bulan'],
            ['kode' => 'DORMITORI', 'nama' => 'Uang Dormitori', 'nominal' => 100000, 'is_bulanan' => true, 'keterangan' => 'Biaya kamar/asrama per bulan'],
            ['kode' => 'MAKAN', 'nama' => 'Uang Makan', 'nominal' => 200000, 'is_bulanan' => true, 'keterangan' => 'Biaya makan santri per bulan'],
            ['kode' => 'BUKU', 'nama' => 'Uang Buku', 'nominal' => 250000, 'is_bulanan' => false, 'keterangan' => 'Pembelian buku pelajaran & kitab'],
            ['kode' => 'SERAGAM', 'nama' => 'Uang Seragam', 'nominal' => 300000, 'is_bulanan' => false, 'keterangan' => 'Seragam tahunan santri'],
        ];

        foreach ($data as $item) {
            JenisPembayaran::updateOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
