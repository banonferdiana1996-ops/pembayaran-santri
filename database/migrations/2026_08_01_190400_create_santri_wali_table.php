<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri_wali', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('wali_id')->constrained('users')->cascadeOnDelete();
            $table->enum('hubungan', ['ayah', 'ibu', 'wali'])->default('ayah');
            $table->timestamps();

            $table->unique(['santri_id', 'wali_id']);
            $table->index('wali_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_wali');
    }
};
