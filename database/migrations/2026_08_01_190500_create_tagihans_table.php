<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('jenis_pembayaran_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('periode_bulan')->nullable()->index();
            $table->unsignedBigInteger('nominal');
            $table->enum('status', ['belum_lunas', 'lunas', 'dibatalkan'])->default('belum_lunas')->index();
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['santri_id', 'jenis_pembayaran_id', 'tahun_ajaran_id', 'periode_bulan'], 'uniq_tagihan_bulanan');
            $table->index(['santri_id', 'status']);
            $table->index(['jenis_pembayaran_id', 'periode_bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
