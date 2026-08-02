<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Santri;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SantriImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;

    public array $errors = [];

    public function collection(Collection $rows): void
    {
        $kelas = Kelas::pluck('id', 'nama_kelas');

        foreach ($rows as $index => $row) {
            $row = $row->toArray();
            $nomorBaris = $index + 2;

            try {
                $nis = trim((string) ($row['nis'] ?? ''));
                $namaLengkap = trim((string) ($row['nama_lengkap'] ?? ''));

                if ($nis === '' || $namaLengkap === '') {
                    throw new \RuntimeException('NIS dan Nama Lengkap wajib diisi.');
                }

                if (Santri::where('nis', $nis)->exists()) {
                    throw new \RuntimeException("NIS '{$nis}' sudah terdaftar.");
                }

                $jenisKelamin = strtoupper(trim((string) ($row['jenis_kelamin'] ?? '')));
                if (! in_array($jenisKelamin, ['L', 'P'], true)) {
                    throw new \RuntimeException('Jenis Kelamin harus L atau P.');
                }

                $status = strtolower(trim((string) ($row['status'] ?? 'aktif')));
                if (! in_array($status, ['aktif', 'nonaktif', 'lulus'], true)) {
                    $status = 'aktif';
                }

                $namaKelas = trim((string) ($row['kelas'] ?? ''));
                $kelasId = $namaKelas !== '' ? ($kelas[$namaKelas] ?? null) : null;

                $data = [
                    'nis' => $nis,
                    'nama_lengkap' => $namaLengkap,
                    'jenis_kelamin' => $jenisKelamin,
                    'tempat_lahir' => trim((string) ($row['tempat_lahir'] ?? '')) ?: null,
                    'tanggal_lahir' => $this->parseTanggal($row['tanggal_lahir'] ?? null),
                    'alamat' => trim((string) ($row['alamat'] ?? '')) ?: null,
                    'nama_ayah' => trim((string) ($row['nama_ayah'] ?? '')) ?: null,
                    'nama_ibu' => trim((string) ($row['nama_ibu'] ?? '')) ?: null,
                    'no_hp_wali' => trim((string) ($row['no_hp_wali'] ?? '')) ?: null,
                    'kelas_id' => $kelasId,
                    'status' => $status,
                    'tanggal_masuk' => $this->parseTanggal($row['tanggal_masuk'] ?? null),
                ];

                Santri::create($data);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->errors[] = "Baris {$nomorBaris}: {$e->getMessage()}";
            }
        }
    }

    private function parseTanggal(mixed $value): ?string
    {
        if (! $value || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $value = trim((string) $value);

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $format) {
            $parsed = \DateTime::createFromFormat($format, $value);
            if ($parsed !== false) {
                return $parsed->format('Y-m-d');
            }
        }

        $parsed = strtotime($value);

        return $parsed !== false ? date('Y-m-d', $parsed) : null;
    }
}
