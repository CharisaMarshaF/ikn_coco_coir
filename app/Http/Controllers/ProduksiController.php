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
        $produksi = Produksi::with('detail')->latest()->paginate(10);
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
        'status' => 'required|in:proses,berhasil,reject',
        'bahan_ids' => 'required|array',
        'produk_ids' => 'required|array',
    ]);

    try {
        DB::beginTransaction();

        // 1. Create Produksi Header
        $produksi = Produksi::create([
            'tanggal' => $request->tanggal,
            'status' => $request->status,
            'keterangan' => $request->keterangan
        ]);

        // 2. Simpan Bahan Baku (Looping berdasarkan index array)
        foreach ($request->bahan_ids as $index => $bahanId) {
            $qtyBahan = $request->bahan_qtys[$index];

            $itemBahan = BahanBaku::findOrFail($bahanId);
            $stok = StokBahan::where('bahan_id', $bahanId)->first();

            // Validasi Stok
            if (!$stok || $stok->jumlah < $qtyBahan) {
                throw new \Exception("Stok bahan {$itemBahan->nama} tidak mencukupi.");
            }

            // Simpan Detail
            ProduksiDetail::create([
                'produksi_id' => $produksi->id,
                'jenis' => 'bahan',
                'item_id' => $bahanId,
                'qty' => $qtyBahan
            ]);

            // Kurangi Stok Bahan
            $stok->decrement('jumlah', $qtyBahan);
        }

        // 3. Simpan Hasil Produk
        foreach ($request->produk_ids as $index => $produkId) {
            $qtyProduk = $request->produk_qtys[$index];

            ProduksiDetail::create([
                'produksi_id' => $produksi->id,
                'jenis' => 'produk',
                'item_id' => $produkId,
                'qty' => $qtyProduk
            ]);

            // Update stok jika status BERHASIL
            if ($request->status === 'berhasil') {
                \App\Helpers\StokHelper::updateStokProduk($produkId, $qtyProduk);
            }
        }

        DB::commit();
        return redirect()->route('produksi.index')->with('success', 'Data produksi berhasil disimpan');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->with('error', $e->getMessage());
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

    // Validasi: Hanya status 'proses' yang bisa diubah
    if ($produksi->status !== 'proses') {
        return redirect()->back()->with('error', 'Status produksi sudah tidak dapat diubah.');
    }

    try {
        DB::beginTransaction();

        if ($request->status === 'berhasil') {
            // 1. Update status ke berhasil
            $produksi->update(['status' => 'berhasil']);

            // 2. Tambah stok produk
            foreach ($produksi->detail->where('jenis', 'produk') as $item) {
                \App\Helpers\StokHelper::updateStokProduk($item->item_id, $item->qty);
            }
            $message = 'Produksi berhasil diselesaikan dan stok telah ditambahkan.';

        } elseif ($request->status === 'reject') {
            // 1. Update status ke reject & simpan keterangan jika ada
            $produksi->update([
                'status' => 'reject',
                'keterangan' => $request->keterangan ?? 'Dibatalkan melalui halaman detail.'
            ]);
            $message = 'Produksi telah ditandai sebagai Reject/Gagal.';
        }

        DB::commit();
        return redirect()->back()->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
    return view('admin.produksi.repair', compact('produksi', 'bahan'));
}

public function repairStore(Request $request, $id)
{
    $request->validate([
        'tanggal' => 'required|date',
        'bahan_ids' => 'nullable|array',
    ]);

    try {
        DB::beginTransaction();

        $produksi = Produksi::findOrFail($id);

        // 1. Simpan bahan tambahan jika ada
        if ($request->has('bahan_ids')) {
            foreach ($request->bahan_ids as $index => $bahanId) {
                $qtyBahan = $request->bahan_qtys[$index];
                
                $stok = StokBahan::where('bahan_id', $bahanId)->first();
                if (!$stok || $stok->jumlah < $qtyBahan) {
                    throw new \Exception("Stok bahan tambahan tidak mencukupi.");
                }

                ProduksiDetail::create([
                    'produksi_id' => $produksi->id,
                    'jenis' => 'bahan',
                    'item_id' => $bahanId,
                    'qty' => $qtyBahan,
                    'keterangan' => 'Bahan perbaikan (Repair)'
                ]);

                $stok->decrement('jumlah', $qtyBahan);
            }
        }

        // 2. Update status jadi Berhasil dan Update Stok Produk
        $produksi->update([
            'status' => 'berhasil',
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan ?? 'Berhasil diperbaiki (Repair)'
        ]);

        foreach ($produksi->detail->where('jenis', 'produk') as $item) {
            \App\Helpers\StokHelper::updateStokProduk($item->item_id, $item->qty);
        }

        DB::commit();
        return redirect()->route('produksi.show', $id)->with('success', 'Produksi berhasil diperbaiki dan stok ditambahkan.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
    }
}
}
