<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function penjualan(Request $request)
    {
        $start_date = $request->query('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->query('end_date', Carbon::now()->format('Y-m-d'));
        $status = $request->query('status');

        $query = Penjualan::with(['client', 'detail'])
            ->whereBetween('tanggal', [$start_date, $end_date]);

        if ($status) {
            $query->where('status', $status);
        }

        $data = $query->orderBy('tanggal', 'desc')->get();
        $summary = [
            'total_omzet' => $data->where('status', 'berhasil')->sum('total'),
            'total_cancel' => $data->where('status', 'cancel')->sum('total'),
            'total_return' => $data->where('status', 'return')->sum('total'),
            'count_transaksi' => $data->count(),
        ];

        return view('admin.laporan.penjualan', compact('data', 'summary', 'start_date', 'end_date', 'status'));
    }

    public function pembelian(Request $request)
    {
        // 1. Default Tanggal (Bulan ini)
        $start_date = $request->query('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->query('end_date', Carbon::now()->format('Y-m-d'));
        $status = $request->query('status');

        // 2. Query data pembelian dengan relasi supplier
        $query = Pembelian::with(['supplier', 'detail.bahan'])
            ->whereBetween('tanggal', [$start_date, $end_date]);

        // 3. Filter Status Pembayaran (lunas, hutang, dll)
        if ($status) {
            $query->where('status_pembayaran', $status);
        }

        $data = $query->orderBy('tanggal', 'desc')->get();

        // 4. Hitung Ringkasan Pengeluaran
        $summary = [
            'total_pengeluaran' => $data->sum('total'),
            'total_lunas' => $data->where('status_pembayaran', 'lunas')->sum('total'),
            'total_hutang' => $data->where('status_pembayaran', 'hutang')->sum('total'),
            'count_transaksi' => $data->count(),
        ];

        return view('admin.laporan.pembelian', compact('data', 'summary', 'start_date', 'end_date', 'status'));
    }

public function cetakPenjualan(Request $request)
    {
        // Jika start_date kosong, ambil dari awal bulan ini atau bisa diset ke '1970-01-01' untuk benar-benar semua
        $start_date = $request->query('start_date') ?? Penjualan::min('tanggal') ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = $request->query('end_date') ?? Carbon::now()->format('Y-m-d');
        $status = $request->query('status');

        $query = Penjualan::with(['client', 'detail'])
            ->whereBetween('tanggal', [$start_date, $end_date]);

        if ($status) {
            $query->where('status', $status);
        }

        $data = $query->orderBy('tanggal', 'desc')->get();
        
        $summary = [
            'total_omzet' => $data->where('status', 'berhasil')->sum('total'),
            'total_cancel' => $data->where('status', 'cancel')->sum('total'),
            'total_return' => $data->where('status', 'return')->sum('total'),
            'count_transaksi' => $data->count(),
        ];

        $pdf = Pdf::loadView('admin.laporan.pdf_penjualan', compact('data', 'summary', 'start_date', 'end_date'))
                  ->setPaper('a4', 'portrait');
        
        return $pdf->stream('Laporan-Penjualan.pdf');
    }

    public function cetakPembelian(Request $request)
    {
        // Jika start_date kosong, ambil tanggal terkecil dari database agar mencakup semua data
        $start_date = $request->query('start_date') ?? Pembelian::min('tanggal') ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = $request->query('end_date') ?? Carbon::now()->format('Y-m-d');
        $status = $request->query('status');

        $query = Pembelian::with(['supplier', 'detail.bahan'])
            ->whereBetween('tanggal', [$start_date, $end_date]);

        if ($status) {
            $query->where('status_pembayaran', $status);
        }

        $data = $query->orderBy('tanggal', 'desc')->get();

        $summary = [
            'total_pengeluaran' => $data->sum('total'),
            'total_lunas' => $data->where('status_pembayaran', 'lunas')->sum('total'),
            'total_hutang' => $data->where('status_pembayaran', 'hutang')->sum('total'),
            'count_transaksi' => $data->count(),
        ];

        $pdf = Pdf::loadView('admin.laporan.pdf_pembelian', compact('data', 'summary', 'start_date', 'end_date'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Pembelian.pdf');
    }
}