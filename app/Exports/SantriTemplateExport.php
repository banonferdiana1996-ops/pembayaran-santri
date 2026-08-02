<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithTitle;

class SantriTemplateExport implements FromArray, WithHeadingRow, WithTitle
{
    public function array(): array
    {
        return [
            [
                '2026001',
                'Nama Lengkap Contoh',
                'P',
                'Cirebon',
                '2010-01-15',
                'Alamat contoh',
                'Nama Ayah Contoh',
                'Nama Ibu Contoh',
                '081234567890',
                'Kelas 1A',
                'aktif',
                '2026-07-13',
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Santri';
    }
}
