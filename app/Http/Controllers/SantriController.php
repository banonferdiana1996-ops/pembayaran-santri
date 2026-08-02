<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SantriController extends Controller
{
    public function index(): View
    {
        return view('santri.index', [
            'santris' => Santri::with(['kelas', 'user'])->latest()->get(),
            'kelas' => Kelas::with('tahunAjaran')->get(),
            'users' => User::role('santri')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $data['foto'] = $this->simpanFoto($request);
        $data['status'] = $data['status'] ?? 'aktif';

        Santri::create($data);

        return $this->jsonSuccess('Santri berhasil ditambahkan.');
    }

    public function update(Request $request, Santri $santri): JsonResponse
    {
        $data = $request->validate($this->rules($santri->id));
        $data['foto'] = $this->simpanFoto($request);

        $santri->update($data);

        return $this->jsonSuccess('Santri berhasil diperbarui.');
    }

    public function destroy(Santri $santri): JsonResponse
    {
        if ($santri->foto) {
            $path = public_path($santri->foto);

            if (is_file($path)) {
                @unlink($path);
            }
        }

        $santri->delete();

        return $this->jsonSuccess('Santri berhasil dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'nis' => ['required', 'string', 'max:20', 'unique:santri,nis'.($ignoreId ? ','.$ignoreId : '')],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir' => ['nullable', 'string', 'max:60'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'nama_ayah' => ['nullable', 'string', 'max:100'],
            'nama_ibu' => ['nullable', 'string', 'max:100'],
            'no_hp_wali' => ['nullable', 'string', 'max:20'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:aktif,nonaktif,lulus'],
            'tanggal_masuk' => ['nullable', 'date'],
            'tanggal_lulus' => ['nullable', 'date', 'after_or_equal:tanggal_masuk'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    private function simpanFoto(Request $request): ?string
    {
        if (! $request->hasFile('foto')) {
            return $request->has('hapus_foto') ? null : $request->input('foto_sekarang');
        }

        $file = $request->file('foto');
        $nama = 'santri-'.Str::lower(Str::random(8)).'-'.time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/santri'), $nama);

        return 'uploads/santri/'.$nama;
    }
}
