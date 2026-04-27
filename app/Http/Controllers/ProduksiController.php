<?php

namespace App\Http\Controllers;

use App\Helpers\StokHelper;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\Produksi;
use App\Models\ProduksiDetail;
use App\Models\StokBahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduksiController extends Controller
{
    public function index()
    {
        $produksi = Produksi::with(['detail.produk'])->latest()->paginate(10);
        return view('admin.produksi.index', compact('produksi'));
    }

    public function create()
    {
        $bahan = BahanBaku::orderBy('nama', 'asc')->get();
        $produk = Produk::orderBy('nama', 'asc')->get();
        return view('admin.produksi.create', compact('bahan', 'produk'));
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

            // 1. Create Header (Status default: proses)
            $produksi = Produksi::create([
                'tanggal' => $request->tanggal,
                'status' => 'proses',
                'keterangan' => $request->keterangan
            ]);

            // 2. Simpan Bahan Baku & Kurangi Stok Langsung (Karena sudah dipakai produksi)
            foreach ($request->bahan_ids as $index => $bahanId) {
                $qtyBahan = $request->bahan_qtys[$index];
                $itemBahan = BahanBaku::findOrFail($bahanId);
                $stok = StokBahan::where('bahan_id', $bahanId)->first();

                if (!$stok || $stok->jumlah < $qtyBahan) {
                    throw new \Exception("Stok bahan {$itemBahan->nama} tidak mencukupi.");
                }

                ProduksiDetail::create([
                    'produksi_id' => $produksi->id,
                    'jenis' => 'bahan',
                    'item_id' => $bahanId,
                    'qty' => $qtyBahan
                ]);

                $stok->decrement('jumlah', $qtyBahan);
            }

            // 3. Simpan Hasil Produk (Belum tambah stok karena status masih 'proses')
            foreach ($request->produk_ids as $index => $produkId) {
                ProduksiDetail::create([
                    'produksi_id' => $produksi->id,
                    'jenis' => 'produk',
                    'item_id' => $produkId,
                    'qty' => $request->produk_qtys[$index]
                ]);
            }

            DB::commit();
            return redirect()->route('produksi.index')->with('success', 'Produksi dimulai. Status: Sedang Proses.');

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

            // Validasi: Hanya status 'proses' atau 'reject' yang masuk akal untuk dicancel/rollback
            // Jika sudah 'berhasil', cancel berarti harus mengurangi stok produk juga.
            if ($produksi->status == 'cancel') {
                return back()->with('error', 'Transaksi ini sudah dibatalkan.');
            }

            // 1. Kembalikan Stok Bahan Baku
            foreach ($produksi->detail->where('jenis', 'bahan') as $detail) {
                $stok = StokBahan::where('bahan_id', $detail->item_id)->first();
                if ($stok) {
                    $stok->increment('jumlah', $detail->qty);
                }
            }

            // 2. Jika status sebelumnya 'berhasil', maka kurangi stok produk yang sudah terlanjur bertambah
            if ($produksi->status == 'berhasil') {
                foreach ($produksi->detail->where('jenis', 'produk') as $detail) {
                    StokHelper::updateStokProduk($detail->item_id, -$detail->qty);
                }
            }

            // 3. Update status
            $produksi->update(['status' => 'cancel']);

            DB::commit();
            return back()->with('success', 'Produksi dibatalkan. Stok bahan baku telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $produksi = Produksi::with(['detail.bahan', 'detail.produk'])->findOrFail($id);
        return view('admin.produksi.show', compact('produksi'));
    }

    public function updateStatus(Request $request, $id)
    {
        $produksi = Produksi::with('detail')->findOrFail($id);

        if ($produksi->status !== 'proses') {
            return redirect()->back()->with('error', 'Status produksi sudah tidak dapat diubah.');
        }

        try {
            DB::beginTransaction();

            if ($request->status === 'berhasil') {
                $produksi->update(['status' => 'berhasil']);
                // Tambah stok produk hasil
                foreach ($produksi->detail->where('jenis', 'produk') as $item) {
                    StokHelper::updateStokProduk($item->item_id, $item->qty);
                }
                $message = 'Produksi selesai. Stok produk telah bertambah.';
            } elseif ($request->status === 'reject') {
                $produksi->update([
                    'status' => 'reject',
                    'keterangan' => $request->keterangan ?? 'Dibatalkan melalui halaman detail.'
                ]);
                $message = 'Produksi ditandai Gagal (Reject). Stok bahan tidak kembali (dianggap hangus).';
            }

            DB::commit();
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    // Repair functions tetap sama sesuai kebutuhan Anda

public function repair($id)
{
    $produksi = Produksi::with(['detail.bahan', 'detail.produk'])->findOrFail($id);
    
    // Pastikan hanya status reject yang bisa di-repair
    if ($produksi->status !== 'reject') {
        return redirect()->back()->with('error', 'Hanya produksi gagal yang dapat diperbaiki.');
    }

    $bahan = BahanBaku::orderBy('nama', 'asc')->get();
    return view('admin.produksi.repair', compact('produksi', 'bahan'));
}

public function repairStore(Request $request, $id)
{
    $request->validate([
        'tanggal' => 'required|date',
        'produk_detail_ids' => 'required|array',
        'produk_qtys' => 'required|array',
        'keterangan' => 'required|string',
    ]);

    try {
        DB::beginTransaction();

        $produksi = Produksi::with('detail')->findOrFail($id);

        if ($produksi->status !== 'reject') {
            throw new \Exception("Hanya produksi status Reject yang dapat diperbaiki.");
        }

        // 1. Update Jumlah Hasil Produk di Detail Produksi
        foreach ($request->produk_detail_ids as $index => $detailId) {
            $newQty = $request->produk_qtys[$index];
            $detailProduk = ProduksiDetail::where('id', $detailId)
                                        ->where('produksi_id', $id)
                                        ->first();
            
            if ($detailProduk) {
                $detailProduk->update(['qty' => $newQty]);
            }
        }

        // 2. Simpan bahan tambahan (Repair) jika ada
        if ($request->has('bahan_ids')) {
            foreach ($request->bahan_ids as $index => $bahanId) {
                $qtyBahan = $request->bahan_qtys[$index];
                
                $stok = StokBahan::where('bahan_id', $bahanId)->first();
                if (!$stok || $stok->jumlah < $qtyBahan) {
                    $namaBahan = BahanBaku::find($bahanId)->nama ?? 'Bahan';
                    throw new \Exception("Stok bahan tambahan ($namaBahan) tidak mencukupi.");
                }

                // Tambah detail baru sebagai record penggunaan bahan repair
                ProduksiDetail::create([
                    'produksi_id' => $produksi->id,
                    'jenis' => 'bahan',
                    'item_id' => $bahanId,
                    'qty' => $qtyBahan,
                    'keterangan' => 'Bahan perbaikan (Repair)'
                ]);

                // Kurangi stok bahan baku
                $stok->decrement('jumlah', $qtyBahan);
            }
        }

        // 3. Update Header Produksi menjadi Berhasil
        $produksi->update([
            'status' => 'berhasil',
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan
        ]);

        // 4. Update Stok Produk Jadi (Gunakan Qty yang baru saja di-update)
        // Load ulang detail untuk mendapatkan qty terbaru
        $produksi->load('detail');
        foreach ($produksi->detail->where('jenis', 'produk') as $item) {
            \App\Helpers\StokHelper::updateStokProduk($item->item_id, $item->qty);
        }

        DB::commit();
        return redirect()->route('produksi.show', $id)->with('success', 'Repair selesai. Jumlah produk disesuaikan dan stok telah ditambahkan.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }
}
}
