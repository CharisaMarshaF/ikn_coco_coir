<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokProduk; // Pastikan model ini di-import
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        // Load relasi stok agar tidak lambat (Eager Loading)
        $produk = Produk::with('stok')->latest()->paginate(10);
        return view('admin.produk', compact('produk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'harga_default' => 'nullable|numeric',
            'stok' => 'nullable|numeric|min:0',
        ]);

        $produk = Produk::create([
            'nama' => $request->nama,
            'satuan' => $request->satuan,
            'harga_default' => $request->harga_default,
        ]);

        // Simpan stok awal
        StokProduk::create([
            'produk_id' => $produk->id,
            'jumlah' => $request->stok ?? 0
        ]);

        return redirect()->back()->with('success', 'Produk dan stok berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'harga_default' => 'nullable|numeric',
            'stok' => 'required|numeric|min:0',
        ]);

        $produk = Produk::findOrFail($id);
        $produk->update([
            'nama' => $request->nama,
            'satuan' => $request->satuan,
            'harga_default' => $request->harga_default,
        ]);

        // Update atau buat stok jika data stok belum ada
        StokProduk::updateOrCreate(
            ['produk_id' => $id],
            ['jumlah' => $request->stok]
        );

        return redirect()->back()->with('success', 'Produk dan stok berhasil diperbarui');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        // Hapus stok terkait terlebih dahulu
        StokProduk::where('produk_id', $id)->delete();
        $produk->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus');
    }
}