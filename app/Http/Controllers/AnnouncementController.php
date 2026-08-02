<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        return view('announcement.index', [
            'announcements' => Announcement::latest('tanggal')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:100'],
            'isi' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'scope' => ['required', 'in:landing,dashboard,semua'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        Announcement::create($data);

        return $this->jsonSuccess('Pengumuman berhasil ditambahkan.');
    }

    public function update(Request $request, Announcement $announcement): JsonResponse
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:100'],
            'isi' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'scope' => ['required', 'in:landing,dashboard,semua'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $announcement->update($data);

        return $this->jsonSuccess('Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement): JsonResponse
    {
        $announcement->delete();

        return $this->jsonSuccess('Pengumuman berhasil dihapus.');
    }
}
