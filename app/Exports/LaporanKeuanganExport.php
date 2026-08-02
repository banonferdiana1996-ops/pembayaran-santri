<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanKeuanganExport implements FromArray, WithHeadings
{
    public function __construct(
        protected array $data
    ) {}

    public function array(): array
    {
        /** @var Collection $rows */
        $rows = $this->data['rows'];

        $result = $rows->map(fn ($r) => [
            $r['tanggal'],
            $r['keterangan'],
            $r['pemasukan'],
            $r['pengeluaran'],
        ])->values()->toArray();

        $result[] = ['', 'TOTAL', $this->data['totalMasuk'], $this->data['totalKeluar']];
        $result[] = ['', 'SELISIH', $this->data['selisih'], ''];

        return $result;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Keterangan', 'Pemasukan (Rp)', 'Pengeluaran (Rp)'];
    }
}
