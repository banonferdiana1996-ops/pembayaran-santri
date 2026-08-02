<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TahunAjaranController extends Controller
{
    public function index(): View
    {
        return view('tahun-ajaran.index', ['tahunAjarans' => TahunAjaran::latest()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:20', 'unique:tahun_ajarans,nama'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_active']) {
            TahunAjaran::query()->update(['is_active' => false]);
        }

        TahunAjaran::create($data);

        return $this->jsonSuccess('Tahun ajaran berhasil ditambahkan.');
    }

    public function update(Request $request, TahunAjaran $tahunAjaran): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:20', 'unique:tahun_ajarans,nama,'.$tahunAjaran->id],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_active']) {
            TahunAjaran::where('id', '!=', $tahunAjaran->id)->update(['is_active' => false]);
        }

        $tahunAjaran->update($data);

        return $this->jsonSuccess('Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran): JsonResponse
    {
        $tahunAjaran->delete();

        return $this->jsonSuccess('Tahun ajaran berhasil dihapus.');
    }
}
