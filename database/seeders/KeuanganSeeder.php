<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Seeder;

class KeuanganSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::role('bendahara')->first() ?? User::firstOrFail();

        Income::factory()->count(5)->create(['user_id' => $user->id]);
        Expense::factory()->count(5)->create(['user_id' => $user->id]);
    }
}
