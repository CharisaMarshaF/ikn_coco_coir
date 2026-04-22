<?php
namespace App\Http\Controllers;

use App\Models\KasHarian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KasHarianController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        
        $kas = KasHarian::whereDate('tanggal', $tanggal)
                ->orderBy('created_at', 'desc')
                ->get();

        // Hitung Summary
        $totalMasuk = KasHarian::whereDate('tanggal', $tanggal)->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = KasHarian::whereDate('tanggal', $tanggal)->where('jenis', 'keluar')->sum('nominal');
        $saldoHariIni = $totalMasuk - $totalKeluar;

        return view('admin.keuangan.kas', compact('kas', 'tanggal', 'totalMasuk', 'totalKeluar', 'saldoHariIni'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'required'
        ]);

        KasHarian::create($request->all());

        return back()->with('success', 'Data kas berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        KasHarian::findOrFail($id)->delete();
        return back()->with('success', 'Data kas berhasil dihapus.');
    }
}