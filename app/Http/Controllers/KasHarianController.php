<?php

namespace App\Http\Controllers;

use App\Models\KasHarian;
use App\Models\KategoriKas;
use App\Models\Rekening;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KasHarianController extends Controller
{
    public function index(Request $request)
    {
        $tgl_mulai = $request->get('tgl_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tgl_selesai = $request->get('tgl_selesai', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $rekening_id = $request->get('rekening_id');
        $kategori_kas_id = $request->get('kategori_kas_id');

        $query = KasHarian::with(['details', 'kategoriKas', 'rekening' => function ($q) {
            $q->withTrashed();
        }])
        ->whereBetween('tanggal', [$tgl_mulai, $tgl_selesai]);

        // Filter dinamis
        if ($rekening_id) $query->where('rekening_id', $rekening_id);
        if ($kategori_kas_id) $query->where('kategori_kas_id', $kategori_kas_id);

        $kas = $query->orderBy('tanggal', 'asc')->orderBy('created_at', 'asc')->get();

        // Data pendukung untuk form/filter
        $rekenings = Rekening::all();
        $kategoris = KategoriKas::all(); 

        // Statistik Bulanan
        $totalMasukBulanIni = KasHarian::whereMonth('tanggal', date('m'))->whereYear('tanggal', date('Y'))->where('jenis', 'masuk')->sum('total_nominal');
        $totalKeluarBulanIni = KasHarian::whereMonth('tanggal', date('m'))->whereYear('tanggal', date('Y'))->where('jenis', 'keluar')->sum('total_nominal');
        $title = 'Data Kas Harian';
        return view('admin.keuangan.kas', compact(
            'kas', 'tgl_mulai', 'tgl_selesai', 'totalMasukBulanIni', 
            'totalKeluarBulanIni', 'rekenings', 'kategoris', 'title'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rekening_id' => 'required|exists:rekening,id',
            'kategori_kas_id' => 'required|exists:kategori_kas,id',
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'kategori' => 'required|in:modal,operasional',
            'nominal' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $totalNominal = 0;

                if ($request->kategori == 'operasional' && $request->has('items')) {
                    foreach ($request->items as $item) {
                        if (!empty($item['nama_item'])) {
                            $totalNominal += ($item['jumlah'] * $item['harga']);
                        }
                    }
                } else {
                    $totalNominal = $request->nominal ?? 0;
                }

                $kas = KasHarian::create([
                    'rekening_id' => $request->rekening_id,
                    'kategori_kas_id' => $request->kategori_kas_id, // Simpan kategori kas
                    'tanggal' => $request->tanggal,
                    'jenis' => $request->jenis,
                    'kategori' => $request->kategori,
                    'total_nominal' => $totalNominal,
                    'keterangan' => $request->keterangan,
                ]);

                if ($request->kategori == 'operasional' && $request->has('items')) {
                    foreach ($request->items as $item) {
                        if (!empty($item['nama_item'])) {
                            $kas->details()->create([
                                'nama_item' => $item['nama_item'],
                                'jumlah' => $item['jumlah'],
                                'harga' => $item['harga'],
                                'subtotal' => $item['jumlah'] * $item['harga']
                            ]);
                        }
                    }
                }

                $rekening = Rekening::findOrFail($request->rekening_id);
                $request->jenis == 'masuk' ? $rekening->increment('saldo', $totalNominal) : $rekening->decrement('saldo', $totalNominal);
            });

            return back()->with('success', 'Data kas berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $kas = KasHarian::findOrFail($id);

                // 3. Gunakan withTrashed() saat mencari rekening untuk kembalikan saldo
                // Karena mungkin saja user menghapus rekening tapi ingin menghapus transaksi lama
                $rekening = Rekening::withTrashed()->findOrFail($kas->rekening_id);

                if ($kas->jenis == 'masuk') {
                    $rekening->decrement('saldo', $kas->total_nominal);
                } else {
                    $rekening->increment('saldo', $kas->total_nominal);
                }

                $kas->delete();
            });
            return back()->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

public function exportPdf(Request $request)
{
    $tgl_mulai = $request->get('tgl_mulai', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
    $tgl_selesai = $request->get('tgl_selesai', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));
    $rekening_id = $request->get('rekening_id');

    // 1. Hitung Saldo Awal
    $querySaldoSekarang = \App\Models\Rekening::query();
    $queryMasuk = \App\Models\KasHarian::where('tanggal', '>=', $tgl_mulai)->where('jenis', 'masuk');
    $queryKeluar = \App\Models\KasHarian::where('tanggal', '>=', $tgl_mulai)->where('jenis', 'keluar');

    if ($rekening_id) {
        $querySaldoSekarang->where('id', $rekening_id);
        $queryMasuk->where('rekening_id', $rekening_id);
        $queryKeluar->where('rekening_id', $rekening_id);
    }

    $totalSaldoSekarang = $querySaldoSekarang->sum('saldo');
    $saldoAwalRiil = $totalSaldoSekarang - $queryMasuk->sum('total_nominal') + $queryKeluar->sum('total_nominal');

    // 2. Ambil data mutasi
    $queryData = \App\Models\KasHarian::with(['rekening' => fn($q) => $q->withTrashed(), 'details', 'kategoriKas'])
        ->whereBetween('tanggal', [$tgl_mulai, $tgl_selesai]);

    if ($rekening_id) $queryData->where('rekening_id', $rekening_id);

    $data = $queryData->orderBy('tanggal', 'asc')->orderBy('created_at', 'asc')->get();

    // 3. AMBIL SEMUA KATEGORI (Agar semua kolom muncul: Investasi, Produksi, dll)
    $kategoriTerlibat = \App\Models\KategoriKas::orderBy('nama', 'asc')->get();

    $namaRekening = $rekening_id ? \App\Models\Rekening::withTrashed()->find($rekening_id)->nama : 'Semua Rekening';

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.keuangan.export_pdf', [
        'data' => $data,
        'tgl_mulai' => $tgl_mulai,
        'tgl_selesai' => $tgl_selesai,
        'saldoAwal' => $saldoAwalRiil,
        'namaRekening' => $namaRekening,
        'kategoriTerlibat' => $kategoriTerlibat
    ])->setPaper('a4', 'landscape'); 

    return $pdf->stream('Laporan_Kas_' . $namaRekening . '.pdf');
}
}
