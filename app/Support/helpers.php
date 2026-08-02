<?php

declare(strict_types=1);
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if (! function_exists('bulanIndonesia')) {
    function bulanIndonesia(int $bulan): string
    {
        $daftar = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $daftar[$bulan] ?? 'Januari';
    }
}

if (! function_exists('formatRupiah')) {
    function formatRupiah(int|float|string|null $angka): string
    {
        return 'Rp '.number_format((int) $angka, 0, ',', '.');
    }
}

if (! function_exists('terbilang')) {
    function terbilang(int $angka): string
    {
        $angka = abs((int) $angka);
        $huruf = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($angka < 12) {
            return ' '.$huruf[$angka];
        }

        if ($angka < 20) {
            return terbilang($angka - 10).' belas';
        }

        if ($angka < 100) {
            return terbilang(intdiv($angka, 10)).' puluh'.terbilang($angka % 10);
        }

        if ($angka < 200) {
            return ' seratus'.terbilang($angka - 100);
        }

        if ($angka < 1000) {
            return terbilang(intdiv($angka, 100)).' ratus'.terbilang($angka % 100);
        }

        if ($angka < 2000) {
            return ' seribu'.terbilang($angka - 1000);
        }

        if ($angka < 1000000) {
            return terbilang(intdiv($angka, 1000)).' ribu'.terbilang($angka % 1000);
        }

        if ($angka < 1000000000) {
            return terbilang(intdiv($angka, 1000000)).' juta'.terbilang($angka % 1000000);
        }

        if ($angka < 1000000000000) {
            return terbilang(intdiv($angka, 1000000000)).' miliar'.terbilang($angka % 1000000000);
        }

        return terbilang(intdiv($angka, 1000000000000)).' triliun'.terbilang($angka % 1000000000000);
    }
}

if (! function_exists('terbilangRupiah')) {
    function terbilangRupiah(int|float|string|null $angka): string
    {
        return trim(terbilang((int) $angka)).' rupiah';
    }
}

if (! function_exists('qrcodeDataUri')) {
    function qrcodeDataUri(string $content, int $size = 220): string
    {
        $qrCode = new QrCode(data: $content, size: $size);

        return (new PngWriter)->write($qrCode)->getDataUri();
    }
}

if (! function_exists('normalisasiNomorHp')) {
    function normalisasiNomorHp(?string $nomor): ?string
    {
        $nomor = preg_replace('/[^0-9]/', '', (string) $nomor);

        if (str_starts_with($nomor, '0')) {
            $nomor = '62'.substr($nomor, 1);
        } elseif (! str_starts_with($nomor, '62')) {
            $nomor = '62'.$nomor;
        }

        return $nomor ?: null;
    }
}
