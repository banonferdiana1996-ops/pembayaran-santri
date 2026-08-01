<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->foreignId('tagihan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('jenis_pembayaran_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('nominal');
            $table->enum('metode', ['tunai', 'transfer'])->default('tunai');
            $table->date('tanggal_bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['santri_id', 'tanggal_bayar']);
            $table->index('user_id');
            $table->index('jenis_pembayaran_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
