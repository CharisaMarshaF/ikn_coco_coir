<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\StokBahan;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BahanBakuController extends Controller
{
    public function index()
    {
        // 1. Ambil kolom yang diperlukan saja (Optimasi Memori)
        // 2. Eager loading stok dengan kolom terbatas
        $bahan = BahanBaku::select('id', 'nama', 'satuan')
            ->with(['stok' => function($query) {
                $query->select('bahan_id', 'jumlah');
            }])
            ->latest()
            ->paginate(10);

        $title = 'Data Bahan Baku';
        return view('admin.bahan_baku', compact('bahan', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'stok' => 'nullable|numeric|min:0'
        ]);

        DB::transaction(function () use ($request) {
            $bahan = BahanBaku::create([
                'nama' => $request->nama,
                'satuan' => $request->satuan
            ]);

            StokBahan::create([
                'bahan_id' => $bahan->id,
                'jumlah' => $request->stok ?? 0
            ]);
        });

        return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'stok_manual' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request, $id) {
            // Gunakan lockForUpdate() jika trafik sangat tinggi
            $bahan = BahanBaku::findOrFail($id);
            $bahan->update([
                'nama' => $request->nama,
                'satuan' => $request->satuan
            ]);

            if ($request->filled('stok_manual') && auth()->user()->role == 'admin') {
                $stokLama = $bahan->stok->jumlah ?? 0;
                $stokBaru = $request->stok_manual;

                if ($stokLama != $stokBaru) {
                    StokBahan::updateOrCreate(
                        ['bahan_id' => $id],
                        ['jumlah' => $stokBaru]
                    );

                    StockLog::create([
                        'item_id' => $id,
                        'item_type' => 'bahan_baku',
                        'jenis' => ($stokBaru > $stokLama) ? 'masuk' : 'keluar',
                        'jumlah' => abs($stokBaru - $stokLama),
                        'stok_sebelum' => $stokLama,
                        'stok_sesudah' => $stokBaru,
                        'sumber' => 'manual',
                        'user_id' => auth()->id(),
                        'keterangan' => $request->keterangan ?? 'Koreksi stok manual oleh Admin'
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $bahan = BahanBaku::findOrFail($id);
        DB::transaction(function () use ($bahan) {
            $bahan->stok()->delete();
            $bahan->delete();
        });

        return redirect()->back()->with('success', 'Bahan baku berhasil dihapus');
    }
}