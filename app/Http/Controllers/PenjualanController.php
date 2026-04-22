<?php

namespace App\Http\Controllers;

use App\Helpers\StokHelper;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar dengan relasi client
        $query = Penjualan::with('client')->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

        // Filter Pencarian (Invoice atau Nama Client)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($qc) use ($search) {
                      $qc->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Pagination
        $penjualan = $query->paginate(10)->withQueryString();

        return view('admin.penjualan.index', compact('penjualan'));
    }

    public function create()
    {
        $clients = Client::all();
        // Mengambil produk beserta relasi stoknya
        $produk = Produk::with('stok')->get(); 
        return view('admin.penjualan.create', compact('clients', 'produk'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|in:berhasil,cancel,return',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.harga' => 'required|numeric',
        ]);

        try {
        DB::beginTransaction();

        // 1. Simpan Penjualan
        $penjualan = Penjualan::create([
            'client_id' => $request->client_id,
            'tanggal'   => $request->tanggal,
            'total'     => $request->total,
            'status'    => 'berhasil',
        ]);

        // 2. Simpan Detail & Update Stok (Logic yang sudah ada)
        foreach ($request->items as $item) {
            PenjualanDetail::create([
                'penjualan_id' => $penjualan->id,
                'produk_id'    => $item['produk_id'],
                'qty'          => $item['qty'],
                'harga'        => $item['harga'],
                'subtotal'     => $item['qty'] * $item['harga'],
            ]);
            StokHelper::updateStokProduk($item['produk_id'], -$item['qty']);
        }

        // 3. OTOMATIS GENERATE INVOICE (Status Lunas)
        Invoice::create([
            'penjualan_id' => $penjualan->id,
            'nomor'        => 'INV-' . date('Ymd') . $penjualan->id,
            'tanggal'      => $request->tanggal,
            'total'        => $request->total,
            'status_bayar' => 'lunas',
        ]);

        // 4. OTOMATIS GENERATE SURAT JALAN (Status Diterima)
        SuratJalan::create([
            'penjualan_id' => $penjualan->id,
            'nomor'        => 'SJ-' . date('Ymd') . $penjualan->id,
            'tanggal'      => $request->tanggal,
            'status_kirim' => 'diterima',
        ]);

        DB::commit();
        // Langsung arahkan ke halaman cetak/invoice
        return redirect()->route('penjualan.print', $penjualan->id)->with('success', 'Transaksi Berhasil.');

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
    return view('admin.penjualan.show', compact('penjualan'));
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
            // Kembalikan stok sebanyak jumlah terjual (positif)
            StokHelper::updateStokProduk($item['produk_id'], $item['qty']);
        }

        $penjualan->update(['status' => 'cancel']);

        DB::commit();
        return back()->with('success', 'Transaksi dibatalkan & stok telah dikembalikan.');
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
        $penjualan = Penjualan::findOrFail($id);

        foreach ($request->items as $item) {
            if ($item['qty_return'] > 0) {
                // Kembalikan stok sejumlah yang direturn
                StokHelper::updateStokProduk($item['produk_id'], $item['qty_return']);
                
                // Opsional: Catat riwayat return di tabel lain jika perlu
            }
        }

        $penjualan->update([
            'status' => 'return',
            'keterangan' => $request->keterangan // Pastikan ada kolom keterangan di tabel penjualan
        ]);

        DB::commit();
        return back()->with('success', 'Berhasil memproses return sebagian stok.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}
    public function print($id)
    {
        $penjualan = Penjualan::with(['client', 'detail.produk', 'invoice', 'suratJalan'])->findOrFail($id);
        return view('admin.penjualan.print', compact('penjualan'));
    }

    public function downloadPDF($id, Request $request)
    {
        $type = $request->query('type', 'invoice'); // invoice atau sj
        $penjualan = Penjualan::with(['client', 'detail.produk', 'invoice', 'suratJalan'])->findOrFail($id);
        
        // Ukuran A5 dalam Points (1 mm = 2.83465 pts)
        // A5 Landscape: 210mm x 148mm => 595pt x 420pt
        $customPaper = [0, 0, 595, 420]; 

        $pdf = Pdf::loadView('admin.penjualan.pdf', compact('penjualan', 'type'))
                ->setPaper($customPaper, 'landscape');

        $filename = ($type == 'sj' ? 'SJ-' : 'INV-') . $penjualan->id . '.pdf';
        
        return $pdf->stream($filename); // Gunakan stream agar langsung terbuka di browser/dialog print
    }
}