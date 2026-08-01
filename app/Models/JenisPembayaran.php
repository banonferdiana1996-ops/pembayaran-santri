<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPembayaran extends Model
{
    /** @use HasFactory<\Database\Factories\JenisPembayaranFactory> */
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'nominal',
        'is_bulanan',
        'is_active',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'is_bulanan' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }
}
