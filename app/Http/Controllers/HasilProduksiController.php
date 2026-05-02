<?php

namespace App\Http\Controllers;

use App\Models\HasilProduksi;
use App\Models\HasilProduksiDetail;
use App\Models\Produk;
use App\Models\StockLog;
use App\Models\StokProduk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilProduksiController extends Controller
{
public function index(Request $request)
{
    $search = $request->get('search');
    
    // Default filter: Bulan ini (dari tanggal 1 sampai hari ini)
    $tgl_mulai = $request->get('tgl_mulai', date('Y-m-01'));
    $tgl_selesai = $request->get('tgl_selesai', date('Y-m-d'));

    $hasilProduksi = HasilProduksi::with(['details.produk' => function($q) {
            $q->withTrashed();
        }, 'user'])
        ->when($search, function ($query) use ($search) {
            $query->where('kode_produksi', 'like', "%{$search}%");
        })
        // Filter Tanggal
        ->whereBetween('tanggal', [$tgl_mulai, $tgl_selesai])
        // Urutan terbaru
        ->orderBy('tanggal', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    $title = 'Data Hasil Produksi';
    return view('admin.hasil_produksi.index', compact('hasilProduksi', 'title', 'tgl_mulai', 'tgl_selesai'));
}

public function cetakLaporan(Request $request)
{
    $tgl_mulai = $request->get('start_date') ?? $request->get('tgl_mulai') ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
    $tgl_selesai = $request->get('end_date') ?? $request->get('tgl_selesai') ?? \Carbon\Carbon::now()->format('Y-m-d');

    $data = HasilProduksi::with(['details.produk' => function($q) {
            $q->withTrashed(); 
        }, 'user'])
        ->whereBetween('tanggal', [$tgl_mulai, $tgl_selesai])
        ->orderBy('tanggal', 'asc')
        ->get();

    // --- TAMBAHKAN LOGIC SUMMARY BERIKUT ---
    $summary = [];
    foreach($data as $row) {
        foreach($row->details as $det) {
            $prodName = $det->produk ? ($det->produk->trashed() ? $det->produk->nama . ' (Dihapus)' : $det->produk->nama) : 'N/A';
            if(!isset($summary[$prodName])) {
                $summary[$prodName] = [
                    'qty' => 0,
                    'satuan' => $det->produk->satuan ?? '-'
                ];
            }
            $summary[$prodName]['qty'] += $det->qty;
        }
    }
    // ---------------------------------------

    $pdf = Pdf::loadView('admin.hasil_produksi.pdf_laporan', [
        'data' => $data,
        'summary' => $summary,
        'tgl_mulai' => $tgl_mulai,
        'tgl_selesai' => $tgl_selesai,
        'konfigurasi' => \App\Models\CompanyProfile::first()
    ])->setPaper('a4', 'portrait');

    return $pdf->stream('Laporan-Produksi-'.$tgl_mulai.'-to-'.$tgl_selesai.'.pdf');
}
    public function create()
    {
        // Saat mencatat produksi baru, hanya tampilkan produk yang BELUM dihapus
        $produkProses = Produk::with('stok')
            ->whereIn('jenis', ['proses', 'jadi'])
            ->orderBy('nama', 'asc')
            ->get();

        $title = 'Catat Hasil Produksi';
        return view('admin.hasil_produksi.create', compact('produkProses', 'title'));
    }

public function store(Request $request)
{
    $request->validate([
        'tanggal' => 'required|date',
        'items' => 'required|array|min:1',
        'items.*.produk_id' => 'required|exists:produk,id',
        'items.*.qty' => 'required|numeric|min:0.01',
    ]);

    try {
        DB::beginTransaction();

        $hasil = HasilProduksi::create([
            'tanggal' => $request->tanggal,
            'kode_produksi' => 'HPR-' . date('YmdHis'),
            'keterangan' => $request->keterangan,
            'user_id' => auth()->id(),
        ]);

        foreach ($request->items as $item) {
            $produk = Produk::findOrFail($item['produk_id']);
            $qtyMasuk = $item['qty'];
            $itemPola = $item['kategori_pola'] ?? 'Jadi'; // Default fallback

            // 1. Simpan Detail (Akan tetap tersimpan meskipun produk_id sama, karena baris berbeda)
            HasilProduksiDetail::create([
                'hasil_produksi_id' => $hasil->id,
                'produk_id' => $produk->id,
                'qty' => $qtyMasuk,
                'kategori_pola' => $itemPola
            ]);

            // 2. Update Stok: Hanya jika pola yang dihasilkan adalah 'Jadi'
            if ($produk->jenis === 'jadi' || $itemPola === 'Jadi') {
                $stokRecord = StokProduk::withTrashed()->firstOrCreate(
                    ['produk_id' => $produk->id],
                    ['jumlah' => 0]
                );

                $stokLama = $stokRecord->jumlah;
                $stokBaru = $stokLama + $qtyMasuk;
                $stokRecord->update(['jumlah' => $stokBaru]);

                StockLog::create([
                    'item_id' => $produk->id,
                    'item_type' => 'produk',
                    'jenis' => 'masuk',
                    'jumlah' => $qtyMasuk,
                    'stok_sebelum' => $stokLama,
                    'stok_sesudah' => $stokBaru,
                    'sumber' => 'produksi',
                    'keterangan' => "Hasil Produksi #{$hasil->kode_produksi} (Pola: {$itemPola})",
                    'user_id' => auth()->id()
                ]);
            }
        }

        DB::commit();
        return redirect()->route('hasil-produksi.index')->with('success', 'Hasil produksi berhasil dicatat.');
    } catch (\Exception $e) {
        DB::rollback();
        return back()->withInput()->with('error', 'Gagal Simpan: ' . $e->getMessage());
    }
}

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            // Load detail dengan produk yang mungkin sudah dihapus (soft delete)
            $hasil = HasilProduksi::with(['details.produk' => function ($q) {
                $q->withTrashed();
            }])->findOrFail($id);

            foreach ($hasil->details as $detail) {
                // Cek apakah produk atau pola mengharuskan pengurangan stok kembali (revert)
                if ($detail->produk->jenis === 'jadi' || $detail->kategori_pola === 'Jadi') {
                    // Cari record stok bahkan jika produk induknya sudah dihapus
                    $stokRecord = StokProduk::withTrashed()->where('produk_id', $detail->produk_id)->first();

                    if ($stokRecord) {
                        $stokLama = $stokRecord->jumlah;
                        $stokBaru = $stokLama - $detail->qty;
                        $stokRecord->update(['jumlah' => $stokBaru]);

                        StockLog::create([
                            'item_id' => $detail->produk_id,
                            'item_type' => 'produk',
                            'jenis' => 'keluar',
                            'jumlah' => $detail->qty,
                            'stok_sebelum' => $stokLama,
                            'stok_sesudah' => $stokBaru,
                            'sumber' => 'produksi',
                            'keterangan' => "Pembatalan Produksi #{$hasil->kode_produksi} (Revert Stok)",
                            'user_id' => auth()->id()
                        ]);
                    }
                }
            }

            // Jika HasilProduksi juga menggunakan SoftDeletes, data tidak hilang permanen
            $hasil->delete();

            DB::commit();
            return back()->with('success', 'Data berhasil dibatalkan dan stok telah disesuaikan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal Hapus: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // Pastikan eager loading menggunakan withTrashed()
        $hasil = HasilProduksi::with(['details.produk' => function ($q) {
            $q->withTrashed();
        }, 'user'])->findOrFail($id);

        $title = 'Detail Hasil Produksi ' . $hasil->kode_produksi;
        return view('admin.hasil_produksi.show', compact('hasil', 'title'));
    }
}
