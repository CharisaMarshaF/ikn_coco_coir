<?php
namespace App\Http\Controllers;

use App\Models\TransaksiKeuangan;
use App\Models\KasHarian;
use App\Models\Rekening;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // Data Transaksi Keuangan (Rekening/Bank)
        $transaksi = TransaksiKeuangan::with('rekening')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('created_at', 'desc')
            ->get();

        // Data Kas Harian (Tunai)
        $kas = KasHarian::whereDate('tanggal', $tanggal)
            ->orderBy('created_at', 'desc')
            ->get();

        $rekening = Rekening::all();

        // Ringkasan Dashboard Ringkas
        $totalBank = $transaksi->where('jenis', 'masuk')->sum('nominal') - $transaksi->where('jenis', 'keluar')->sum('nominal');
        $totalTunai = $kas->where('jenis', 'masuk')->sum('nominal') - $kas->where('jenis', 'keluar')->sum('nominal');

        return view('admin.keuangan.index', compact('transaksi', 'kas', 'rekening', 'tanggal', 'totalBank', 'totalTunai'));
    }

    public function storeTransaksi(Request $request)
    {
        $data = $request->validate([
            'rekening_id' => 'required',
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'sumber' => 'required',
            'nominal' => 'required|numeric',
            'keterangan' => 'nullable'
        ]);

        TransaksiKeuangan::create($data);
        return back()->with('success', 'Transaksi berhasil dicatat.');
    }

    public function storeKas(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'nominal' => 'required|numeric',
            'keterangan' => 'required'
        ]);

        KasHarian::create($data);
        return back()->with('success', 'Kas harian berhasil dicatat.');
    }
}