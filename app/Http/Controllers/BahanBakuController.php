<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\StockLog;
use App\Models\StokBahan;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan sudah install dompdf
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class BahanBakuController extends Controller
{
    public function index()
    {
        // Ambil semua bahan untuk dropdown filter di modal
        $allBahan = BahanBaku::select('id', 'nama')->get();
        
        $bahan = BahanBaku::select('id', 'nama', 'satuan')
            ->with(['stok' => function($query) {
                $query->select('bahan_id', 'jumlah');
            }])
            ->latest()
            ->get(); // Menggunakan get() agar Datatables client-side berfungsi maksimal

        $title = 'Data Bahan Baku';
        return view('admin.bahan_baku', compact('bahan', 'allBahan', 'title'));
    }
    
    public function cetakLaporan(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'bahan_id' => 'required|exists:bahan_baku,id'
        ]);

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $bahanId = $request->bahan_id;

        $bahan = BahanBaku::findOrFail($bahanId);

        // 1. Hitung Stok Awal (Saldo sebelum start_date)
        // Rumus: Stok Sekarang - (Total Mutasi Masuk setelah start_date) + (Total Mutasi Keluar setelah start_date)
        $stokSekarang = $bahan->stok->jumlah ?? 0;
        
        $mutasiSetelah = StockLog::where('item_id', $bahanId)
            ->where('item_type', 'bahan_baku')
            ->where('created_at', '>=', $start)
            ->select(
                DB::raw("SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE 0 END) as total_masuk"),
                DB::raw("SUM(CASE WHEN jenis = 'keluar' THEN jumlah ELSE 0 END) as total_keluar")
            )->first();

        $stokAwal = $stokSekarang - ($mutasiSetelah->total_masuk ?? 0) + ($mutasiSetelah->total_keluar ?? 0);

        // 2. Ambil data mutasi dalam range tanggal
        $logs = StockLog::where('item_id', $bahanId)
            ->where('item_type', 'bahan_baku')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Mapping data untuk tabel (No, Tanggal, Pembelian/Masuk, Produksi/Keluar, Stok)
        $mutasi = [];
        $tempStok = $stokAwal;
        $totalDiambil = 0;

        foreach ($logs as $log) {
            $masuk = $log->jenis == 'masuk' ? $log->jumlah : 0;
            $keluar = $log->jenis == 'keluar' ? $log->jumlah : 0;
            $tempStok = $tempStok + $masuk - $keluar;
            
            if($log->jenis == 'keluar') $totalDiambil += $log->jumlah;

            $mutasi[] = [
                'tanggal' => $log->created_at,
                'masuk' => $masuk,
                'keluar' => $keluar,
                'stok_akhir' => $tempStok,
                'keterangan' => $log->sumber . ' - ' . $log->keterangan
            ];
        }

        $data = [
            'bahan' => $bahan,
            'start_date' => $start,
            'end_date' => $end,
            'stokAwal' => $stokAwal,
            'mutasi' => $mutasi,
            'stokAkhir' => $tempStok,
            'totalDiambil' => $totalDiambil
        ];

        $pdf = Pdf::loadView('admin.laporan.bahan_pdf', $data);
        return $pdf->stream('Laporan_Mutasi_' . $bahan->nama . '.pdf');
    }

    // ... Method store, update, destroy tetap sama seperti kode Anda sebelumnya ...
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'stok' => 'nullable|numeric|min:0'
        ]);

        DB::transaction(function () use ($request) {
            $bahan = BahanBaku::create([
                'nama' => $request->nama,
                'satuan' => $request->satuan
            ]);

            StokBahan::create([
                'bahan_id' => $bahan->id,
                'jumlah' => $request->stok ?? 0
            ]);
        });

        return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:100',
            'satuan' => 'required|max:20',
            'stok_manual' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request, $id) {
            $bahan = BahanBaku::findOrFail($id);
            $bahan->update([
                'nama' => $request->nama,
                'satuan' => $request->satuan
            ]);

            if ($request->filled('stok_manual') && auth()->user()->role == 'admin') {
                $stokLama = $bahan->stok->jumlah ?? 0;
                $stokBaru = $request->stok_manual;

                if ($stokLama != $stokBaru) {
                    StokBahan::updateOrCreate(
                        ['bahan_id' => $id],
                        ['jumlah' => $stokBaru]
                    );

                    StockLog::create([
                        'item_id' => $id,
                        'item_type' => 'bahan_baku',
                        'jenis' => ($stokBaru > $stokLama) ? 'masuk' : 'keluar',
                        'jumlah' => abs($stokBaru - $stokLama),
                        'stok_sebelum' => $stokLama,
                        'stok_sesudah' => $stokBaru,
                        'sumber' => 'manual',
                        'user_id' => auth()->id(),
                        'keterangan' => $request->keterangan ?? 'Koreksi stok manual oleh Admin'
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $bahan = BahanBaku::findOrFail($id);
        $bahan->delete(); 

        return redirect()->back()->with('success', 'Bahan baku berhasil dipindahkan ke sampah');
    }
}