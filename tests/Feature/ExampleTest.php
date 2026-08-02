<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_di_redirect_ke_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_halaman_login_dapat_diakses(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Masuk ke Akun Anda');
    }

    public function test_login_berhasil_mengarah_ke_dashboard(): void
    {
        Role::create(['name' => 'admin']);
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $user->assignRole('admin');

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_password_salah_kembali_ke_login(): void
    {
        User::factory()->create(['password' => bcrypt('password')]);

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
