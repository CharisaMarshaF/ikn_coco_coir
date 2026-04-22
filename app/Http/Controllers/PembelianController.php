<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Helpers\StokHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index()
    {

        $pembelian = Pembelian::with('supplier')->latest()->paginate(10);
        return view('admin.pembelian.index', compact('pembelian'));
    }

    public function create()
    {
        $rekening = \App\Models\Rekening::all();
        $suppliers = Supplier::all();
        $bahan = BahanBaku::all();
        return view('admin.pembelian.create', compact('suppliers', 'bahan', 'rekening'));
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

        // 1. Simpan Header Pembelian
        $pembelian = Pembelian::create([
            'supplier_id' => $request->supplier_id,
            'tanggal' => $request->tanggal,
            'total' => 0, 
            'status_pembayaran' => $request->rekening_id ? 'lunas' : 'belum',
        ]);

        $totalPembelian = 0;

        // 2. Simpan Detail & Update Stok
        foreach ($request->items as $item) {
            $subtotal = $item['qty'] * $item['harga'];
            
            PembelianDetail::create([
                'pembelian_id' => $pembelian->id,
                'bahan_id' => $item['bahan_id'],
                'qty' => $item['qty'],
                'harga' => $item['harga'],
                'subtotal' => $subtotal,
            ]);

            StokHelper::updateStokBahan($item['bahan_id'], $item['qty']);
            $totalPembelian += $subtotal;
        }

        // 3. Update Total di Header
        $pembelian->update(['total' => $totalPembelian]);

        // 4. Logika Pembayaran (Hanya satu blok yang benar)
        if ($request->rekening_id) {
            $rekening = \App\Models\Rekening::findOrFail($request->rekening_id);
            
            if ((float)$rekening->saldo_saat_ini < (float)$totalPembelian) {
                throw new \Exception("Saldo pada rekening {$rekening->nama} tidak cukup!");
            }

            // Potong Saldo
            $rekening->decrement('saldo_saat_ini', $totalPembelian);
            
            // Catat Transaksi Keuangan (Sesuaikan nama kolom dengan Model/Database)
            \App\Models\TransaksiKeuangan::create([
                'rekening_id' => $request->rekening_id,
                'tanggal'     => $request->tanggal,
                'jenis'       => 'keluar',
                'sumber'      => 'pembelian_bahan', 
                'nominal'     => $totalPembelian,    
                'keterangan'  => "Pembelian bahan baku #PB-" . str_pad($pembelian->id, 5, '0', STR_PAD_LEFT),
            ]);
        }

        DB::commit();
        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dicatat');

    } catch (\Exception $e) {
        DB::rollback();
        $errorMessage = "Error: " . $e->getMessage() . " (Line: " . $e->getLine() . ")";
        return redirect()->back()->withInput()->with('error', $errorMessage);
    }
}
    public function show($id)
    {
        // Hapus 'pembayaran.rekening' karena kolom pembelian_id tidak ada
        $pembelian = Pembelian::with(['supplier', 'detail.bahan'])->findOrFail($id);
        
        return view('admin.pembelian.show', compact('pembelian'));
    }
    public function bayar(Request $request, $id)
{
    $request->validate([
        'rekening_id' => 'required'
    ]);

    $pembelian = Pembelian::findOrFail($id);

    try {
        DB::beginTransaction();

        $rekening = \App\Models\Rekening::findOrFail($request->rekening_id);

        // 1. Cek Saldo
        if ((float)$rekening->saldo_saat_ini < (float)$pembelian->total) {
            throw new \Exception("Saldo pada rekening {$rekening->nama} tidak cukup!");
        }

        // 2. Potong Saldo Rekening
        $rekening->decrement('saldo_saat_ini', $pembelian->total);

        // 3. Catat ke Transaksi Keuangan
        \App\Models\TransaksiKeuangan::create([
            'rekening_id' => $request->rekening_id,
            'tanggal'     => date('Y-m-d'),
            'jenis'       => 'keluar',
            'sumber'      => 'pembelian_bahan',
            'nominal'     => $pembelian->total,
            'keterangan'  => "Pembelian bahan baku #PB-" . str_pad($pembelian->id, 5, '0', STR_PAD_LEFT),
        ]);

        // 4. Update Status Pembelian
        $pembelian->update([
            'status_pembayaran' => 'lunas'
        ]);

        DB::commit();
        return redirect()->back()->with('success', 'Pembayaran berhasil, status diperbarui menjadi Lunas.');

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', $e->getMessage());
    }
}
}
