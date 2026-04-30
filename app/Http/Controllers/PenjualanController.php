<?php

namespace App\Http\Controllers;

use App\Helpers\StokHelper;
use App\Models\Client;
use App\Models\CompanyProfile;
use App\Models\Invoice;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\StockLog;
use App\Models\StokProduk;
use App\Models\SuratJalan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with('client')->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($qc) use ($search) {
                      $qc->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $penjualan = $query->paginate(10)->withQueryString();
        $title = 'Data Penjualan';
        return view('admin.penjualan.index', compact('penjualan', 'title'));
    }

    public function create()
    {
        $clients = Client::select('id', 'nama')->get();
        $produk = Produk::with('stok')->get(); 
        $title = 'Tambah Penjualan';
        return view('admin.penjualan.create', compact('clients', 'produk', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.harga' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $penjualan = Penjualan::create([
                'client_id' => $request->client_id,
                'tanggal'   => $request->tanggal,
                'total'     => $request->total,
                'status'    => 'berhasil',
            ]);

            foreach ($request->items as $item) {
                // 1. Ambil stok lama untuk Log
                $stokRecord = StokProduk::where('produk_id', $item['produk_id'])->first();
                $stokLama = $stokRecord->jumlah ?? 0;

                // 2. Simpan Detail
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id'    => $item['produk_id'],
                    'qty'          => $item['qty'],
                    'harga'        => $item['harga'],
                    'subtotal'     => $item['qty'] * $item['harga'],
                ]);

                // 3. Update Stok Fisik
                StokHelper::updateStokProduk($item['produk_id'], -$item['qty']);

                // 4. Catat ke StockLog (Keluar)
                StockLog::create([
                    'item_id' => $item['produk_id'],
                    'item_type' => 'produk',
                    'jenis' => 'keluar',
                    'jumlah' => $item['qty'],
                    'stok_sebelum' => $stokLama,
                    'stok_sesudah' => $stokLama - $item['qty'],
                    'sumber' => 'penjualan',
                    'keterangan' => "Penjualan ke Client ID: {$request->client_id} (ID Transaksi: #{$penjualan->id})",
                    'user_id' => auth()->id()
                ]);
            }

            // Generate Dokumen Pendukung
            Invoice::create([
                'penjualan_id' => $penjualan->id,
                'nomor'        => 'INV-' . date('Ymd') . $penjualan->id,
                'tanggal'      => $request->tanggal,
                'total'        => $request->total,
                'status_bayar' => 'lunas',
            ]);

            SuratJalan::create([
                'penjualan_id' => $penjualan->id,
                'nomor'        => 'SJ-' . date('Ymd') . $penjualan->id,
                'tanggal'      => $request->tanggal,
                'status_kirim' => 'diterima',
            ]);

            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Transaksi Berhasil.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $penjualan = Penjualan::findOrFail($id);
            $penjualan->delete();

            return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    // Menampilkan Detail
    public function show($id)
    {
        $penjualan = Penjualan::with(['client', 'detail.produk'])->findOrFail($id);
        $title = 'Detail Penjualan';
        return view('admin.penjualan.show', compact('penjualan', 'title'));
    }

// Logika CANCEL (Stok kembali semua)
    public function cancel($id)
    {
        try {
            DB::beginTransaction();
            $penjualan = Penjualan::with('detail')->findOrFail($id);

            if ($penjualan->status != 'berhasil') {
                throw new \Exception("Hanya transaksi 'Berhasil' yang dapat dibatalkan.");
            }

            foreach ($penjualan->detail as $item) {
                $stokRecord = StokProduk::where('produk_id', $item->produk_id)->first();
                $stokLama = $stokRecord->jumlah ?? 0;

                // Kembalikan stok
                StokHelper::updateStokProduk($item->produk_id, $item->qty);

                // Log: Masuk (karena pembatalan jual)
                StockLog::create([
                    'item_id' => $item->produk_id,
                    'item_type' => 'produk',
                    'jenis' => 'masuk',
                    'jumlah' => $item->qty,
                    'stok_sebelum' => $stokLama,
                    'stok_sesudah' => $stokLama + $item->qty,
                    'sumber' => 'pembatalan', // Pastikan 'pembatalan' ada di Enum DB Anda
                    'keterangan' => "Pembatalan Penjualan #{$penjualan->id}",
                    'user_id' => auth()->id()
                ]);
            }

            $penjualan->update(['status' => 'cancel']);

            DB::commit();
            return back()->with('success', 'Transaksi dibatalkan & stok dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

// Logika RETURN (Stok kembali sebagian sesuai pilihan)
public function return(Request $request, $id)
{
    try {
        DB::beginTransaction();
        $penjualan = Penjualan::with('detail')->findOrFail($id);
        $totalBaru = 0;

        foreach ($request->items as $itemData) {
            $qtyReturn = (float)$itemData['qty_return'];

            if ($qtyReturn > 0) {
                // 1. Cari detail penjualan yang sesuai
                $detail = PenjualanDetail::where('penjualan_id', $id)
                    ->where('produk_id', $itemData['produk_id'])
                    ->first();

                if ($detail) {
                    // Validasi: Jangan sampai return melebihi qty yang dibeli
                    if ($qtyReturn > $detail->qty) {
                        throw new \Exception("Jumlah return produk {$detail->produk->nama} melebihi jumlah pembelian.");
                    }

                    // 2. Ambil stok lama untuk Logging
                    $stokRecord = StokProduk::where('produk_id', $itemData['produk_id'])->first();
                    $stokLama = $stokRecord->jumlah ?? 0;

                    // 3. Kembalikan stok fisik ke gudang
                    StokHelper::updateStokProduk($itemData['produk_id'], $qtyReturn);

                    // 4. Update data Detail Penjualan (KURANGI QTY)
                    $qtySisa = $detail->qty - $qtyReturn;
                    $detail->update([
                        'qty' => $qtySisa,
                        'subtotal' => $qtySisa * $detail->harga
                    ]);

                    // Jika qty jadi 0 setelah return, opsional: hapus detail atau biarkan 0
                    // if ($qtySisa <= 0) { $detail->delete(); }

                    // 5. Catat ke StockLog
                    StockLog::create([
                        'item_id' => $itemData['produk_id'],
                        'item_type' => 'produk',
                        'jenis' => 'masuk',
                        'jumlah' => $qtyReturn,
                        'stok_sebelum' => $stokLama,
                        'stok_sesudah' => $stokLama + $qtyReturn,
                        'sumber' => 'manual', 
                        'keterangan' => "Return Produk dari Penjualan #{$penjualan->id}",
                        'user_id' => auth()->id()
                    ]);
                }
            }
        }

        // 6. Hitung ulang Total Penjualan berdasarkan detail yang sudah diupdate
        $totalBaru = PenjualanDetail::where('penjualan_id', $id)->sum('subtotal');

        // 7. Update Header Penjualan (Status dan Total Tagihan Baru)
        $penjualan->update([
            'status' => 'return',
            'total' => $totalBaru,
            'keterangan' => $request->keterangan ?? "Return sebagian barang"
        ]);

        // 8. Update data Invoice (opsional, agar invoice sinkron dengan total baru)
        if ($penjualan->invoice) {
            $penjualan->invoice->update(['total' => $totalBaru]);
        }

        DB::commit();
        return back()->with('success', 'Berhasil memproses return. Detail penjualan dan stok telah diperbarui.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}
    public function print($id)
    {
        $penjualan = Penjualan::with(['client', 'detail.produk', 'invoice', 'suratJalan'])->findOrFail($id);
        $title = 'Cetak Penjualan';
        $company = \App\Models\CompanyProfile::first(); 

        return view('admin.penjualan.print', compact('penjualan', 'company', 'title'));
    }

    public function downloadPDF($id, Request $request)
    {
        $type = $request->query('type', 'invoice');
        
        $penjualan = Penjualan::with(['client', 'invoice', 'suratJalan', 'detail' => function($query) {
            $query->where('qty', '>', 0)->with('produk');
        }])->findOrFail($id);
        
        $company = CompanyProfile::first();

        $customPaper = [0, 0, 595, 420]; 

        $pdf = Pdf::loadView('admin.penjualan.pdf', compact('penjualan', 'type', 'company'))
                ->setPaper($customPaper, 'landscape');

        $filename = ($type == 'sj' ? 'SJ-' : 'INV-') . $penjualan->id . '.pdf';
        return $pdf->stream($filename);
    }
}