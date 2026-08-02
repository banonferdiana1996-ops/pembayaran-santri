<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class BackupController extends Controller
{
    protected function backupDir(): string
    {
        $dir = storage_path('app/backups');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir;
    }

    public function index(): View
    {
        return view('backup.index', [
            'backups' => Backup::latest()->get(),
        ]);
    }

    public function store(): JsonResponse
    {
        $conn = Config::get('database.connections.mysql');
        $filename = 'backup-'.now()->format('Ymd-His').'.sql';
        $path = $this->backupDir().'/'.$filename;

        $cmd = sprintf(
            'mysqldump --no-tablespaces --user=%s --password=%s --host=%s %s > %s 2>/dev/null',
            escapeshellarg((string) $conn['username']),
            escapeshellarg((string) $conn['password']),
            escapeshellarg((string) $conn['host']),
            escapeshellarg((string) $conn['database']),
            escapeshellarg($path)
        );

        exec($cmd, $output, $code);

        if ($code !== 0 || ! file_exists($path) || filesize($path) === 0) {
            if (file_exists($path)) {
                unlink($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat backup database. Pastikan mysqldump tersedia.',
            ], 422);
        }

        Backup::create([
            'nama_file' => $filename,
            'ukuran' => filesize($path),
            'user_id' => auth()->id(),
        ]);

        return $this->jsonSuccess('Backup database berhasil dibuat.');
    }

    public function unduh(Backup $backup)
    {
        $path = $this->backupDir().'/'.$backup->nama_file;

        abort_unless(file_exists($path), 404, 'File backup tidak ditemukan.');

        return response()->download($path, $backup->nama_file);
    }

    public function destroy(Backup $backup): JsonResponse
    {
        $path = $this->backupDir().'/'.$backup->nama_file;

        if (file_exists($path)) {
            unlink($path);
        }

        $backup->delete();

        return $this->jsonSuccess('Backup berhasil dihapus.');
    }
}
