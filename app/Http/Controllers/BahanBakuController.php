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
            ->with(['stok' => function ($query) {
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
            'end_date'   => 'required|date',
            'bahan_id'   => 'required|exists:bahan_baku,id'
        ]);

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $bahanId = $request->bahan_id;

        $bahan = BahanBaku::findOrFail($bahanId);

        // 1. Ambil Saldo Awal 
        // Cari log terakhir SEBELUM tanggal mulai untuk mendapatkan stok_sesudah-nya
        $lastLogBefore = StockLog::where('item_id', $bahanId)
            ->where('item_type', 'bahan_baku')
            ->where('created_at', '<', $start)
            ->orderBy('created_at', 'desc')
            ->first();

        // Jika tidak ada log sebelumnya, ambil stok_sebelum dari log pertama di periode ini
        // Jika masih tidak ada, berarti stok awal 0
        $firstLogInRange = StockLog::where('item_id', $bahanId)
            ->where('item_type', 'bahan_baku')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'asc')
            ->first();

        $stokAwal = $lastLogBefore ? $lastLogBefore->stok_sesudah : ($firstLogInRange->stok_sebelum ?? 0);

        // 2. Ambil Mutasi dalam periode
        $logs = StockLog::where('item_id', $bahanId)
            ->where('item_type', 'bahan_baku')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Mapping data untuk view agar struktur tetap sama dengan laporan sebelumnya
        $mutasi = $logs->map(function ($log) {
            return [
                'tanggal'    => $log->created_at,
                'masuk'      => $log->jenis === 'masuk' ? (double)$log->jumlah : 0,
                'keluar'     => $log->jenis === 'keluar' ? (double)$log->jumlah : 0,
                'stok_akhir' => (double)$log->stok_sesudah,
                'keterangan' => strtoupper($log->sumber) . ($log->keterangan ? ' - ' . $log->keterangan : '')
            ];
        });

        $data = [
            'bahan'        => $bahan,
            'start_date'   => $start,
            'end_date'     => $end,
            'stokAwal'     => (double)$stokAwal,
            'mutasi'       => $mutasi,
            'stokAkhir'    => (double)($logs->last()->stok_sesudah ?? $stokAwal),
            'totalMasuk'   => (double)$mutasi->sum('masuk'),
            'totalKeluar'  => (double)$mutasi->sum('keluar'),
            'konfigurasi'  => \App\Models\CompanyProfile::first(),
            'formatNumber' => function($val) {
                return number_format((double)$val, 2, ',', '.');
            }
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.bahan_pdf', $data);
        return $pdf->stream('Laporan_Mutasi_' . str_replace(' ', '_', $bahan->nama) . '.pdf');
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
