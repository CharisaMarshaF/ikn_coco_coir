<?php

namespace App\Http\Controllers;

use App\Helpers\StokHelper;
use App\Models\BahanBaku;
use App\Models\CompanyProfile;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $pembelian = Pembelian::with(['supplier', 'detail'])
            ->when($search, function($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhereHas('supplier', function($q) use ($search) {
                          $q->where('nama', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(10);

        return view('admin.pembelian.index', compact('pembelian'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $bahan = BahanBaku::all(); 
        return view('admin.pembelian.create', compact('suppliers', 'bahan'));
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

                StokHelper::updateStokBahan($item['bahan_id'], $item['qty']);
                $totalPembelian += $subtotal;
            }

            $pembelian->update(['total' => $totalPembelian]);

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian Berhasil.');

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
                StokHelper::updateStokBahan($detail->bahan_id, -$detail->qty);
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
        // Menghapus relasi rekening yang tidak digunakan
        $pembelian = Pembelian::with(['supplier', 'detail.bahan'])->findOrFail($id);
        return view('admin.pembelian.show', compact('pembelian'));
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