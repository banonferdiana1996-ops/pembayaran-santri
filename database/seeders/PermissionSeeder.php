<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'master-data', 'kelola-santri', 'kelola-kelas', 'kelola-tahun-ajaran',
            'kelola-jenis-pembayaran', 'kelola-tagihan', 'kelola-pembayaran',
            'kelola-income', 'kelola-expense', 'lihat-laporan', 'kelola-pengguna',
            'kelola-pengumuman', 'kelola-pengaturan', 'kelola-backup', 'cetak-kartu',
            'cetak-kwitansi', 'kelola-promosi',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        $bendahara = Role::firstOrCreate(['name' => 'bendahara']);
        $bendahara->syncPermissions([
            'kelola-jenis-pembayaran', 'kelola-tagihan', 'kelola-pembayaran',
            'kelola-income', 'kelola-expense', 'lihat-laporan', 'cetak-kwitansi',
        ]);

        Role::firstOrCreate(['name' => 'santri']);
        Role::firstOrCreate(['name' => 'wali']);
    }
}
