<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produksi;
use App\Models\StokProduk;
use App\Models\ReturnPenjualan; // Tambahkan ini
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $today = Carbon::today();

        $totalStok = StokProduk::sum('jumlah') ?? 0;
        $produksiHariIni = Produksi::whereDate('tanggal', $today)->count();
        $totalJualBulanIni = Penjualan::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->whereIn('status', ['berhasil', 'return']) // Transaksi 'return' tetap dihitung omsetnya
            ->sum('total') ?? 0;

        $totalReturnBulanIni = ReturnPenjualan::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->sum('total_refund') ?? 0;

        $penjualanBulanIni = $totalJualBulanIni - $totalReturnBulanIni;
        $salesData = Penjualan::whereIn('status', ['berhasil', 'return'])
            ->where('tanggal', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as month_year'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('month_year')
            ->get();

        // Ambil Data Return per Bulan untuk Pengurang
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
            
            // Hitung Penjualan Kotor
            $valJual = $salesData->firstWhere('month_year', $key)->total ?? 0;
            
            // Hitung Pengurang (Return)
            $valReturn = $returnData->firstWhere('month_year', $key)->total_refund ?? 0;
            
            // Omset Bersih
            $chartValues[] = (float)$valJual - (float)$valReturn;
        }

        // 4. Penjualan Terbaru
        $recentTransactions = Penjualan::with('client')->latest()->take(5)->get();
        
        $title = 'Dashboard';

        return view('admin.dashboard', compact(
            'totalStok', 
            'produksiHariIni',
            'penjualanBulanIni', 
            'recentTransactions',
            'chartLabels',
            'chartValues',
            'title'
        ));
    }  
}