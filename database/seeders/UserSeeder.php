<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator', 'email' => 'admin@ppds.test', 'role' => 'admin',
            ],
            [
                'name' => 'Bendahara', 'email' => 'bendahara@ppds.test', 'role' => 'bendahara',
            ],
            [
                'name' => 'Ust. Ahmad Santoso', 'email' => 'santri1@ppds.test', 'role' => 'santri',
            ],
            [
                'name' => 'H. Slamet Riyadi', 'email' => 'wali1@ppds.test', 'role' => 'wali',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('password'), 'is_active' => true])
            );
            $user->assignRole($role);
        }
    }
}
