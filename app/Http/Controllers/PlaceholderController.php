<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PlaceholderController extends Controller
{
    private const MODULES = [
        'tahun-ajaran.index' => 'Tahun Ajaran',
        'kelas.index' => 'Kelas',
        'santri.index' => 'Data Santri',
        'users.index' => 'Pengguna',
        'jenis-pembayaran.index' => 'Jenis Pembayaran',
        'tagihan.index' => 'Tagihan',
        'pembayaran.index' => 'Pembayaran',
        'income.index' => 'Pemasukan',
        'expense.index' => 'Pengeluaran',
        'report.index' => 'Laporan',
        'announcement.index' => 'Pengumuman',
        'setting.index' => 'Pengaturan',
        'backup.index' => 'Backup',
    ];

    public function __invoke(Request $request): View
    {
        $routeName = (string) $request->route()?->getName();

        return view('placeholder', ['modul' => self::MODULES[$routeName] ?? 'Modul']);
    }
}
