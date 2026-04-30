<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\PengambilanBahan;
use App\Models\PengambilanBahanDetail;
use App\Models\StockLog;
use App\Models\StokBahan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengambilanBahanController extends Controller
{
public function index(Request $request)
    {
        $dari = $request->get('dari');
        $sampai = $request->get('sampai');

        $pengambilan = PengambilanBahan::with(['details.bahan'])
            ->when($dari && $sampai, function($query) use ($dari, $sampai) {
                $query->whereBetween('tanggal', [$dari, $sampai]);
            })
            ->latest()
            ->paginate(10);

        $title = 'Data Pengambilan Bahan';
        return view('admin.pengambilan.index', compact('pengambilan', 'title', 'dari', 'sampai'));
    }

public function cetakPdf(Request $request)
{
    $dari = $request->get('dari');
    $sampai = $request->get('sampai');

    // 1. Inisialisasi Query
    $query = PengambilanBahanDetail::with(['bahan', 'pengambilan']);
    
    // 2. Tambahkan filter HANYA JIKA input tanggal ada
    if ($dari && $sampai) {
        $query->whereHas('pengambilan', function($q) use ($dari, $sampai) {
            $q->whereBetween('tanggal', [$dari, $sampai]);
        });
    }

    // 3. Ambil data (Sekarang $data pasti terdefinisi, baik terfilter maupun tidak)
    $data = $query->get();

    // 4. Hitung Ringkasan / Subtotal dari hasil $data di atas
    $summary = $data->groupBy('bahan_id')->map(function ($items) {
        return [
            'nama' => $items->first()->bahan->nama ?? 'Bahan Dihapus',
            'satuan' => $items->first()->bahan->satuan ?? '',
            'total_qty' => $items->sum('qty')
        ];
    });

    // 5. Load View PDF
    $pdf = Pdf::loadView('admin.pengambilan.pdf', compact('data', 'summary', 'dari', 'sampai'));
    
    // 6. Penamaan File yang aman (Tanpa karakter / atau \)
    if ($dari && $sampai) {
        $filename = "Laporan_Pengambilan_" . $dari . "_sd_" . $sampai . ".pdf";
    } else {
        $filename = "Semua_Laporan_Pengambilan.pdf";
    }

    return $pdf->stream($filename);
}
    public function create()
    {
        $bahanBaku = BahanBaku::with('stok')->orderBy('nama', 'asc')->get();
        $title = 'Tambah Pengambilan';
        return view('admin.pengambilan.create', compact('bahanBaku', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'bahan_ids' => 'required|array|min:1',
            'qtys' => 'required|array',
            'qtys.*' => 'required|numeric|min:0.01',
        ]);

        // VALIDASI SERVER-SIDE: Cek duplikasi bahan_id dalam satu request
        if (count($request->bahan_ids) !== count(array_unique($request->bahan_ids))) {
            return redirect()->back()->with('error', 'Ada bahan yang duplikat dalam daftar!')->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
                // 1. Simpan Master
                $pengambilan = PengambilanBahan::create([
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan ?? 'Pengambilan produksi',
                ]);

                foreach ($request->bahan_ids as $index => $bahanId) {
                    $qtyAmbil = $request->qtys[$index];

                    $stok = StokBahan::where('bahan_id', $bahanId)->first();
                    $stokLama = $stok ? $stok->jumlah : 0;

                    if ($stokLama < $qtyAmbil) {
                        throw new \Exception("Stok bahan ID $bahanId tidak mencukupi!");
                    }

                    // 2. Simpan Detail
                    PengambilanBahanDetail::create([
                        'pengambilan_id' => $pengambilan->id,
                        'bahan_id' => $bahanId,
                        'qty' => $qtyAmbal ?? $qtyAmbil,
                    ]);

                    // 3. Update Stok
                    $stokBaru = $stokLama - $qtyAmbil;
                    $stok->update(['jumlah' => $stokBaru]);

                    // 4. Stock Log
                    StockLog::create([
                        'item_id' => $bahanId,
                        'item_type' => 'bahan_baku',
                        'jenis' => 'keluar',
                        'jumlah' => $qtyAmbil,
                        'stok_sebelum' => $stokLama,
                        'stok_sesudah' => $stokBaru,
                        'sumber' => 'produksi',
                        'keterangan' => "Pengambilan bahan (#{$pengambilan->id})",
                        'user_id' => auth()->id()
                    ]);
                }
            });

            // PERBAIKAN: Pastikan mengarah ke 'pengambilan.index' sesuai route Anda
            return redirect()->route('pengambilan.index')->with('success', 'Berhasil mencatat pengambilan bahan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $pengambilan = PengambilanBahan::findOrFail($id);
                foreach ($pengambilan->details as $detail) {
                    $stok = StokBahan::where('bahan_id', $detail->bahan_id)->first();
                    if ($stok) {
                        $stokLama = $stok->jumlah;
                        $stokBaru = $stokLama + $detail->qty;
                        $stok->update(['jumlah' => $stokBaru]);

                        StockLog::create([
                            'item_id' => $detail->bahan_id,
                            'item_type' => 'bahan_baku',
                            'jenis' => 'masuk',
                            'jumlah' => $detail->qty,
                            'stok_sebelum' => $stokLama,
                            'stok_sesudah' => $stokBaru,
                            'sumber' => 'manual',
                            'keterangan' => "Revert stok: Penghapusan pengambilan #{$id}",
                            'user_id' => auth()->id()
                        ]);
                    }
                }
                $pengambilan->delete();
            });
            return redirect()->back()->with('success', 'Data berhasil dihapus dan stok dikembalikan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    public function laporan(Request $request)
    {
        $dari = $request->get('dari');
        $sampai = $request->get('sampai');

        // Ambil data detail bahan yang diambil berdasarkan range tanggal
        $query = PengambilanBahanDetail::with(['bahan', 'pengambilan'])
            ->whereHas('pengambilan', function ($q) use ($dari, $sampai) {
                if ($dari && $sampai) {
                    $q->whereBetween('tanggal', [$dari, $sampai]);
                }
            });

        $data = $query->get();

        // Hitung total akumulasi per bahan untuk ringkasan di bawah
        $summary = $data->groupBy('bahan_id')->map(function ($items) {
            return [
                'nama' => $items->first()->bahan->nama,
                'satuan' => $items->first()->bahan->satuan,
                'total_qty' => $items->sum('qty')
            ];
        });

        $title = 'Laporan Pengambilan Bahan';
        return view('admin.pengambilan.laporan', compact('data', 'summary', 'dari', 'sampai', 'title'));
    }
}
