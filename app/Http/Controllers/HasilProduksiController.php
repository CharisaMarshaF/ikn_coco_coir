<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokProduk;
use App\Models\HasilProduksi;
use App\Models\HasilProduksiDetail;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilProduksiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $hasilProduksi = HasilProduksi::with(['details.produk', 'user'])
            ->when($search, function($query) use ($search) {
                $query->where('kode_produksi', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);
        
        $title = 'Data Hasil Produksi';
        return view('admin.hasil_produksi.index', compact('hasilProduksi', 'title'));
    }

    public function create()
    {
        // Hanya ambil produk yang jenisnya 'proses'
        $produkProses = Produk::with('stok')
            ->where('jenis', 'proses')
            ->get();
            
        $title = 'Catat Hasil Produksi';
        return view('admin.hasil_produksi.create', compact('produkProses', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $hasil = HasilProduksi::create([
                'tanggal' => $request->tanggal,
                'kode_produksi' => 'HPR-' . date('YmdHis'),
                'keterangan' => $request->keterangan,
                'user_id' => auth()->id()
            ]);

            foreach ($request->items as $item) {
                $produkId = $item['produk_id'];
                $qtyMasuk = $item['qty'];

                // 1. Simpan Detail
                HasilProduksiDetail::create([
                    'hasil_produksi_id' => $hasil->id,
                    'produk_id' => $produkId,
                    'qty' => $qtyMasuk,
                ]);

                // 2. Update Stok Fisik Produk
                $stokRecord = StokProduk::firstOrCreate(
                    ['produk_id' => $produkId],
                    ['jumlah' => 0]
                );
                
                $stokLama = $stokRecord->jumlah;
                $stokBaru = $stokLama + $qtyMasuk;
                
                $stokRecord->update(['jumlah' => $stokBaru]);

                // 3. Catat ke StockLog (PENTING: item_type = produk)
                StockLog::create([
                    'item_id' => $produkId,
                    'item_type' => 'produk',
                    'jenis' => 'masuk',
                    'jumlah' => $qtyMasuk,
                    'stok_sebelum' => $stokLama,
                    'stok_sesudah' => $stokBaru,
                    'sumber' => 'produksi',
                    'keterangan' => "Hasil Produksi #{$hasil->kode_produksi}",
                    'user_id' => auth()->id()
                ]);
            }

            DB::commit();
            return redirect()->route('hasil-produksi.index')->with('success', 'Hasil produksi berhasil dicatat dan stok bertambah.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $hasil = HasilProduksi::with('details')->findOrFail($id);

            foreach ($hasil->details as $detail) {
                $stokRecord = StokProduk::where('produk_id', $detail->produk_id)->first();
                $stokLama = $stokRecord->jumlah ?? 0;
                $stokBaru = $stokLama - $detail->qty;

                // Update Stok (Dikurangi karena data produksi dihapus)
                if ($stokRecord) {
                    $stokRecord->update(['jumlah' => $stokBaru]);
                }

                // Log Pembatalan
                StockLog::create([
                    'item_id' => $detail->produk_id,
                    'item_type' => 'produk',
                    'jenis' => 'keluar',
                    'jumlah' => $detail->qty,
                    'stok_sebelum' => $stokLama,
                    'stok_sesudah' => $stokBaru,
                    'sumber' => 'produksi',
                    'keterangan' => "Penghapusan Data Produksi #{$hasil->kode_produksi}",
                    'user_id' => auth()->id()
                ]);
            }

            $hasil->delete();
            DB::commit();
            return back()->with('success', 'Data berhasil dihapus & stok dikoreksi.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}