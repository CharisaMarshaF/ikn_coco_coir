<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\StokBahan;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index()
    {
        // Gunakan with('stok') agar tidak terjadi N+1 query
        $bahan = BahanBaku::with('stok')->latest()->paginate(10);
        return view('admin.bahan_baku', compact('bahan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'stok' => 'nullable|numeric|min:0'
        ]);

        $bahan = BahanBaku::create([
            'nama' => $request->nama,
            'satuan' => $request->satuan
        ]);

        // Inisialisasi stok awal jika diisi
        StokBahan::create([
            'bahan_id' => $bahan->id,
            'jumlah' => $request->stok ?? 0
        ]);

        return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'stok' => 'required|numeric|min:0'
        ]);

        $bahan = BahanBaku::findOrFail($id);
        $bahan->update([
            'nama' => $request->nama,
            'satuan' => $request->satuan
        ]);

        // Update atau buat stok jika belum ada
        StokBahan::updateOrCreate(
            ['bahan_id' => $id],
            ['jumlah' => $request->stok]
        );

        return redirect()->back()->with('success', 'Bahan baku berhasil diperbarui');
    }

    public function destroy($id)
    {
        $bahan = BahanBaku::findOrFail($id);
        // Stok akan terhapus otomatis jika Anda menggunakan foreign key ON DELETE CASCADE
        // Jika tidak, hapus manual:
        StokBahan::where('bahan_id', $id)->delete();
        $bahan->delete();

        return redirect()->back()->with('success', 'Bahan baku berhasil dihapus');
    }
}