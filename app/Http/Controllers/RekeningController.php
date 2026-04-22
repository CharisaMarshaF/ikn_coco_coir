<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use Illuminate\Http\Request;

class RekeningController extends Controller
{
    public function index()
    {
        $rekening = Rekening::latest()->paginate(10);
        return view('admin.rekening', compact('rekening'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'jenis' => 'required|in:kas,bank',
            'saldo_awal' => 'required|numeric',
        ]);

        // Saat simpan pertama kali, saldo_saat_ini disamakan dengan saldo_awal
        Rekening::create([
            'nama' => $request->nama,
            'jenis' => $request->jenis,
            'saldo_awal' => $request->saldo_awal,
            'saldo_saat_ini' => $request->saldo_awal,
        ]);

        return redirect()->back()->with('success', 'Rekening berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'jenis' => 'required|in:kas,bank',
        ]);

        $rekening = Rekening::findOrFail($id);
        $rekening->update($request->only(['nama', 'jenis']));

        return redirect()->back()->with('success', 'Rekening berhasil diperbarui');
    }

    public function destroy($id)
    {
        $rekening = Rekening::findOrFail($id);
        $rekening->delete();

        return redirect()->back()->with('success', 'Rekening berhasil dihapus');
    }
}