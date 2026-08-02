<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Income::query()->with('user')->latest('tanggal');

        if ($request->filled('sumber')) {
            $query->where('sumber', $request->string('sumber'));
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->date('dari'));
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->date('sampai'));
        }

        $total = (clone $query)->sum('jumlah');

        return view('income.index', [
            'incomes' => $query->get(),
            'sumber' => $request->string('sumber')->toString(),
            'dari' => $request->input('dari'),
            'sampai' => $request->input('sampai'),
            'total' => $total,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sumber' => ['required', 'in:donasi,infaq,lainnya'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $data['user_id'] = auth()->id();

        Income::create($data);

        return $this->jsonSuccess('Pemasukan berhasil dicatat.');
    }

    public function update(Request $request, Income $income): JsonResponse
    {
        $data = $request->validate([
            'sumber' => ['required', 'in:donasi,infaq,lainnya'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $income->update($data);

        return $this->jsonSuccess('Pemasukan berhasil diperbarui.');
    }

    public function destroy(Income $income): JsonResponse
    {
        $income->delete();

        return $this->jsonSuccess('Pemasukan berhasil dihapus.');
    }
}
