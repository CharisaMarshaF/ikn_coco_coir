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
        $query = Penjualan::with(['client' => function($q) {
                $q->withTrashed();
            }])
            // Filter untuk hanya menampilkan data bulan ini
            ->whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            // Urutkan berdasarkan tanggal terbaru, lalu waktu input terbaru
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

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
        $title = 'Data Penjualan Bulan Ini';

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
    // Contoh pada fungsi show
    public function show($id)
    {
        $penjualan = Penjualan::with([
            'client' => function($q) { $q->withTrashed(); },
            'detail.produk' => function($q) { $q->withTrashed(); } // TAMBAHKAN INI
        ])->findOrFail($id);
        
        $title = 'Detail Penjualan';
        return view('admin.penjualan.show', compact('penjualan', 'title'));
    }

    public function cancel($id)
    {
        try {
            DB::beginTransaction();
            
            // Ambil data penjualan beserta detailnya
            $penjualan = Penjualan::with('detail')->findOrFail($id);

            // Proteksi: Hanya transaksi yang sudah berhasil yang bisa dicancel
            if ($penjualan->status != 'berhasil') {
                throw new \Exception("Hanya transaksi dengan status 'berhasil' yang dapat dibatalkan.");
            }

            foreach ($penjualan->detail as $item) {
                // PENTING: Gunakan withTrashed() agar stok tetap bisa diupdate 
                // meskipun produknya sudah di-soft delete
                $stokRecord = StokProduk::withTrashed()->where('produk_id', $item->produk_id)->first();
                
                $stokLama = $stokRecord->jumlah ?? 0;

                // 1. Kembalikan stok fisik menggunakan Helper
                StokHelper::updateStokProduk($item->produk_id, $item->qty);

                // 2. Catat riwayat ke StockLog
                StockLog::create([
                    'item_id'      => $item->produk_id,
                    'item_type'    => 'produk',
                    'jenis'        => 'masuk',
                    'jumlah'       => $item->qty,
                    'stok_sebelum' => $stokLama,
                    'stok_sesudah' => $stokLama + $item->qty,
                    'sumber'       => 'pembatalan', 
                    'keterangan'   => "Pembatalan Penjualan #{$penjualan->id}",
                    'user_id'      => auth()->id()
                ]);
            }

            // 3. Update status penjualan
            $penjualan->update(['status' => 'cancel']);

            DB::commit();
            return back()->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
    public function return(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $penjualan = Penjualan::with('detail')->findOrFail($id);

            foreach ($request->items as $itemData) {
                $qtyReturn = (float)$itemData['qty_return'];

                // Hanya proses jika jumlah return lebih dari 0
                if ($qtyReturn > 0) {
                    // Cari detail penjualan terkait
                    $detail = PenjualanDetail::where('penjualan_id', $id)
                        ->where('produk_id', $itemData['produk_id'])
                        ->first();

                    if ($detail) {
                        // Validasi agar tidak return melebihi yang dibeli
                        if ($qtyReturn > $detail->qty) {
                            throw new \Exception("Jumlah return untuk produk ID {$itemData['produk_id']} melebihi jumlah pembelian.");
                        }

                        // PENTING: Cari stok dengan withTrashed()
                        $stokRecord = StokProduk::withTrashed()->where('produk_id', $itemData['produk_id'])->first();
                        $stokLama = $stokRecord->jumlah ?? 0;

                        // 1. Kembalikan stok ke gudang
                        StokHelper::updateStokProduk($itemData['produk_id'], $qtyReturn);

                        // 2. Update Qty di Detail Penjualan (dikurangi)
                        $qtySisa = $detail->qty - $qtyReturn;
                        $detail->update([
                            'qty'      => $qtySisa,
                            'subtotal' => $qtySisa * $detail->harga
                        ]);

                        // 3. Catat StockLog
                        StockLog::create([
                            'item_id'      => $itemData['produk_id'],
                            'item_type'    => 'produk',
                            'jenis'        => 'masuk',
                            'jumlah'       => $qtyReturn,
                            'stok_sebelum' => $stokLama,
                            'stok_sesudah' => $stokLama + $qtyReturn,
                            'sumber'       => 'manual', 
                            'keterangan'   => "Return barang dari Penjualan #{$penjualan->id}",
                            'user_id'      => auth()->id()
                        ]);
                    }
                }
            }

            // 4. Hitung ulang total penjualan dari sisa detail yang ada
            $totalBaru = PenjualanDetail::where('penjualan_id', $id)->sum('subtotal');

            // 5. Update Header Penjualan
            $penjualan->update([
                'status'     => 'return',
                'total'      => $totalBaru,
                'keterangan' => $request->keterangan ?? "Proses return sebagian barang"
            ]);

            // 6. Sinkronkan dengan Invoice jika ada
            if ($penjualan->invoice) {
                $penjualan->invoice->update(['total' => $totalBaru]);
            }

            DB::commit();
            return back()->with('success', 'Berhasil memproses return. Stok dan total tagihan telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses return: ' . $e->getMessage());
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