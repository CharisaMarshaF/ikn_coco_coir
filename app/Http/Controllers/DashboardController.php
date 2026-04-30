<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\KasHarian;
use App\Models\Produksi; 
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $today = Carbon::today();

        // --- STATISTIK RINGKASAN ---
        $totalProduk = Produk::count() ?? 0;

        // Omset Hari Ini (Penjualan Berhasil)
        $penjualanHariIni = Penjualan::whereDate('tanggal', $today)
            ->where('status', 'berhasil')
            ->sum('total') ?? 0;

        // Omset Bulan Ini (Penjualan Berhasil)
        $penjualanBulanIni = Penjualan::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->where('status', 'berhasil')
            ->sum('total') ?? 0;

        // Pemasukan Kas Bulan Ini (Dari KasHarian)
        $totalPemasukan = (float) KasHarian::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year) 
            ->where('jenis', 'masuk')
            ->sum('total_nominal') ?? 0;

        // Total Produksi Bulan Ini
        $jumlahProduksiBulanIni = Produksi::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->count();

        // --- LOGIKA GRAFIK PENJUALAN BULANAN (12 BULAN TERAKHIR) ---
        $salesData = Penjualan::where('status', 'berhasil')
            ->where('tanggal', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as month_year'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('month_year')
            ->orderBy('month_year', 'ASC')
            ->get();

        $chartLabels = [];
        $chartValues = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $key = $month->format('Y-m');
            $label = $month->format('M Y');
            
            $chartLabels[] = $label;
            $monthlyTotal = $salesData->firstWhere('month_year', $key);
            $chartValues[] = $monthlyTotal ? (float)$monthlyTotal->total : 0;
        }

        // --- DATA TABEL & TRANSAKSI ---
        $produksiTerbaru = Produksi::with(['detail' => function($query) {
            $query->where('jenis', 'produk')->with('produk');
        }])->latest()->take(5)->get();
        
        $recentTransactions = Penjualan::with('client')->latest()->take(5)->get();
        
        $title = 'Dashboard';

        return view('admin.dashboard', compact(
            'totalProduk', 
            'penjualanHariIni',
            'penjualanBulanIni', 
            'totalPemasukan', 
            'jumlahProduksiBulanIni',
            'produksiTerbaru',
            'recentTransactions',
            'chartLabels',
            'chartValues',
            'title'
        ));
    }  
}