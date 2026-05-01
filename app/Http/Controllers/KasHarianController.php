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
    // Mengatur default: Tanggal 1 bulan ini
    $tgl_mulai = $request->get('tgl_mulai', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
    
    // Mengatur default: Tanggal terakhir bulan ini (bukan hari ini saja)
    $tgl_selesai = $request->get('tgl_selesai', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));

    // 1. Hitung Saldo Awal (Logic tarik mundur saldo riil)
    // Pastikan variabel saldo awal menggunakan $tgl_mulai yang sudah kita set di atas
    $totalMasukSejakMulai = KasHarian::where('tanggal', '>=', $tgl_mulai)->where('jenis', 'masuk')->sum('total_nominal');
    $totalKeluarSejakMulai = KasHarian::where('tanggal', '>=', $tgl_mulai)->where('jenis', 'keluar')->sum('total_nominal');
    $totalSaldoSekarang = Rekening::sum('saldo');
    $saldoAwalRiil = $totalSaldoSekarang - $totalMasukSejakMulai + $totalKeluarSejakMulai;

    // 2. Ambil data mutasi BESERTA detail itemnya
    $data = KasHarian::with([
        'rekening' => function ($q) {
            $q->withTrashed(); 
        },
        'details'
    ])
    ->whereBetween('tanggal', [$tgl_mulai, $tgl_selesai])
    ->orderBy('tanggal', 'asc')
    ->orderBy('created_at', 'asc')
    ->get();

    $pdf = Pdf::loadView('admin.keuangan.export_pdf', [
        'data' => $data,
        'tgl_mulai' => $tgl_mulai,
        'tgl_selesai' => $tgl_selesai,
        'saldoAwal' => $saldoAwalRiil,
    ])->setPaper('a4', 'landscape');

    return $pdf->stream('Laporan_Kas_Detailed.pdf');
}
}
