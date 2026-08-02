<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Santri;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SantriFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function loginAdmin(): User
    {
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_import_santri_dari_csv(): void
    {
        $admin = $this->loginAdmin();
        $tahun = TahunAjaran::create(['nama' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30']);
        Kelas::create(['nama_kelas' => 'Kelas 1A', 'tingkat' => '1', 'kuota' => 40, 'tahun_ajaran_id' => $tahun->id]);

        $csv = "nis,nama_lengkap,jenis_kelamin,kelas,status\n"
            ."2026001,Siti Aminah,P,Kelas 1A,aktif\n"
            ."2026002,Abdullah Rahman,L,,aktif\n";

        $file = UploadedFile::fake()->createWithContent('santri.csv', $csv);

        $response = $this->actingAs($admin)->post(route('santri.import'), ['file' => $file]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('santri', ['nis' => '2026001', 'nama_lengkap' => 'Siti Aminah']);
        $this->assertDatabaseHas('santri', ['nis' => '2026002', 'nama_lengkap' => 'Abdullah Rahman']);
    }

    public function test_kartu_santri_memuat_qr_code(): void
    {
        $admin = $this->loginAdmin();
        $santri = Santri::create([
            'nis' => '2026001',
            'nama_lengkap' => 'Siti Aminah',
            'jenis_kelamin' => 'P',
            'status' => 'aktif',
        ]);

        $this->actingAs($admin)->get(route('santri.kartu', $santri))
            ->assertOk()
            ->assertSee('KARTU SANTRI')
            ->assertSee('2026001')
            ->assertSee('data:image/png;base64');
    }
}
