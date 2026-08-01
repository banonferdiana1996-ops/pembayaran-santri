<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'nama_institusi' => 'Pondok Pesantren Darussalam',
            'alamat' => 'Jl. Pesantren No. 1, Darussalam',
            'telepon' => '081234567890',
            'email' => 'info@ppds.test',
            'logo' => '/img/icon-192.png',
            'favicon' => '/img/icon-192.png',
            'landing_background' => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
