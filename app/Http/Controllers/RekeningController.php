<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use Illuminate\Http\Request;

class RekeningController extends Controller
{
    public function index()
    {
        // Menampilkan data terbaru dengan paginasi
        $rekening = Rekening::latest()->paginate(10);
        $title = 'Data Rekening';
        return view('admin.rekening', compact('rekening', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|max:100',
            'jenis' => 'required|in:kas,bank',
            'saldo' => 'required|numeric|min:0',
        ]);

        Rekening::create([
            'nama'  => $request->nama,
            'jenis' => $request->jenis,
            'saldo' => $request->saldo,
        ]);

        return redirect()->back()->with('success', 'Rekening berhasil ditambahkan dengan saldo awal.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'  => 'required|max:100',
            'jenis' => 'required|in:kas,bank',
            // Saldo sengaja tidak divalidasi/dimasukkan di sini
        ]);

        $rekening = Rekening::findOrFail($id);
        
        // Hanya update nama dan jenis saja
        $rekening->update([
            'nama'  => $request->nama,
            'jenis' => $request->jenis,
        ]);

        return redirect()->back()->with('success', 'Informasi rekening berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rekening = Rekening::findOrFail($id);
        
        // Proteksi: Jangan hapus jika saldo masih ada (Opsional)
        if ($rekening->saldo != 0) {
            return redirect()->back()->with('error', 'Rekening dengan saldo aktif tidak boleh dihapus. Kosongkan saldo terlebih dahulu.');
        }

        $rekening->delete(); // Melakukan Soft Delete

        return redirect()->back()->with('success', 'Rekening berhasil dinonaktifkan (Soft Delete).');
    }

    public function restore($id)
    {
        Rekening::withTrashed()->findOrFail($id)->restore();
        return redirect()->back()->with('success', 'Rekening berhasil diaktifkan kembali.');
    }
}