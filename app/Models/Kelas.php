<?php

namespace App\Models;

use Database\Factories\KelasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    /** @use HasFactory<KelasFactory> */
    use HasFactory;

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'tahun_ajaran_id',
        'kuota',
    ];

    protected function casts(): array
    {
        return [
            'kuota' => 'integer',
        ];
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function santri(): HasMany
    {
        return $this->hasMany(Santri::class);
    }
}
