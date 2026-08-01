<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['L', 'P'])->index();
            $table->string('tempat_lahir', 60)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nama_ayah', 100)->nullable();
            $table->string('nama_ibu', 100)->nullable();
            $table->string('no_hp_wali', 20)->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'lulus'])->default('aktif')->index();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_lulus')->nullable();
            $table->timestamps();

            $table->index('kelas_id');
            $table->index(['status', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri');
    }
};
