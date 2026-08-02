<?php

namespace App\Models;

use Database\Factories\SantriFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Santri extends Model
{
    /** @use HasFactory<SantriFactory> */
    use HasFactory;

    protected $table = 'santri';

    protected $fillable = [
        'nis',
        'user_id',
        'kelas_id',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nama_ayah',
        'nama_ibu',
        'no_hp_wali',
        'foto',
        'status',
        'tanggal_masuk',
        'tanggal_lulus',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_masuk' => 'date',
            'tanggal_lulus' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function wali(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'santri_wali', 'santri_id', 'wali_id')
            ->withPivot('hubungan')
            ->withTimestamps();
    }

    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function tagihanAktif(): HasMany
    {
        return $this->hasMany(Tagihan::class)->where('status', 'belum_lunas');
    }

    public function totalTagihan(): Attribute
    {
        return Attribute::get(fn () => (int) $this->tagihans()->sum('nominal'));
    }

    public function totalBayar(): Attribute
    {
        return Attribute::get(fn () => (int) $this->pembayarans()->sum('nominal'));
    }

    public function totalSisa(): Attribute
    {
        return Attribute::get(fn () => max(0, $this->total_tagihan - $this->total_bayar));
    }

    public function isLulus(): bool
    {
        return $this->status === 'lulus';
    }
}
