<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HasilProduksiController;
use App\Http\Controllers\KasHarianController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PengambilanBahanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\StockLogController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    
    // Dashboard Routes
Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->name('admin.dashboard');
    Route::get('/staff/dashboard', function () {
        return view('admin.dashboard');
    })->name('staff.dashboard');

    Route::resource('users', UserController::class);
    Route::resource('supplier', SupplierController::class);
    Route::resource('bahan-baku', BahanBakuController::class);
    Route::resource('produk', ProdukController::class);
    Route::resource('client', ClientController::class);
    Route::resource('rekening', RekeningController::class);
    Route::resource('pembelian', PembelianController::class);
    Route::post('/pembelian/{id}/bayar', [PembelianController::class, 'bayar'])->name('pembelian.bayar');
    Route::resource('produksi', ProduksiController::class);
    Route::get('produksi/{id}/repair', [ProduksiController::class, 'repair'])->name('produksi.repair');
    Route::post('/produksi/{id}/update-status', [ProduksiController::class, 'updateStatus'])->name('produksi.update-status');
    Route::get('/produksi/{id}/repair', [ProduksiController::class, 'repair'])->name('produksi.repair');
    Route::post('/produksi/{id}/repair-store', [ProduksiController::class, 'repairStore'])->name('produksi.repair-store');
    Route::resource('penjualan', PenjualanController::class);   
    Route::get('penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
    Route::post('penjualan/{id}/cancel', [PenjualanController::class, 'cancel'])->name('penjualan.cancel');
    Route::post('penjualan/{id}/return', [PenjualanController::class, 'return'])->name('penjualan.return');
    Route::get('penjualan/{id}/print', [PenjualanController::class, 'print'])->name('penjualan.print');
    Route::get('penjualan/{id}/pdf', [PenjualanController::class, 'downloadPDF'])->name('penjualan.pdf');
    Route::get('keuangan/kas', [KasHarianController::class, 'index'])->name('kas.index');
    Route::post('keuangan/kas', [KasHarianController::class, 'store'])->name('kas.store');
    Route::delete('keuangan/kas/{id}', [KasHarianController::class, 'destroy'])->name('kas.destroy');
    Route::get('keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
    Route::post('keuangan/transaksi', [KeuanganController::class, 'storeTransaksi'])->name('keuangan.storeTransaksi');
    Route::post('keuangan/kash', [KeuanganController::class, 'storeKas'])->name('keuangan.storeKas');
    Route::get('/konfigurasi', [CompanyProfileController::class, 'index'])->name('konfigurasi.index');
    Route::post('/konfigurasi/update', [CompanyProfileController::class, 'update'])->name('konfigurasi.update');
    Route::post('pembelian/{id}/cancel', [PembelianController::class, 'cancel'])->name('pembelian.cancel');
    Route::get('pembelian/{id}/pdf', [PembelianController::class, 'cetakPDF'])->name('pembelian.pdf');
    Route::delete('/produksi/{id}/cancel', [ProduksiController::class, 'cancel'])->name('produksi.cancel');
    Route::put('/penjualan/{id}/cancel', [App\Http\Controllers\PenjualanController::class, 'cancel'])->name('penjualan.cancel');
    Route::get('/laporan/penjualan', [LaporanController::class, 'penjualan'])->name('laporan.penjualan');
    Route::get('/laporan/pembelian', [LaporanController::class, 'pembelian'])->name('laporan.pembelian');
    Route::get('/laporan/penjualan/cetak', [LaporanController::class, 'cetakPenjualan'])->name('laporan.penjualan.cetak');
    Route::get('/laporan/pembelian/cetak', [LaporanController::class, 'cetakPembelian'])->name('laporan.pembelian.cetak');
    Route::get('/stok-log', [StockLogController::class, 'index'])->name('stock-log.index');
    Route::get('/log-stok-produk', [StockLogController::class, 'indexProduk'])->name('log.produk');
    Route::get('/kas', [KasHarianController::class, 'index'])->name('kas.index');
    Route::get('/kas/export-pdf', [KasHarianController::class, 'exportPdf'])->name('kas.pdf');
    Route::prefix('pengambilan-bahan')->name('pengambilan.')->group(function () {
        Route::get('/', [PengambilanBahanController::class, 'index'])->name('index');           // Daftar Pengambilan
        Route::get('/create', [PengambilanBahanController::class, 'create'])->name('create');   // Form Tambah
        Route::post('/store', [PengambilanBahanController::class, 'store'])->name('store');     // Proses Simpan
        Route::delete('/{id}', [PengambilanBahanController::class, 'destroy'])->name('destroy'); // Proses Hapus (Revert Stok)
    });
    // Hasil Produksi
// Pastikan name() sesuai dengan yang dipanggil di view
// Tambahkan ini di web.php
Route::get('hasil-produksi/cetak', [HasilProduksiController::class, 'cetakLaporan'])->name('hasil-produksi.cetak');

// Baru kemudian resource (jika ada)
Route::resource('hasil-produksi', HasilProduksiController::class);
    Route::prefix('hasil-produksi')->name('hasil-produksi.')->group(function () {
        Route::get('/', [HasilProduksiController::class, 'index'])->name('index');
        Route::get('/create', [HasilProduksiController::class, 'create'])->name('create');
        Route::post('/store', [HasilProduksiController::class, 'store'])->name('store');
        Route::delete('/{id}', [HasilProduksiController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [HasilProduksiController::class, 'show'])->name('show');
    });
Route::get('/pengambilan/pdf', [PengambilanBahanController::class, 'cetakPdf'])->name('pengambilan.pdf');
Route::get('pembelian/pdf', [PembelianController::class, 'cetakPDF'])->name('pembelian.pdf');
// Pengambilan Bahan
Route::get('pengambilan/cetak', [PengambilanBahanController::class, 'cetakPdf'])->name('pengambilan.cetak');


});