<?php

namespace App\Http\Controllers;

use App\Helpers\StokHelper;
use App\Models\Client;
use App\Models\CompanyProfile;
use App\Models\Invoice;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\ReturnPenjualan;
use App\Models\StockLog;
use App\Models\StokProduk;
use App\Models\SuratJalan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with(['client' => function ($q) {
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
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($qc) use ($search) {
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
            'client' => function ($q) {
                $q->withTrashed();
            },
            'detail.produk' => function ($q) {
                $q->withTrashed();
            } // TAMBAHKAN INI
        ])->findOrFail($id);

        $title = 'Detail Penjualan';
        return view('admin.penjualan.show', compact('penjualan', 'title'));
    }

public function cancel($id)
{
    try {
        DB::beginTransaction();

        // 1. Ambil data penjualan beserta detail dan data return-nya
        $penjualan = Penjualan::with(['detail', 'returns.detail'])->findOrFail($id);

        // Proteksi: Hanya transaksi yang sudah berhasil atau status return yang bisa dicancel
        if (!in_array($penjualan->status, ['berhasil', 'return'])) {
            throw new \Exception("Transaksi ini tidak dapat dibatalkan (Status: {$penjualan->status}).");
        }

        // --- LOGIKA PENGEMBALIAN STOK DARI PENJUALAN ASLI ---
        foreach ($penjualan->detail as $item) {
            $stokRecord = StokProduk::withTrashed()->where('produk_id', $item->produk_id)->first();
            $stokLama = $stokRecord->jumlah ?? 0;

            // Kembalikan stok fisik yang keluar saat penjualan
            StokHelper::updateStokProduk($item->produk_id, $item->qty);

            // Catat riwayat ke StockLog
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

        // --- LOGIKA PEMBERSIHAN DATA RETURN ---
        if ($penjualan->returns->count() > 0) {
            foreach ($penjualan->returns as $returnHeader) {
                
                // A. Jika saat return barang dimasukkan ke stok, maka saat cancel stok harus dikurangi lagi
                // agar tidak terjadi double stok (karena stok penjualan asli sudah dikembalikan di atas)
                foreach ($returnHeader->detail as $retDetail) {
                    // Cek apakah produk ini ada recordnya
                    $stokRecordRet = StokProduk::withTrashed()->where('produk_id', $retDetail->produk_id)->first();
                    $stokLamaRet = $stokRecordRet->jumlah ?? 0;

                    // Kita kurangi stok sejumlah yang pernah di-return (karena di atas kita sudah mengembalikan FULL qty penjualan)
                    StokHelper::updateStokProduk($retDetail->produk_id, -$retDetail->qty);

                    // Catat Log Pengurangan (Penyesuaian akibat cancel return)
                    StockLog::create([
                        'item_id'      => $retDetail->produk_id,
                        'item_type'    => 'produk',
                        'jenis'        => 'keluar',
                        'jumlah'       => $retDetail->qty,
                        'stok_sebelum' => $stokLamaRet,
                        'stok_sesudah' => $stokLamaRet - $retDetail->qty,
                        'sumber'       => 'pembatalan',
                        'keterangan'   => "Koreksi Stok: Pembatalan Return dari Transaksi #{$penjualan->id}",
                        'user_id'      => auth()->id()
                    ]);
                }

                // B. Hapus Detail Return dan Header Return
                $returnHeader->detail()->delete();
                $returnHeader->delete();
            }
        }

        // 3. Update status penjualan menjadi cancel
        $penjualan->update(['status' => 'cancel']);

        DB::commit();
        return back()->with('success', 'Transaksi berhasil dibatalkan. Data return terkait telah dihapus dan stok telah disesuaikan.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
    }
}

    public function showReturn($id)
    {
        $return = ReturnPenjualan::with(['penjualan.client', 'detail.produk'])->findOrFail($id);
        
        $title = 'Detail Return Barang';
        return view('admin.penjualan.show_return', compact('return', 'title'));
    }

    public function kirimUlang(Request $request, $return_id)
    {
        $request->validate([
            'tanggal' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            // Ambil data return detail
            $return = ReturnPenjualan::with(['penjualan', 'detail'])->findOrFail($return_id);

            // Buat Transaksi Penjualan Baru (Kirim Ulang)
            $newPenjualan = Penjualan::create([
                'client_id' => $return->penjualan->client_id,
                'tanggal'   => $request->tanggal,
                'total'     => 0, // Harga default 0
                'status'    => 'berhasil',
                'keterangan'=> "Kirim Ulang (Replacement) dari Return #{$return->nomor_return}"
            ]);

            foreach ($return->detail as $item) {
                $stokRecord = StokProduk::where('produk_id', $item->produk_id)->first();
                $stokLama = $stokRecord->jumlah ?? 0;

                // Simpan Detail Penjualan Baru dengan harga 0
                PenjualanDetail::create([
                    'penjualan_id' => $newPenjualan->id,
                    'produk_id'    => $item->produk_id,
                    'qty'          => $item->qty,
                    'harga'        => 0,
                    'subtotal'     => 0,
                ]);

                // Update Stok (Barang keluar lagi)
                StokHelper::updateStokProduk($item->produk_id, -$item->qty);

                // Catat Log
                StockLog::create([
                    'item_id' => $item->produk_id,
                    'item_type' => 'produk',
                    'jenis' => 'keluar',
                    'jumlah' => $item->qty,
                    'stok_sebelum' => $stokLama,
                    'stok_sesudah' => $stokLama - $item->qty,
                    'sumber' => 'penjualan',
                    'keterangan' => "Kirim Ulang Barang Return #{$return->nomor_return}",
                    'user_id' => auth()->id()
                ]);
            }

            // Generate Surat Jalan untuk pengiriman ulang
            SuratJalan::create([
                'penjualan_id' => $newPenjualan->id,
                'nomor'        => 'SJ-RE-' . date('Ymd') . $newPenjualan->id,
                'tanggal'      => $request->tanggal,
                'status_kirim' => 'proses',
            ]);

            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Barang pengganti berhasil diproses (Harga Rp 0).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal kirim ulang: ' . $e->getMessage());
        }
    }

    public function printResendPDF($id)
    {
        // Load data penjualan yang merupakan hasil resend
        $penjualan = Penjualan::with(['client', 'invoice', 'suratJalan', 'detail.produk'])
            ->findOrFail($id);

        $company = CompanyProfile::first();
        
        // Kita gunakan flag 'type' => 'resend' agar di view bisa kita manipulasi
        $type = 'invoice'; 
        $is_resend_doc = true; // Penanda untuk view

        $customPaper = [0, 0, 595, 420]; // A5 Landscape
        
        $pdf = Pdf::loadView('admin.penjualan.pdf_resend', compact('penjualan', 'type', 'company', 'is_resend_doc'))
            ->setPaper($customPaper, 'landscape');

        return $pdf->stream('INV-RESEND-' . $penjualan->id . '.pdf');
    }

    public function return(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'tanggal' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $penjualan = Penjualan::with(['detail'])->findOrFail($id);
            $totalRefund = 0;
            $returnItemsData = [];

            foreach ($request->items as $itemData) {
                $qtyReturnRequest = (float)$itemData['qty_return'];
                $isiStok = isset($itemData['kembalikan_stok']) && $itemData['kembalikan_stok'] == '1';

                if ($qtyReturnRequest > 0) {
                    $detailJual = $penjualan->detail->where('produk_id', $itemData['produk_id'])->first();

                    if (!$detailJual) continue;

                    // LOGIKA: Sisa yang bisa direturn = Qty Beli - Qty yang SUDAH direturn sebelumnya
                    $sisaBisaReturn = $detailJual->qty - $detailJual->qty_return;

                    if ($qtyReturnRequest > $sisaBisaReturn) {
                        throw new \Exception("Jumlah return produk " . $detailJual->produk->nama . " melebihi sisa yang ada.");
                    }

                    $subtotalRefundPerItem = $qtyReturnRequest * $detailJual->harga;
                    $totalRefund += $subtotalRefundPerItem;

                    $returnItemsData[] = [
                        'produk_id' => $itemData['produk_id'],
                        'qty'       => $qtyReturnRequest,
                        'harga'     => $detailJual->harga,
                        'subtotal'  => $subtotalRefundPerItem,
                    ];

                    // UPDATE BARIS DETAIL: Tambahkan nilai qty_return (TIDAK memotong qty asli)
                    $detailJual->increment('qty_return', $qtyReturnRequest);

                    if ($isiStok) {
                        \App\Helpers\StokHelper::updateStokProduk($itemData['produk_id'], $qtyReturnRequest);
                    }
                }
            }

            if (count($returnItemsData) > 0) {
                // Simpan ke tabel 'returns' (Audit Trail)
                $returnHeader = \App\Models\ReturnPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'nomor_return' => 'RET-' . date('Ymd', strtotime($request->tanggal)) . '-' . rand(100, 999),
                    'tanggal'      => $request->tanggal,
                    'total_refund' => $totalRefund,
                ]);

                foreach ($returnItemsData as $ri) {
                    $returnHeader->detail()->create($ri);
                }

                // Update status transaksi jika diperlukan
                $penjualan->update(['status' => 'return']);
            }

            DB::commit();
            return back()->with('success', 'Return berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $penjualan = Penjualan::with(['client', 'detail.produk', 'invoice', 'suratJalan'])->findOrFail($id);
        $title = 'Cetak Penjualan';
        $company = \App\Models\CompanyProfile::first();

        return view('admin.penjualan.print', compact('penjualan', 'company', 'title'));
    }

    // Cari fungsi downloadPDF di PenjualanController dan pastikan detail dimuat
    public function downloadPDF($id, Request $request)
    {
        $type = $request->query('type', 'invoice');

        $penjualan = Penjualan::with(['client', 'invoice', 'suratJalan', 'detail' => function ($query) {
            // Kita tetap tampilkan baris yang qty-nya > 0 atau yang memiliki catatan return
            $query->with('produk');
        }])->findOrFail($id);

        $company = CompanyProfile::first();
        $customPaper = [0, 0, 595, 420]; // A5 Landscape

        $pdf = Pdf::loadView('admin.penjualan.pdf', compact('penjualan', 'type', 'company'))
            ->setPaper($customPaper, 'landscape');

        $filename = ($type == 'sj' ? 'SJ-' : 'INV-') . $penjualan->id . '.pdf';
        return $pdf->stream($filename);
    }
   // Menampilkan Daftar Return
    public function returnList(Request $request)
    {
        $query = \App\Models\ReturnPenjualan::with(['penjualan.client', 'detail.produk']);

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('penjualan.client', function($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            })->orWhere('nomor_return', 'like', "%$search%");
        }
        $title = 'Daftar Return Penjualan';
        $returns = $query->latest()->paginate(10);
        return view('admin.penjualan.return_list', compact('returns', 'title'));
    }

public function resendReturn(Request $request, $return_id)
{
    $request->validate([
        'tanggal_kirim' => 'required|date',
    ]);

    try {
        DB::beginTransaction();

        // Load detail return BESERTA produknya
        $returnHeader = \App\Models\ReturnPenjualan::with(['detail.produk', 'penjualan'])->findOrFail($return_id);
        
        if ($returnHeader->is_resend) {
            return back()->with('error', 'Gagal: Barang sudah pernah dikirim ulang.');
        }

        // Buat Penjualan Baru
        $newPenjualan = Penjualan::create([
            'client_id' => $returnHeader->penjualan->client_id,
            'tanggal'   => $request->tanggal_kirim,
            'total'     => 0,
            'status'    => 'berhasil',
            'keterangan' => "Kirim ulang barang dari Return #" . $returnHeader->nomor_return
        ]);

        foreach ($returnHeader->detail as $item) {
            // Ambil stok lama untuk log
            $stokRecord = StokProduk::where('produk_id', $item->produk_id)->first();
            $stokLama = $stokRecord->jumlah ?? 0;

            // Simpan Detail Penjualan Baru (Replacement)
            PenjualanDetail::create([
                'penjualan_id' => $newPenjualan->id,
                'produk_id'    => $item->produk_id, // Pastikan ID produk masuk
                'qty'          => $item->qty,
                'harga'        => 0,
                'subtotal'     => 0,
            ]);

            // Update Stok (Barang keluar lagi)
            StokHelper::updateStokProduk($item->produk_id, -$item->qty);

            // Log Stok
            StockLog::create([
                'item_id' => $item->produk_id,
                'item_type' => 'produk',
                'jenis' => 'keluar',
                'jumlah' => $item->qty,
                'stok_sebelum' => $stokLama,
                'stok_sesudah' => $stokLama - $item->qty,
                'sumber' => 'penjualan',
                'keterangan' => "Kirim Ulang Barang (Replacement) Return #{$returnHeader->nomor_return}",
                'user_id' => auth()->id()
            ]);
        }

        $returnHeader->update(['is_resend' => 1]);

        DB::commit();
        return redirect()->route('penjualan.index')->with('success', 'Barang pengganti return berhasil diproses.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal kirim ulang: ' . $e->getMessage());
    }
}

    public function cetakReturn(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $start = $request->start_date;
        $end = $request->end_date;

        // Gunakan Eager Loading agar tidak berat saat looping di view
        $data = \App\Models\ReturnDetail::with([
                'returnHeader.penjualan.client', 
                'produk'
            ])
            ->whereHas('returnHeader', function($q) use ($start, $end) {
                $q->whereBetween('tanggal', [$start, $end]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $title = "Laporan Return Penjualan";

        $pdf = Pdf::loadView('admin.penjualan.pdf_return', compact('data', 'start', 'end', 'title'))
                ->setPaper('a4', 'portrait'); // Bisa ganti landscape jika kolom makin banyak
        
        return $pdf->stream('Laporan-Return-'.$start.'-to-'.$end.'.pdf');
    }

    

}
