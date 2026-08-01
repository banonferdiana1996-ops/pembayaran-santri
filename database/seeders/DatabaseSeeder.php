<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            SettingSeeder::class,
            TahunAjaranSeeder::class,
            KelasSeeder::class,
            JenisPembayaranSeeder::class,
            UserSeeder::class,
            SantriSeeder::class,
            TagihanSeeder::class,
            PembayaranSeeder::class,
            KeuanganSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
