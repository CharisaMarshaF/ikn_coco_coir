<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\StockLog;
use Illuminate\Http\Request;

class StockLogController extends Controller
{
    public function index(Request $request)
    {
        // Optimasi: Gunakan select dan eager loading untuk user
        $query = StockLog::select('id', 'item_id', 'item_type', 'jenis', 'jumlah', 'stok_sebelum', 'stok_sesudah', 'sumber', 'keterangan', 'user_id', 'created_at')
            ->with(['user' => function($q) {
                $q->select('id', 'name');
            }]);

        $type = $request->query('type');
        $itemId = $request->query('item_id');

        // 1. Filter berdasarkan Tipe (Produk / Bahan Baku)
        if ($type) {
            $query->where('item_type', $type);
        }

        // 2. Jika filter spesifik per Barang/Produk
        if ($itemId && $type) {
            $query->where('item_id', $itemId);
            
            // Cari nama item berdasarkan tipe untuk judul
            if ($type === 'produk') {
                $item = Produk::select('nama')->find($itemId);
            } else {
                $item = BahanBaku::select('nama')->find($itemId);
            }
            
            $title = "Riwayat Stok: " . ($item->nama ?? 'Tidak Ditemukan');
        } else {
            // Judul dinamis berdasarkan tipe global
            $title = ($type === 'produk') ? "Semua Log Produk Jadi" : (($type === 'bahan_baku') ? "Semua Log Bahan Baku" : "Semua Log Stok");
        }

        // 3. Ambil data dengan pagination (Optimasi untuk data ribuan)
        $logs = $query->latest()->paginate(20)->withQueryString();
        $title = 'Riwayat Stok';
        return view('admin.stock_log', compact('logs', 'title', 'type'));
    }
}