<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\KasHarian;
use App\Models\TransaksiKeuangan;
use App\Models\Produksi; // Pastikan model Produksi di-import
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Ringkas
        $totalProduk = Produk::count() ?? 0;
        
        $penjualanBulanIni = Penjualan::whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->where('status', 'berhasil')
            ->sum('total') ?? 0;
        
        $totalPemasukan = (float) KasHarian::whereMonth('tanggal', Carbon::now()->month)
            ->where('jenis', 'masuk')->sum('nominal') + 
            (float) TransaksiKeuangan::whereMonth('tanggal', Carbon::now()->month)
            ->where('jenis', 'masuk')->sum('nominal');

        // 2. Statistik Produksi (Ganti User Online & Grafik)
        $jumlahProduksiBulanIni = Produksi::whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->count();

        $produksiTerbaru = Produksi::latest()->take(8)->get();

        // 3. Penjualan Terbaru
        $recentTransactions = Penjualan::with('client')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProduk', 
            'penjualanBulanIni', 
            'totalPemasukan', 
            'jumlahProduksiBulanIni',
            'produksiTerbaru',
            'recentTransactions'
        ));
    }
}