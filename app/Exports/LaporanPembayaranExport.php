<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanPembayaranExport implements FromArray, WithHeadings
{
    public function __construct(
        protected array $data
    ) {}

    public function array(): array
    {
        /** @var Collection $rows */
        $rows = $this->data['rows'];

        $result = $rows->map(fn ($r, $i) => [
            $i + 1,
            $r['nama'],
            $r['kelas'],
            $r['jumlah_transaksi'],
            $r['total'],
        ])->values()->toArray();

        $result[] = ['', 'TOTAL', '', $this->data['totalSantri'].' santri', $this->data['totalDibayar']];

        return $result;
    }

    public function headings(): array
    {
        return ['No', 'Nama Santri', 'Kelas', 'Jumlah Transaksi', 'Total Dibayar (Rp)'];
    }
}
