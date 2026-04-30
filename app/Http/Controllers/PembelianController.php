<?php

namespace App\Http\Controllers;

use App\Helpers\StokHelper;
use App\Models\BahanBaku;
use App\Models\CompanyProfile;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\StockLog; 
use App\Models\StokBahan;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        // Optimasi: Select kolom tertentu dan eager loading
        $pembelian = Pembelian::select('id', 'supplier_id', 'tanggal', 'total', 'status_pembayaran')
            ->with(['supplier:id,nama', 'detail'])
            ->when($search, function($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhereHas('supplier', function($q) use ($search) {
                          $q->where('nama', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(10);
        
        $title = 'Data Pembelian';
        return view('admin.pembelian.index', compact('pembelian', 'title'));
    }

    public function create()
    {
        $suppliers = Supplier::select('id', 'nama')->get();
        $bahan = BahanBaku::select('id', 'nama', 'satuan')->get(); 
        $title = 'Tambah Pembelian';
        return view('admin.pembelian.create', compact('suppliers', 'bahan', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'tanggal' => 'required|date',
            'items' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $pembelian = Pembelian::create([
                'supplier_id' => $request->supplier_id,
                'tanggal' => $request->tanggal,
                'total' => 0, 
                'status_pembayaran' => 'lunas', 
                'status' => 'aktif',
                'keterangan' => $request->keterangan
            ]);

            $totalPembelian = 0;

            foreach ($request->items as $item) {
                $subtotal = $item['qty'] * $item['harga'];
                
                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'bahan_id' => $item['bahan_id'],
                    'qty' => $item['qty'],
                    'harga' => $item['harga'],
                    'subtotal' => $subtotal,
                ]);

                // 1. Ambil stok lama untuk keperluan Log
                $stokRecord = StokBahan::where('bahan_id', $item['bahan_id'])->first();
                $stokLama = $stokRecord->jumlah ?? 0;

                // 2. Update Stok Fisik
                StokHelper::updateStokBahan($item['bahan_id'], $item['qty']);

                // 3. Catat ke StockLog
                StockLog::create([
                    'item_id' => $item['bahan_id'],
                    'item_type' => 'bahan_baku',
                    'jenis' => 'masuk',
                    'jumlah' => $item['qty'],
                    'stok_sebelum' => $stokLama,
                    'stok_sesudah' => $stokLama + $item['qty'],
                    'sumber' => 'pembelian',
                    'keterangan' => "Pembelian ID: #{$pembelian->id} dari Supplier ID: {$request->supplier_id}",
                    'user_id' => auth()->id()
                ]);

                $totalPembelian += $subtotal;
            }

            $pembelian->update(['total' => $totalPembelian]);

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian Berhasil tercatat.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function cancel($id)
    {
        try {
            DB::beginTransaction();
            $pembelian = Pembelian::with('detail')->findOrFail($id);

            if ($pembelian->status == 'cancelled') {
                return back()->with('error', 'Transaksi ini sudah dibatalkan.');
            }

            foreach ($pembelian->detail as $detail) {
                // 1. Ambil stok saat ini sebelum dikurangi (karena pembatalan beli = stok keluar)
                $stokRecord = StokBahan::where('bahan_id', $detail->bahan_id)->first();
                $stokLama = $stokRecord->jumlah ?? 0;

                // 2. Balikkan Stok (dikurangi karena pembelian batal)
                StokHelper::updateStokBahan($detail->bahan_id, -$detail->qty);

                // 3. Catat ke StockLog
                StockLog::create([
                    'item_id' => $detail->bahan_id,
                    'item_type' => 'bahan_baku',
                    'jenis' => 'keluar',
                    'jumlah' => $detail->qty,
                    'stok_sebelum' => $stokLama,
                    'stok_sesudah' => $stokLama - $detail->qty,
                    'sumber' => 'pembatalan',
                    'keterangan' => "Pembatalan Transaksi Pembelian #{$pembelian->id}",
                    'user_id' => auth()->id()
                ]);
            }

            $pembelian->update([
                'status' => 'cancelled',
                'status_pembayaran' => 'cancel' 
            ]);

            DB::commit();
            return back()->with('success', 'Transaksi berhasil dibatalkan & stok dikoreksi.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal membatalkan transaksi.');
        }
    }

    public function show($id)
    {
        $pembelian = Pembelian::with(['supplier', 'detail.bahan'])->findOrFail($id);
        $title = 'Detail Pembelian';
        return view('admin.pembelian.show', compact('pembelian', 'title'));
    }

    public function cetakPDF($id)
    {
        $pembelian = Pembelian::with(['supplier', 'detail.bahan'])->findOrFail($id);
        $konfigurasi = CompanyProfile::first(); // Ambil data profil perusahaan
        
        $pdf = Pdf::loadView('admin.pembelian.pdf', compact('pembelian', 'konfigurasi'))
                ->setPaper('a4', 'portrait');
                
        return $pdf->stream('Invoice-Pembelian-'.$pembelian->id.'.pdf');
    }
}