<?php

namespace App\Models;

use Database\Factories\TagihanFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    /** @use HasFactory<TagihanFactory> */
    use HasFactory;

    public const STATUS_BELUM_LUNAS = 'belum_lunas';

    public const STATUS_LUNAS = 'lunas';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    protected $fillable = [
        'nomor',
        'santri_id',
        'jenis_pembayaran_id',
        'tahun_ajaran_id',
        'periode_bulan',
        'nominal',
        'status',
        'tanggal_jatuh_tempo',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'periode_bulan' => 'integer',
            'nominal' => 'integer',
            'tanggal_jatuh_tempo' => 'date',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function jenisPembayaran(): BelongsTo
    {
        return $this->belongsTo(JenisPembayaran::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function totalDibayar(): Attribute
    {
        return Attribute::get(fn () => (int) $this->pembayarans()->sum('nominal'));
    }

    public function sisa(): Attribute
    {
        return Attribute::get(fn () => max(0, $this->nominal - $this->total_dibayar));
    }

    public function isLunas(): bool
    {
        return $this->status === self::STATUS_LUNAS;
    }

    public function isBulanan(): bool
    {
        return $this->periode_bulan !== null;
    }
}
