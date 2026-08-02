<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Expense::query()->with('user')->latest('tanggal');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->string('kategori'));
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->date('dari'));
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->date('sampai'));
        }

        $total = (clone $query)->sum('jumlah');

        return view('expense.index', [
            'expenses' => $query->get(),
            'kategori' => $request->string('kategori')->toString(),
            'dari' => $request->input('dari'),
            'sampai' => $request->input('sampai'),
            'total' => $total,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'kategori' => ['required', 'in:operasional,sarana,gaji,lainnya'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $data['user_id'] = auth()->id();

        Expense::create($data);

        return $this->jsonSuccess('Pengeluaran berhasil dicatat.');
    }

    public function update(Request $request, Expense $expense): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'kategori' => ['required', 'in:operasional,sarana,gaji,lainnya'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $expense->update($data);

        return $this->jsonSuccess('Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $expense->delete();

        return $this->jsonSuccess('Pengeluaran berhasil dihapus.');
    }
}
