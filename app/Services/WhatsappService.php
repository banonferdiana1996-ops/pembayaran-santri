<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Support\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    public function isEnabled(): bool
    {
        return (bool) Setting::get('wa_enabled', false);
    }

    public function sendPembayaranNotification(Pembayaran $pembayaran): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $target = normalisasiNomorHp($pembayaran->santri?->no_hp_wali);

        if (! $target) {
            return false;
        }

        $url = (string) Setting::get('wa_api_url', 'https://api.fonnte.com/send');
        $token = (string) Setting::get('wa_api_token', '');

        try {
            $response = Http::timeout(8)
                ->withHeaders(['Authorization' => $token])
                ->post($url, [
                    'target' => $target,
                    'message' => $this->messagePembayaran($pembayaran),
                    'countryCode' => '62',
                ]);

            if ($response->failed()) {
                Log::warning('Notifikasi WhatsApp gagal: '.$response->status().' '.$response->body());
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim notifikasi WhatsApp: '.$e->getMessage());

            return false;
        }
    }

    private function messagePembayaran(Pembayaran $pembayaran): string
    {
        $santri = $pembayaran->santri;
        $tagihan = $pembayaran->tagihan;
        $sisa = (int) ($tagihan?->sisa ?? 0);
        $status = $sisa > 0 ? 'Sisa tagihan: '.formatRupiah($sisa) : 'Tagihan LUNAS';
        $sapaan = $santri?->nama_ayah
            ? 'Bapak '.$santri->nama_ayah
            : ($santri?->nama_ibu ? 'Ibu '.$santri->nama_ibu : 'Bapak/Ibu Wali Santri');

        return "Assalamu'alaikum, {$sapaan}.\n"
            ."Pembayaran santri berikut telah diterima:\n\n"
            ."Nama: {$santri?->nama_lengkap}\n"
            ."NIS: {$santri?->nis}\n"
            ."Pembayaran: {$pembayaran->jenisPembayaran?->nama}\n"
            .'Nominal: '.formatRupiah($pembayaran->nominal)."\n"
            .'Metode: '.($pembayaran->metode === 'tunai' ? 'Tunai' : 'Transfer')."\n"
            ."Tanggal: {$pembayaran->tanggal_bayar?->translatedFormat('d F Y')}\n"
            ."Status: {$status}\n\n"
            .'Terima kasih. '.Setting::get('nama_institusi', config('app.name'));
    }
}
