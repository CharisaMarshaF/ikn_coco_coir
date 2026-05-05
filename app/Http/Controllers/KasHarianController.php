<?php

namespace App\Http\Controllers;

use App\Models\KasHarian;
use App\Models\Rekening;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasHarianController extends Controller
{
    public function index(Request $request)
    {
         $tgl_mulai = $request->get('tgl_mulai', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
    
    // Mengatur default: Tanggal terakhir bulan ini (bukan hari ini saja)
    $tgl_selesai = $request->get('tgl_selesai', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));

        $kas = KasHarian::with(['rekening' => function ($q) {
            $q->withTrashed();
        }])
            ->whereBetween('tanggal', [$tgl_mulai, $tgl_selesai])
            ->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // 2. Hanya tampilkan rekening yang AKTIF untuk pilihan di Form Input
        $rekenings = Rekening::all();

        // Logika Kotak Besar (Per Bulan ini)
        $bulanSekarang = date('m');
        $tahunSekarang = date('Y');

        $totalMasukBulanIni = KasHarian::whereMonth('tanggal', $bulanSekarang)
            ->whereYear('tanggal', $tahunSekarang)
            ->where('jenis', 'masuk')->sum('total_nominal');
        $totalKeluarBulanIni = KasHarian::whereMonth('tanggal', $bulanSekarang)
            ->whereYear('tanggal', $tahunSekarang)
            ->where('jenis', 'keluar')->sum('total_nominal');

        $title = 'Laporan Kas Harian';

        return view('admin.keuangan.kas', compact(
            'kas',
            'tgl_mulai',
            'tgl_selesai',
            'totalMasukBulanIni',
            'totalKeluarBulanIni',
            'rekenings',
            'title'
        ));
    }

    public function store(Request $request)
    {
        // Validasi yang lebih fleksibel
        $request->validate([
            'rekening_id' => 'required|exists:rekening,id',
            'tanggal'     => 'required|date',
            'jenis'       => 'required|in:masuk,keluar',
            'kategori'    => 'required|in:modal,operasional',
            'nominal'     => 'nullable|numeric|min:0',
            'items'       => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $totalNominal = 0;

                // Hitung total berdasarkan kategori
                if ($request->kategori == 'operasional' && $request->has('items')) {
                    foreach ($request->items as $item) {
                        if (!empty($item['nama_item'])) {
                            $totalNominal += ($item['jumlah'] * $item['harga']);
                        }
                    }
                } else {
                    $totalNominal = $request->nominal ?? 0;
                }

                // Simpan Header Kas
                $kas = KasHarian::create([
                    'rekening_id'   => $request->rekening_id,
                    'tanggal'       => $request->tanggal,
                    'jenis'         => $request->jenis,
                    'kategori'      => $request->kategori,
                    'total_nominal' => $totalNominal,
                    'keterangan'    => $request->keterangan,
                ]);

                // Simpan Detail jika Operasional
                if ($request->kategori == 'operasional' && $request->has('items')) {
                    foreach ($request->items as $item) {
                        if (!empty($item['nama_item'])) {
                            $kas->details()->create([
                                'nama_item' => $item['nama_item'],
                                'jumlah'    => $item['jumlah'],
                                'harga'     => $item['harga'],
                                'subtotal'  => $item['jumlah'] * $item['harga']
                            ]);
                        }
                    }
                }

                // Update Saldo Rekening
                $rekening = Rekening::findOrFail($request->rekening_id);
                if ($request->jenis == 'masuk') {
                    $rekening->increment('saldo', $totalNominal);
                } else {
                    $rekening->decrement('saldo', $totalNominal);
                }
            });

            return back()->with('success', 'Data kas berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
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
    $rekening_id = $request->get('rekening_id'); // Ambil filter rekening

    // 1. Hitung Saldo Awal Berdasarkan Filter
    $querySaldoSekarang = Rekening::query();
    $queryMasuk = KasHarian::where('tanggal', '>=', $tgl_mulai)->where('jenis', 'masuk');
    $queryKeluar = KasHarian::where('tanggal', '>=', $tgl_mulai)->where('jenis', 'keluar');

    if ($rekening_id) {
        $querySaldoSekarang->where('id', $rekening_id);
        $queryMasuk->where('rekening_id', $rekening_id);
        $queryKeluar->where('rekening_id', $rekening_id);
    }

    $totalSaldoSekarang = $querySaldoSekarang->sum('saldo');
    $totalMasukSejakMulai = $queryMasuk->sum('total_nominal');
    $totalKeluarSejakMulai = $queryKeluar->sum('total_nominal');
    
    $saldoAwalRiil = $totalSaldoSekarang - $totalMasukSejakMulai + $totalKeluarSejakMulai;

    // 2. Ambil data mutasi dengan filter rekening
    $queryData = KasHarian::with([
        'rekening' => function ($q) { $q->withTrashed(); },
        'details'
    ])
    ->whereBetween('tanggal', [$tgl_mulai, $tgl_selesai]);

    if ($rekening_id) {
        $queryData->where('rekening_id', $rekening_id);
    }

    $data = $queryData->orderBy('tanggal', 'asc')
                      ->orderBy('created_at', 'asc')
                      ->get();

    // Ambil info nama rekening untuk judul PDF jika ada filter
    $namaRekening = $rekening_id ? Rekening::withTrashed()->find($rekening_id)->nama : 'Semua Rekening';

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.keuangan.export_pdf', [
        'data' => $data,
        'tgl_mulai' => $tgl_mulai,
        'tgl_selesai' => $tgl_selesai,
        'saldoAwal' => $saldoAwalRiil,
        'namaRekening' => $namaRekening, // Opsional: tampilkan di PDF
    ])->setPaper('a4', 'landscape');

    return $pdf->stream('Laporan_Kas_'.$namaRekening.'.pdf');
}
}
