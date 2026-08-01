<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class Setting
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember('setting:'.$key, 3600, function () use ($key, $default) {
            try {
                $value = \App\Models\Setting::query()->where('key', $key)->value('value');

                return $value ?? $default;
            } catch (\Throwable) {
                return $default;
            }
        });
    }

    public static function set(string $key, mixed $value): void
    {
        try {
            \App\Models\Setting::query()->updateOrCreate(['key' => $key], ['value' => (string) $value]);
        } catch (\Throwable) {
            // tabel belum tersedia saat instalasi awal
        }

        Cache::forget('setting:'.$key);
    }

    public static function flush(): void
    {
        Cache::flush();
    }
}
