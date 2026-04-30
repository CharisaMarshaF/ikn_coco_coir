<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokProduk;
use App\Models\HasilProduksi;
use App\Models\HasilProduksiDetail;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilProduksiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $hasilProduksi = HasilProduksi::with(['details.produk', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->where('kode_produksi', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        $title = 'Data Hasil Produksi';
        return view('admin.hasil_produksi.index', compact('hasilProduksi', 'title'));
    }

    public function create()
    {
        $produkProses = Produk::with('stok')
            ->whereIn('jenis', ['proses', 'jadi'])
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
                $itemPola = $item['kategori_pola'] ?? null;

                // 1. Simpan Detail
                HasilProduksiDetail::create([
                    'hasil_produksi_id' => $hasil->id,
                    'produk_id' => $produk->id,
                    'qty' => $qtyMasuk,
                    'kategori_pola' => $itemPola 
                ]);

                // 2. Update Stok: Jika jenis 'jadi' ATAU pola 'Jadi'
                if ($produk->jenis === 'jadi' || $itemPola === 'Jadi') {
                    $stokRecord = StokProduk::firstOrCreate(
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
            // Load detail dan produk
            $hasil = HasilProduksi::with('details.produk')->findOrFail($id);

            foreach ($hasil->details as $detail) {
                // Perbaikan: Cek pola dari $detail, bukan dari $hasil
                if ($detail->produk->jenis === 'jadi' || $detail->kategori_pola === 'Jadi') {
                    $stokRecord = StokProduk::where('produk_id', $detail->produk_id)->first();

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
                            'keterangan' => "Pembatalan Produksi #{$hasil->kode_produksi}",
                            'user_id' => auth()->id()
                        ]);
                    }
                }
            }

            $hasil->delete(); // Ini akan menghapus detail jika Anda menggunakan onDelete('cascade') di DB
            DB::commit();
            return back()->with('success', 'Data berhasil dibatalkan dan stok telah disesuaikan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal Hapus: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $hasil = HasilProduksi::with(['details.produk', 'user'])->findOrFail($id);
        $title = 'Detail Hasil Produksi ' . $hasil->kode_produksi;
        return view('admin.hasil_produksi.show', compact('hasil', 'title'));
    }
}