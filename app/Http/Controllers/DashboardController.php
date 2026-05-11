<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produksi;
use App\Models\StokProduk;
use App\Models\ReturnPenjualan;
use App\Models\Rekening; // Tambahkan ini
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $today = Carbon::today();

        // Data Widget Utama
        $totalStok = StokProduk::sum('jumlah') ?? 0;
        $produksiHariIni = Produksi::whereDate('tanggal', $today)->count();
        $totalJualBulanIni = Penjualan::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->whereIn('status', ['berhasil', 'return'])
            ->sum('total') ?? 0;

        $totalReturnBulanIni = ReturnPenjualan::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->sum('total_refund') ?? 0;

        $penjualanBulanIni = $totalJualBulanIni - $totalReturnBulanIni;

        // Data Saldo Rekening (Untuk Widget Baru)
        $listRekening = Rekening::all();

        // Data Grafik 12 Bulan
        $salesData = Penjualan::whereIn('status', ['berhasil', 'return'])
            ->where('tanggal', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as month_year'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('month_year')
            ->get();

        $returnData = ReturnPenjualan::where('tanggal', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as month_year'),
                DB::raw('SUM(total_refund) as total_refund')
            )
            ->groupBy('month_year')
            ->get();

        $chartLabels = [];
        $chartValues = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $key = $month->format('Y-m');
            $chartLabels[] = $month->format('M Y');
            $valJual = $salesData->firstWhere('month_year', $key)->total ?? 0;
            $valReturn = $returnData->firstWhere('month_year', $key)->total_refund ?? 0;
            $chartValues[] = (float)$valJual - (float)$valReturn;
        }

        $recentTransactions = Penjualan::with('client')->latest()->take(5)->get();
        $title = 'Dashboard';

        return view('admin.dashboard', compact(
            'totalStok', 
            'produksiHariIni',
            'penjualanBulanIni', 
            'recentTransactions',
            'chartLabels',
            'chartValues',
            'title',
            'listRekening' // Kirim data rekening ke view
        ));
    }

    // Fungsi Export PDF yang diminta
    public function exportPdf(Request $request)
    {
        $tgl_mulai = $request->get('tgl_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tgl_selesai = $request->get('tgl_selesai', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $rekening_id = $request->get('rekening_id');

        // 1. Hitung Saldo Awal
        $querySaldoSekarang = \App\Models\Rekening::query();
        $queryMasuk = \App\Models\KasHarian::where('tanggal', '>=', $tgl_mulai)->where('jenis', 'masuk');
        $queryKeluar = \App\Models\KasHarian::where('tanggal', '>=', $tgl_mulai)->where('jenis', 'keluar');

        if ($rekening_id) {
            $querySaldoSekarang->where('id', $rekening_id);
            $queryMasuk->where('rekening_id', $rekening_id);
            $queryKeluar->where('rekening_id', $rekening_id);
        }

        $totalSaldoSekarang = $querySaldoSekarang->sum('saldo');
        $saldoAwalRiil = $totalSaldoSekarang - $queryMasuk->sum('total_nominal') + $queryKeluar->sum('total_nominal');

        // 2. Ambil data mutasi
        $queryData = \App\Models\KasHarian::with(['rekening' => fn($q) => $q->withTrashed(), 'details', 'kategoriKas'])
            ->whereBetween('tanggal', [$tgl_mulai, $tgl_selesai]);

        if ($rekening_id) $queryData->where('rekening_id', $rekening_id);

        $data = $queryData->orderBy('tanggal', 'asc')->orderBy('created_at', 'asc')->get();

        // 3. AMBIL SEMUA KATEGORI
        $kategoriTerlibat = \App\Models\KategoriKas::orderBy('nama', 'asc')->get();

        $rekening = $rekening_id ? \App\Models\Rekening::withTrashed()->find($rekening_id) : null;
        $namaRekening = $rekening ? $rekening->nama : 'Semua Rekening';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.keuangan.export_pdf', [
            'data' => $data,
            'tgl_mulai' => $tgl_mulai,
            'tgl_selesai' => $tgl_selesai,
            'saldoAwal' => $saldoAwalRiil,
            'namaRekening' => $namaRekening,
            'kategoriTerlibat' => $kategoriTerlibat
        ])->setPaper('a4', 'landscape'); 

        return $pdf->stream('Laporan_Kas_' . $namaRekening . '.pdf');
    }
}