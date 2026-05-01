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

        $query = PengambilanBahan::with(['details.bahan' => function ($q) {
            $q->withTrashed();
        }]);

        // Jika user melakukan filter tanggal manual
        if ($dari && $sampai) {
            $query->whereBetween('tanggal', [$dari, $sampai]);
        } else {
            // Default: Tampilkan hanya bulan ini
            $query->whereMonth('tanggal', date('m'))
                ->whereYear('tanggal', date('Y'));
        }

        // Mengurutkan dari yang paling baru (berdasarkan tanggal dan ID)
        $pengambilan = $query->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $title = 'Data Pengambilan Bahan';
        return view('admin.pengambilan.index', compact('pengambilan', 'title', 'dari', 'sampai'));
    }
public function cetakPdf(Request $request)
{
    // Menggunakan Carbon untuk tanggal default
    $dari = $request->get('dari') ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
    $sampai = $request->get('sampai') ?? \Carbon\Carbon::now()->format('Y-m-d');

    // Query Detail agar lebih mudah menampilkan list per item di PDF
    $data = PengambilanBahanDetail::with(['bahan' => function ($q) {
            $q->withTrashed(); 
        }, 'pengambilan'])
        ->whereHas('pengambilan', function($q) use ($dari, $sampai) {
            $q->whereBetween('tanggal', [$dari, $sampai]);
        })
        ->get();

    // Summary akumulasi per bahan
    $summary = $data->groupBy('bahan_id')->map(function ($items) {
        $bahan = $items->first()->bahan;
        return [
            'nama' => $bahan ? ($bahan->trashed() ? $bahan->nama . ' (Dihapus)' : $bahan->nama) : 'Bahan Tidak Ditemukan',
            'satuan' => $bahan->satuan ?? '-',
            'total_qty' => $items->sum('qty')
        ];
    });

    $pdf = Pdf::loadView('admin.pengambilan.pdf', [
        'data' => $data,
        'summary' => $summary,
        'dari' => $dari,
        'sampai' => $sampai,
        'konfigurasi' => \App\Models\CompanyProfile::first()
    ])->setPaper('a4', 'portrait');

    return $pdf->stream("Laporan_Pengambilan_{$dari}_sd_{$sampai}.pdf");
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
                    // Gunakan withTrashed() agar stok tetap bisa dikembalikan 
                    // meskipun bahan bakunya sendiri sudah dihapus dari master data
                    $stok = StokBahan::where('bahan_id', $detail->bahan_id)->first();

                    if ($stok) {
                        $stokLama = $stok->jumlah;
                        $stokBaru = $stokLama + $detail->qty;
                        $stok->update(['jumlah' => $stokBaru]);

                        // Catat Log
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
                // Ini akan melakukan Soft Delete jika trait SoftDeletes ada di model
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
