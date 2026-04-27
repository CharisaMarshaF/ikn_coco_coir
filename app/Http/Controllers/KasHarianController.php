<?php 

namespace App\Http\Controllers;

use App\Models\KasHarian;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasHarianController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        
        // Load relasi rekening agar bisa menampilkan nama rekening di tabel
        $kas = KasHarian::with('rekening')
                ->whereDate('tanggal', $tanggal)
                ->orderBy('created_at', 'desc')
                ->get();

        // Ambil daftar rekening untuk dropdown di modal tambah
        $rekenings = Rekening::all();

        // Hitung Summary
        $totalMasuk = KasHarian::whereDate('tanggal', $tanggal)->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = KasHarian::whereDate('tanggal', $tanggal)->where('jenis', 'keluar')->sum('nominal');
        $saldoHariIni = $totalMasuk - $totalKeluar;

        return view('admin.keuangan.kas', compact('kas', 'tanggal', 'totalMasuk', 'totalKeluar', 'saldoHariIni', 'rekenings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rekening_id' => 'required|exists:rekening,id',
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Simpan data Kas
                KasHarian::create($request->all());

                // 2. Update Saldo di Rekening
                $rekening = Rekening::findOrFail($request->rekening_id);
                if ($request->jenis == 'masuk') {
                    $rekening->increment('saldo', $request->nominal);
                } else {
                    $rekening->decrement('saldo', $request->nominal);
                }
            });

            return back()->with('success', 'Data kas berhasil ditambahkan dan saldo rekening telah diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $kas = KasHarian::findOrFail($id);
                $rekening = Rekening::findOrFail($kas->rekening_id);

                // Kembalikan saldo sebelum data dihapus
                // Jika kas dihapus adalah 'masuk', maka saldo rekening harus dikurangi
                if ($kas->jenis == 'masuk') {
                    $rekening->decrement('saldo', $kas->nominal);
                } else {
                    $rekening->increment('saldo', $kas->nominal);
                }

                $kas->delete();
            });

            return back()->with('success', 'Data kas berhasil dihapus dan saldo rekening telah disesuaikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}