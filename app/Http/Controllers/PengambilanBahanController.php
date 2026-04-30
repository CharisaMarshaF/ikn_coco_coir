<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\PengambilanBahan;
use App\Models\PengambilanBahanDetail;
use App\Models\StokBahan;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengambilanBahanController extends Controller
{
    public function index()
    {
        $pengambilan = PengambilanBahan::with(['details.bahan'])
            ->latest()
            ->paginate(10);

        $title = 'Data Pengambilan Bahan';
        return view('admin.pengambilan.index', compact('pengambilan', 'title'));
    }

    public function create()
    {
        $bahanBaku = BahanBaku::with('stok')->orderBy('nama', 'asc')->get();
        $title = 'Tambah Pengambilan';
        return view('admin.pengambilan.create', compact('bahanBaku', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kategori_pola' => 'required|in:bulat,set jadi,jadi',
            'bahan_ids' => 'required|array|min:1',
            'qtys' => 'required|array',
            'qtys.*' => 'required|numeric|min:0.01',
        ]);

        // VALIDASI SERVER-SIDE: Cek duplikasi bahan_id dalam satu request
        if (count($request->bahan_ids) !== count(array_unique($request->bahan_ids))) {
            return redirect()->back()->with('error', 'Ada bahan yang duplikat dalam daftar!')->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
                // 1. Simpan Master
                $pengambilan = PengambilanBahan::create([
                    'tanggal' => $request->tanggal,
                    'kategori_pola' => $request->kategori_pola,
                    'keterangan' => $request->keterangan ?? 'Pengambilan produksi pola ' . $request->kategori_pola,
                ]);

                foreach ($request->bahan_ids as $index => $bahanId) {
                    $qtyAmbil = $request->qtys[$index];
                    
                    $stok = StokBahan::where('bahan_id', $bahanId)->first();
                    $stokLama = $stok ? $stok->jumlah : 0;

                    if ($stokLama < $qtyAmbil) {
                        throw new \Exception("Stok bahan ID $bahanId tidak mencukupi!");
                    }

                    // 2. Simpan Detail
                    PengambilanBahanDetail::create([
                        'pengambilan_id' => $pengambilan->id,
                        'bahan_id' => $bahanId,
                        'qty' => $qtyAmbal ?? $qtyAmbil, 
                    ]);

                    // 3. Update Stok
                    $stokBaru = $stokLama - $qtyAmbil;
                    $stok->update(['jumlah' => $stokBaru]);

                    // 4. Stock Log
                    StockLog::create([
                        'item_id' => $bahanId,
                        'item_type' => 'bahan_baku',
                        'jenis' => 'keluar',
                        'jumlah' => $qtyAmbil,
                        'stok_sebelum' => $stokLama,
                        'stok_sesudah' => $stokBaru,
                        'sumber' => 'produksi',
                        'keterangan' => "Pengambilan pola {$request->kategori_pola} (#{$pengambilan->id})",
                        'user_id' => auth()->id()
                    ]);
                }
            });

            // PERBAIKAN: Pastikan mengarah ke 'pengambilan.index' sesuai route Anda
            return redirect()->route('pengambilan.index')->with('success', 'Berhasil mencatat pengambilan bahan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $pengambilan = PengambilanBahan::findOrFail($id);
                foreach ($pengambilan->details as $detail) {
                    $stok = StokBahan::where('bahan_id', $detail->bahan_id)->first();
                    if ($stok) {
                        $stokLama = $stok->jumlah;
                        $stokBaru = $stokLama + $detail->qty;
                        $stok->update(['jumlah' => $stokBaru]);

                        StockLog::create([
                            'item_id' => $detail->bahan_id,
                            'item_type' => 'bahan_baku',
                            'jenis' => 'masuk',
                            'jumlah' => $detail->qty,
                            'stok_sebelum' => $stokLama,
                            'stok_sesudah' => $stokBaru,
                            'sumber' => 'manual',
                            'keterangan' => "Revert stok: Penghapusan pengambilan #{$id}",
                            'user_id' => auth()->id()
                        ]);
                    }
                }
                $pengambilan->delete();
            });
            return redirect()->back()->with('success', 'Data berhasil dihapus dan stok dikembalikan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}