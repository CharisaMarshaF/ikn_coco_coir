<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokProduk;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index()
    {
        // Optimasi: Hanya ambil kolom yang diperlukan dan eager loading stok dengan select
        $produk = Produk::select('id', 'nama', 'satuan', 'harga_default','jenis')
            ->with(['stok' => function($query) {
                $query->select('produk_id', 'jumlah');
            }])
            ->latest()
            ->paginate(10);

        $title = 'Data Produk';
        return view('admin.produk', compact('produk', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'jenis' => 'required|in:jadi,proses',
            'harga_default' => 'nullable|numeric',
            'stok' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $produk = Produk::create([
                'nama' => $request->nama,
                'satuan' => $request->satuan,
                'jenis' => $request->jenis,
                'harga_default' => $request->harga_default,
            ]);

            StokProduk::create([
                'produk_id' => $produk->id,
                'jumlah' => $request->stok ?? 0
            ]);
        });

        return redirect()->back()->with('success', 'Produk dan stok berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'jenis' => 'required|in:jadi,proses',
            'harga_default' => 'nullable|numeric',
            'stok_manual' => 'nullable|numeric', 
        ]);

        DB::transaction(function () use ($request, $id) {
            $produk = Produk::findOrFail($id);
            
            // 1. Update data produk dasar
            $produk->update([
                'nama' => $request->nama,
                'satuan' => $request->satuan,
                'jenis' => $request->jenis,
                'harga_default' => $request->harga_default,
            ]);

            // 2. Logika Update Stok & Log (HANYA UNTUK ADMIN)
            if (auth()->user()->role == 'admin' && $request->filled('stok_manual')) {
                $stokLama = $produk->stok->jumlah ?? 0;
                $stokBaru = $request->stok_manual;

                if ($stokLama != $stokBaru) {
                    StokProduk::updateOrCreate(
                        ['produk_id' => $id],
                        ['jumlah' => $stokBaru]
                    );

                    StockLog::create([
                        'item_id' => $id,
                        'item_type' => 'produk',
                        'jenis' => ($stokBaru > $stokLama) ? 'masuk' : 'keluar',
                        'jumlah' => abs($stokBaru - $stokLama),
                        'stok_sebelum' => $stokLama,
                        'stok_sesudah' => $stokBaru,
                        'sumber' => 'manual',
                        'keterangan' => $request->keterangan ?? 'Koreksi stok oleh Admin',
                        'user_id' => auth()->id()
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        
        DB::transaction(function () use ($produk) {
            $produk->stok()->delete();
            $produk->delete();
        });

        return redirect()->back()->with('success', 'Produk berhasil dihapus');
    }
}