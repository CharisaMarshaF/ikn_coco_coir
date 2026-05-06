<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\CompanyProfile;
use App\Models\Produk;
use App\Models\StockLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StockLogController extends Controller
{
    public function index(Request $request)
    {
        $query = StockLog::with(['user' => function($q) {
                $q->select('id', 'name');
            }]);

        $type = $request->query('type');
        $itemId = $request->query('item_id');

        if ($type) {
            $query->where('item_type', $type);
        }

        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        $logs = $query->latest()->paginate(20)->withQueryString();
        $title = 'Riwayat Stok';
        
        return view('admin.stock_log', compact('logs', 'title', 'type', 'itemId'));
    }

public function cetakLaporan(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date'   => 'required|date',
        'type'       => 'required|in:produk,bahan_baku',
        'item_id'    => 'nullable' 
    ]);

    $start = Carbon::parse($request->start_date)->startOfDay();
    $end = Carbon::parse($request->end_date)->endOfDay();
    $type = $request->type;
    $itemId = $request->item_id;

    // 1. Ambil Nama Items
    $items = ($type === 'produk') 
        ? Produk::pluck('nama', 'id') 
        : BahanBaku::pluck('nama', 'id');

    // 2. Query Logs dari tabel StockLog (Sudah mencakup semua mutasi)
    $query = StockLog::where('item_type', $type)
        ->whereBetween('created_at', [$start, $end])
        ->orderBy('item_id', 'asc')
        ->orderBy('created_at', 'asc');

    if ($itemId) {
        $query->where('item_id', $itemId);
    }

    $allLogs = $query->get();

    // 3. LOGIKA DESIMAL: Jika bahan_baku gunakan 2 desimal, jika produk gunakan 0 desimal
    $formatNumber = function($val) use ($type) {
        if ($type == 'bahan_baku') {
            // Hasil: 10.00 atau 10.50
            return number_format((double)$val, 2, ',', '.');
        }
        // Hasil: 10 (untuk produk jadi)
        return number_format((double)$val, 0, ',', '.');
    };

    $konfigurasi = CompanyProfile::first();

    // 4. Grouping Data
    $groupedData = $allLogs->groupBy('item_id')->map(function ($logs, $id) use ($type, $start, $items) {
        // Cari saldo sebelum periode mulai
        $lastLogBefore = StockLog::where('item_id', $id)
            ->where('item_type', $type)
            ->where('created_at', '<', $start)
            ->orderBy('created_at', 'desc')
            ->first();

        $stokAwal = $lastLogBefore ? (double)$lastLogBefore->stok_sesudah : (double)($logs->first()->stok_sebelum ?? 0);
        
        return [
            'nama' => $items[$id] ?? 'Item Dihapus',
            'stok_awal' => $stokAwal,
            'logs' => $logs
        ];
    });

    $data = [
        'type'         => $type,
        'start_date'   => $start,
        'end_date'     => $end,
        'groupedData'  => $groupedData,
        'konfigurasi'  => $konfigurasi,
        'formatNumber' => $formatNumber
    ];

    $pdf = Pdf::loadView('admin.laporan.stock_log_pdf', $data);
    return $pdf->stream('Laporan_Mutasi_Stok.pdf');
}
}