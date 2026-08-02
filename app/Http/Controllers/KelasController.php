<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(): View
    {
        return view('kelas.index', [
            'kelas' => Kelas::with('tahunAjaran')->latest()->get(),
            'tahunAjarans' => TahunAjaran::latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:30'],
            'tingkat' => ['required', 'string', 'max:10'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'kuota' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $exists = Kelas::where('nama_kelas', $data['nama_kelas'])
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas dengan nama tersebut sudah ada pada tahun ajaran yang sama.',
            ], 422);
        }

        Kelas::create($data);

        return $this->jsonSuccess('Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas): JsonResponse
    {
        $data = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:30'],
            'tingkat' => ['required', 'string', 'max:10'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'kuota' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $exists = Kelas::where('nama_kelas', $data['nama_kelas'])
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->where('id', '!=', $kelas->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas dengan nama tersebut sudah ada pada tahun ajaran yang sama.',
            ], 422);
        }

        $kelas->update($data);

        return $this->jsonSuccess('Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): JsonResponse
    {
        $kelas->delete();

        return $this->jsonSuccess('Kelas berhasil dihapus.');
    }
}
