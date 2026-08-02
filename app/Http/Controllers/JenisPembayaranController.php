<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenisPembayaranController extends Controller
{
    public function index(): View
    {
        return view('jenis-pembayaran.index', ['jenisPembayarans' => JenisPembayaran::latest()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:jenis_pembayarans,kode'],
            'nama' => ['required', 'string', 'max:60'],
            'nominal' => ['required', 'integer', 'min:0'],
            'is_bulanan' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $data['is_bulanan'] = $request->boolean('is_bulanan');
        $data['is_active'] = $request->boolean('is_active');

        JenisPembayaran::create($data);

        return $this->jsonSuccess('Jenis pembayaran berhasil ditambahkan.');
    }

    public function update(Request $request, JenisPembayaran $jenisPembayaran): JsonResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:jenis_pembayarans,kode,'.$jenisPembayaran->id],
            'nama' => ['required', 'string', 'max:60'],
            'nominal' => ['required', 'integer', 'min:0'],
            'is_bulanan' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $data['is_bulanan'] = $request->boolean('is_bulanan');
        $data['is_active'] = $request->boolean('is_active');

        $jenisPembayaran->update($data);

        return $this->jsonSuccess('Jenis pembayaran berhasil diperbarui.');
    }

    public function destroy(JenisPembayaran $jenisPembayaran): JsonResponse
    {
        if ($jenisPembayaran->tagihans()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis pembayaran tidak dapat dihapus karena masih memiliki tagihan. Nonaktifkan saja.',
            ], 422);
        }

        $jenisPembayaran->delete();

        return $this->jsonSuccess('Jenis pembayaran berhasil dihapus.');
    }
}
