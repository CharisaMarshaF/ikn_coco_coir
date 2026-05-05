<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokProduk;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
class ProdukController extends Controller
{
    public function index()
    {
        // Optimasi: Hanya ambil kolom yang diperlukan dan eager loading stok dengan select
        $produk = Produk::select('id', 'nama', 'satuan', 'harga_default','jenis')
            ->with(['stok' => function($query) {
                $query->select('produk_id', 'jumlah');
            }])
            ->latest()
            ->paginate(10);

        $title = 'Data Produk';
        return view('admin.produk', compact('produk', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'jenis' => 'required|in:jadi,proses',
            'harga_default' => 'nullable|numeric',
            'stok' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $produk = Produk::create([
                'nama' => $request->nama,
                'satuan' => $request->satuan,
                'jenis' => $request->jenis,
                'harga_default' => $request->harga_default,
            ]);

            StokProduk::create([
                'produk_id' => $produk->id,
                'jumlah' => $request->stok ?? 0
            ]);
        });

        return redirect()->back()->with('success', 'Produk dan stok berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'jenis' => 'required|in:jadi,proses',
            'harga_default' => 'nullable|numeric',
            'stok_manual' => 'nullable|numeric', 
        ]);

        DB::transaction(function () use ($request, $id) {
            $produk = Produk::findOrFail($id);
            
            // 1. Update data produk dasar
            $produk->update([
                'nama' => $request->nama,
                'satuan' => $request->satuan,
                'jenis' => $request->jenis,
                'harga_default' => $request->harga_default,
            ]);

            // 2. Logika Update Stok & Log (HANYA UNTUK ADMIN)
            if (auth()->user()->role == 'admin' && $request->filled('stok_manual')) {
                $stokLama = $produk->stok->jumlah ?? 0;
                $stokBaru = $request->stok_manual;

                if ($stokLama != $stokBaru) {
                    StokProduk::updateOrCreate(
                        ['produk_id' => $id],
                        ['jumlah' => $stokBaru]
                    );

                    StockLog::create([
                        'item_id' => $id,
                        'item_type' => 'produk',
                        'jenis' => ($stokBaru > $stokLama) ? 'masuk' : 'keluar',
                        'jumlah' => abs($stokBaru - $stokLama),
                        'stok_sebelum' => $stokLama,
                        'stok_sesudah' => $stokBaru,
                        'sumber' => 'manual',
                        'keterangan' => $request->keterangan ?? 'Koreksi stok oleh Admin',
                        'user_id' => auth()->id()
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        
        DB::transaction(function () use ($produk) {
            // Karena StokProduk juga pakai SoftDeletes, 
            // ini akan mengisi kolom deleted_at di tabel stok_produk
            if ($produk->stok) {
                $produk->stok->delete();
            }
            
            // Ini akan mengisi kolom deleted_at di tabel produk
            $produk->delete();
        });

        return redirect()->back()->with('success', 'Produk berhasil dipindahkan ke tempat sampah (Soft Delete)');
    }

    public function cetakLaporan(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'produk_id' => 'required|exists:produk,id'
        ]);

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $produkId = $request->produk_id;

        $produk = Produk::findOrFail($produkId);

        // 1. Hitung Stok Awal (Saldo sebelum start_date)
        // Stok Sekarang - (Masuk setelah start_date) + (Keluar setelah start_date)
        $stokSekarang = $produk->stok->jumlah ?? 0;
        
        $mutasiSetelah = StockLog::where('item_id', $produkId)
            ->where('item_type', 'produk')
            ->where('created_at', '>=', $start)
            ->select(
                DB::raw("SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE 0 END) as total_masuk"),
                DB::raw("SUM(CASE WHEN jenis = 'keluar' THEN jumlah ELSE 0 END) as total_keluar")
            )->first();

        $stokAwal = $stokSekarang - ($mutasiSetelah->total_masuk ?? 0) + ($mutasiSetelah->total_keluar ?? 0);

        // 2. Ambil mutasi dalam range tanggal
        $logs = StockLog::where('item_id', $produkId)
            ->where('item_type', 'produk')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'asc')
            ->get();

        $mutasi = [];
        $tempStok = $stokAwal;
        $totalKeluar = 0;

        foreach ($logs as $log) {
            $masuk = $log->jenis == 'masuk' ? $log->jumlah : 0;
            $keluar = $log->jenis == 'keluar' ? $log->jumlah : 0;
            $tempStok = $tempStok + $masuk - $keluar;
            
            if($log->jenis == 'keluar') $totalKeluar += $log->jumlah;

            $mutasi[] = [
                'tanggal' => $log->created_at,
                'masuk' => $masuk,
                'keluar' => $keluar,
                'stok_akhir' => $tempStok,
                'keterangan' => $log->sumber . ' - ' . $log->keterangan
            ];
        }

        $data = [
            'produk' => $produk,
            'start_date' => $start,
            'end_date' => $end,
            'stokAwal' => $stokAwal,
            'mutasi' => $mutasi,
            'stokAkhir' => $tempStok,
            'totalKeluar' => $totalKeluar
        ];

        $pdf = Pdf::loadView('admin.laporan.produk_pdf', $data);
        return $pdf->stream('Laporan_Produk' . $produk->nama . '.pdf');
    }
}