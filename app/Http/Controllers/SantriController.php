<?php

namespace App\Http\Controllers;

use App\Exports\SantriTemplateExport;
use App\Imports\SantriImport;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

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

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file'],
        ]);

        $import = new SantriImport;
        Excel::import($import, $request->file('file'));

        $message = "Berhasil mengimpor {$import->imported} data santri.";

        if ($import->errors) {
            $message .= ' '.count($import->errors).' baris gagal. '.implode(' | ', array_slice($import->errors, 0, 5));
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'reload' => true,
        ]);
    }

    public function unduhTemplate()
    {
        return Excel::download(new SantriTemplateExport, 'template-import-santri.xlsx');
    }

    public function hapusMasal(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:santri,id'],
        ]);

        $santris = Santri::whereIn('id', $request->input('ids'))->get();

        foreach ($santris as $santri) {
            if ($santri->foto && is_file(public_path($santri->foto))) {
                @unlink(public_path($santri->foto));
            }
        }

        Santri::whereIn('id', $santris->pluck('id'))->delete();

        return $this->jsonSuccess(count($santris).' data santri berhasil dihapus.');
    }

    public function kartu(Santri $santri): View
    {
        $santri->load('kelas');
        $santri->setAttribute('qr', qrcodeDataUri($santri->nis));

        return view('santri.kartu', ['santri' => $santri]);
    }

    public function cetakKartuMasal(Request $request): View
    {
        $ids = collect(explode(',', (string) $request->query('ids')))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id);

        $santris = Santri::with('kelas')->whereIn('id', $ids)->get();

        $santris->each(function (Santri $santri) {
            $santri->setAttribute('qr', qrcodeDataUri($santri->nis));
        });

        return view('santri.kartu-masal', ['santris' => $santris]);
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
