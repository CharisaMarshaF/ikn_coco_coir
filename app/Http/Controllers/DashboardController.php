<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\KasHarian;
use App\Models\Produksi; 
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // 1. Total Produk Terdaftar
        $totalProduk = Produk::count() ?? 0;
        
        // 2. Penjualan Bulan Ini (Hanya yang berstatus berhasil)
        $penjualanBulanIni = Penjualan::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->where('status', 'berhasil')
            ->sum('total') ?? 0;
        
        // 3. Total Pemasukan (Hanya dari KasHarian jenis 'masuk')
        $totalPemasukan = (float) KasHarian::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year) // Tambahkan filter tahun agar akurat
            ->where('jenis', 'masuk')
            ->sum('nominal') ?? 0;

        // 4. Jumlah Produksi Bulan Ini
        $jumlahProduksiBulanIni = Produksi::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->count();

        // 5. List Produksi Terbaru (Menampilkan 8 data terakhir)
        $produksiTerbaru = Produksi::with(['detail' => function($query) {
            $query->where('jenis', 'produk')->with('produk');
        }])->latest()->take(8)->get();
        
        // 6. Transaksi Penjualan Terakhir (Menampilkan 5 data terakhir)
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