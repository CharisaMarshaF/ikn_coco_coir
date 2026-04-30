<?php

namespace App\Http\Controllers;

use App\Helpers\StokHelper;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\Produksi;
use App\Models\ProduksiDetail;
use App\Models\StockLog;
use App\Models\StokBahan;
use App\Models\StokProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduksiController extends Controller
{
    public function index()
    {
        $produksi = Produksi::with(['detail.produk'])->latest()->paginate(10);
        $title = 'Data Produksi';
        return view('admin.produksi.index', compact('produksi', 'title'));
    }

    public function create()
    {
        $bahan = BahanBaku::select('id', 'nama', 'satuan')->orderBy('nama', 'asc')->get();
        $produk = Produk::select('id', 'nama', 'satuan')->orderBy('nama', 'asc')->get();
        $title = 'Tambah Produksi';
        return view('admin.produksi.create', compact('bahan', 'produk', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'bahan_ids' => 'required|array',
            'produk_ids' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $produksi = Produksi::create([
                'tanggal' => $request->tanggal,
                'status' => 'proses',
                'keterangan' => $request->keterangan
            ]);

            // 1. Kurangi Stok Bahan Baku & Catat Log
            foreach ($request->bahan_ids as $index => $bahanId) {
                $qtyBahan = $request->bahan_qtys[$index];
                $stok = StokBahan::where('bahan_id', $bahanId)->first();

                if (!$stok || $stok->jumlah < $qtyBahan) {
                    $itemBahan = BahanBaku::find($bahanId);
                    throw new \Exception("Stok bahan {$itemBahan->nama} tidak mencukupi.");
                }

                $stokLama = $stok->jumlah;

                ProduksiDetail::create([
                    'produksi_id' => $produksi->id,
                    'jenis' => 'bahan',
                    'item_id' => $bahanId,
                    'qty' => $qtyBahan
                ]);

                $stok->decrement('jumlah', $qtyBahan);

                // LOG: Bahan Baku Keluar
                StockLog::create([
                    'item_id' => $bahanId,
                    'item_type' => 'bahan_baku',
                    'jenis' => 'keluar',
                    'jumlah' => $qtyBahan,
                    'stok_sebelum' => $stokLama,
                    'stok_sesudah' => $stokLama - $qtyBahan,
                    'sumber' => 'produksi',
                    'keterangan' => "Digunakan untuk Produksi #{$produksi->id}",
                    'user_id' => auth()->id()
                ]);
            }

            // 2. Simpan Target Produk (Belum tambah stok, belum ada log)
            foreach ($request->produk_ids as $index => $produkId) {
                ProduksiDetail::create([
                    'produksi_id' => $produksi->id,
                    'jenis' => 'produk',
                    'item_id' => $produkId,
                    'qty' => $request->produk_qtys[$index]
                ]);
            }

            DB::commit();
            return redirect()->route('produksi.index')->with('success', 'Produksi dimulai. Stok bahan baku telah dikurangi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function cancel($id)
    {
        try {
            DB::beginTransaction();
            $produksi = Produksi::with('detail')->findOrFail($id);

            if ($produksi->status == 'cancel') {
                return back()->with('error', 'Transaksi sudah dibatalkan.');
            }

            // 1. Kembalikan Stok Bahan Baku & Log
            foreach ($produksi->detail->where('jenis', 'bahan') as $detail) {
                $stok = StokBahan::where('bahan_id', $detail->item_id)->first();
                if ($stok) {
                    $stokLama = $stok->jumlah;
                    $stok->increment('jumlah', $detail->qty);

                    StockLog::create([
                        'item_id' => $detail->item_id,
                        'item_type' => 'bahan_baku',
                        'jenis' => 'masuk',
                        'jumlah' => $detail->qty,
                        'stok_sebelum' => $stokLama,
                        'stok_sesudah' => $stokLama + $detail->qty,
                        'sumber' => 'pembatalan',
                        'keterangan' => "Pembatalan Produksi #{$produksi->id}",
                        'user_id' => auth()->id()
                    ]);
                }
            }

            // 2. Jika sudah 'berhasil', kurangi stok produk & Log
            if ($produksi->status == 'berhasil') {
                foreach ($produksi->detail->where('jenis', 'produk') as $detail) {
                    $stokRecord = StokProduk::where('produk_id', $detail->item_id)->first();
                    $stokLama = $stokRecord->jumlah ?? 0;

                    StokHelper::updateStokProduk($detail->item_id, -$detail->qty);

                    StockLog::create([
                        'item_id' => $detail->item_id,
                        'item_type' => 'produk',
                        'jenis' => 'keluar',
                        'jumlah' => $detail->qty,
                        'stok_sebelum' => $stokLama,
                        'stok_sesudah' => $stokLama - $detail->qty,
                        'sumber' => 'pembatalan',
                        'keterangan' => "Pembatalan Produksi #{$produksi->id}",
                        'user_id' => auth()->id()
                    ]);
                }
            }

            $produksi->update(['status' => 'cancel']);
            DB::commit();
            return back()->with('success', 'Produksi dibatalkan. Stok telah dikoreksi.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $produksi = Produksi::with(['detail.bahan', 'detail.produk'])->findOrFail($id);
        $title = 'Detail Produksi';
        return view('admin.produksi.show', compact('produksi', 'title'));
    }

    public function updateStatus(Request $request, $id)
    {
        $produksi = Produksi::with('detail')->findOrFail($id);
        if ($produksi->status !== 'proses') {
            return redirect()->back()->with('error', 'Status sudah tidak dapat diubah.');
        }

        try {
            DB::beginTransaction();

            if ($request->status === 'berhasil') {
                $produksi->update(['status' => 'berhasil']);
                
                // Tambah stok produk & Log
                foreach ($produksi->detail->where('jenis', 'produk') as $item) {
                    $stokRecord = StokProduk::where('produk_id', $item->item_id)->first();
                    $stokLama = $stokRecord->jumlah ?? 0;

                    StokHelper::updateStokProduk($item->item_id, $item->qty);

                    // LOG: Produk Masuk
                    StockLog::create([
                        'item_id' => $item->item_id,
                        'item_type' => 'produk',
                        'jenis' => 'masuk',
                        'jumlah' => $item->qty,
                        'stok_sebelum' => $stokLama,
                        'stok_sesudah' => $stokLama + $item->qty,
                        'sumber' => 'produksi',
                        'keterangan' => "Hasil Produksi #{$produksi->id}",
                        'user_id' => auth()->id()
                    ]);
                }
                $message = 'Produksi selesai. Stok produk bertambah.';
            } elseif ($request->status === 'reject') {
                $produksi->update(['status' => 'reject', 'keterangan' => $request->keterangan ?? 'Gagal produksi.']);
                $message = 'Produksi Gagal. Bahan baku dianggap hangus.';
            }

            DB::commit();
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function repair($id)
    {
        $produksi = Produksi::with(['detail.bahan', 'detail.produk'])->findOrFail($id);
        
        // Pastikan hanya status reject yang bisa di-repair
        if ($produksi->status !== 'reject') {
            return redirect()->back()->with('error', 'Hanya produksi gagal yang dapat diperbaiki.');
        }

        $bahan = BahanBaku::orderBy('nama', 'asc')->get();
        $title = 'Perbaiki Produksi';
        return view('admin.produksi.repair', compact('produksi', 'bahan', 'title'));
    }

    public function repairStore(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $produksi = Produksi::with('detail')->findOrFail($id);

            // 1. Update stok bahan tambahan (jika ada) & Log
            if ($request->has('bahan_ids')) {
                foreach ($request->bahan_ids as $index => $bahanId) {
                    $qtyBahan = $request->bahan_qtys[$index];
                    $stok = StokBahan::where('bahan_id', $bahanId)->first();
                    
                    if (!$stok || $stok->jumlah < $qtyBahan) throw new \Exception("Stok bahan tambahan tidak cukup.");

                    $stokLama = $stok->jumlah;
                    $stok->decrement('jumlah', $qtyBahan);

                    ProduksiDetail::create([
                        'produksi_id' => $produksi->id, 'jenis' => 'bahan', 'item_id' => $bahanId,
                        'qty' => $qtyBahan, 'keterangan' => 'Bahan perbaikan (Repair)'
                    ]);

                    StockLog::create([
                        'item_id' => $bahanId, 'item_type' => 'bahan_baku', 'jenis' => 'keluar',
                        'jumlah' => $qtyBahan, 'stok_sebelum' => $stokLama, 'stok_sesudah' => $stokLama - $qtyBahan,
                        'sumber' => 'produksi', 'keterangan' => "Bahan tambahan repair Produksi #{$id}", 'user_id' => auth()->id()
                    ]);
                }
            }

            // 2. Update status & Stok Produk & Log
            $produksi->update(['status' => 'berhasil', 'tanggal' => $request->tanggal]);
            
            foreach ($request->produk_detail_ids as $idx => $detailId) {
                $newQty = $request->produk_qtys[$idx];
                $detail = ProduksiDetail::find($detailId);
                $detail->update(['qty' => $newQty]);

                $stokRecord = StokProduk::where('produk_id', $detail->item_id)->first();
                $stokLama = $stokRecord->jumlah ?? 0;

                StokHelper::updateStokProduk($detail->item_id, $newQty);

                StockLog::create([
                    'item_id' => $detail->item_id, 'item_type' => 'produk', 'jenis' => 'masuk',
                    'jumlah' => $newQty, 'stok_sebelum' => $stokLama, 'stok_sesudah' => $stokLama + $newQty,
                    'sumber' => 'produksi', 'keterangan' => "Hasil Repair Produksi #{$id}", 'user_id' => auth()->id()
                ]);
            }

            DB::commit();
            return redirect()->route('produksi.show', $id)->with('success', 'Repair Berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
