@extends('layouts.app')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Detail Penjualan #PJ-{{ str_pad($penjualan->id, 5, '0', STR_PAD_LEFT) }}</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0 gap-2 no-print">
        
        {{-- Tombol Invoice & SJ hanya muncul jika status BERHASIL atau RETURN --}}
        @if($penjualan->status == 'berhasil' || $penjualan->status == 'return')
            <a href="{{ route('penjualan.pdf', [$penjualan->id, 'type' => 'invoice']) }}" target="_blank" class="btn btn-outline-secondary shadow-md">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Invoice
            </a>
            <a href="{{ route('penjualan.pdf', [$penjualan->id, 'type' => 'sj']) }}" target="_blank" class="btn btn-outline-success shadow-md">
                <i data-lucide="truck" class="w-4 h-4 mr-2"></i> Surat Jalan
            </a>
        @endif

        
        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary shadow-md">Kembali</a>
    </div>
</div>

<div class="intro-y grid grid-cols-12 gap-5 mt-5">
    <div class="col-span-12 lg:col-span-4">
        <div class="box p-5 rounded-md">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate">Informasi Pelanggan</div>
            </div>
            <div class="flex items-center"> 
                <i data-lucide="user" class="w-4 h-4 text-slate-500 mr-2"></i> Nama: 
                <span class="font-medium ml-1">{{ $penjualan->client->nama ?? 'Pembeli Umum (Anonim)' }}</span> 
            </div>
            <div class="flex items-center mt-3"> 
                <i data-lucide="calendar" class="w-4 h-4 text-slate-500 mr-2"></i> Tanggal: 
                <span class="ml-1">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d F Y') }}</span> 
            </div>
            <div class="flex items-center mt-3 border-b border-slate-100 pb-5"> 
                <i data-lucide="activity" class="w-4 h-4 text-slate-500 mr-2"></i> Status: 
                @if($penjualan->status == 'berhasil')
                    <span class="bg-success/20 text-success rounded px-2 ml-1 text-xs font-medium uppercase">Berhasil</span>
                @elseif($penjualan->status == 'return')
                    <span class="bg-warning/20 text-warning rounded px-2 ml-1 text-xs font-medium uppercase">Dikembalikan (Return)</span>
                @else
                    <span class="bg-danger/20 text-danger rounded px-2 ml-1 text-xs font-medium uppercase">Dibatalkan (Cancel)</span>
                @endif
            </div>

            @if($penjualan->status == 'berhasil')
            <div class="mt-5 space-y-2">
                <p class="text-xs text-slate-500 mb-2 font-medium">TINDAKAN TRANSAKSI:</p>
                <button data-tw-toggle="modal" data-tw-target="#modal-return" class="btn btn-warning w-full text-white shadow-md">
                    <i data-lucide="refresh-ccw" class="w-4 h-4 mr-2"></i> Return Produk (Sebagian)
                </button>
                
                <form action="{{ route('penjualan.cancel', $penjualan->id) }}" method="POST" onsubmit="return confirm('Batalkan transaksi? Semua stok akan dikembalikan.')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-full mt-2">
                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Cancel Transaksi (Semua)
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <div class="col-span-12 lg:col-span-8">
        <div class="box p-5 rounded-md">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate">Item Terjual</div>
            </div>
            <div class="overflow-auto">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Nama Produk</th>
                            <th class="text-center whitespace-nowrap">Qty</th>
                            <th class="text-right whitespace-nowrap">Harga</th>
                            <th class="text-right whitespace-nowrap">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penjualan->detail as $item)
                        <tr>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $item->produk->nama }}</div>
                                <div class="text-slate-500 text-xs mt-0.5">{{ $item->produk->sku ?? 'No-SKU' }}</div>
                            </td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50">
                            <td colspan="3" class="text-right font-bold uppercase">Total Tagihan</td>
                            <td class="text-right font-bold text-primary text-lg">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal-return" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('penjualan.return', $penjualan->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Return Sebagian Produk</h2>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-4">Pilih jumlah item yang ingin dikembalikan ke stok.</div>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty Jual</th>
                                <th class="text-center w-32">Qty Return</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penjualan->detail as $index => $item)
                            <tr>
                                <td>{{ $item->produk->nama }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][produk_id]" value="{{ $item->produk_id }}">
                                    <input type="number" name="items[{{ $index }}][qty_return]" class="form-control" min="0" max="{{ $item->qty }}" value="0">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- <div class="mt-4">
                        <label class="form-label">Alasan Return</label>
                        <textarea name="keterangan" class="form-control" placeholder="Contoh: Barang cacat, Salah kirim..."></textarea>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Batal</button>
                    <button type="submit" class="btn btn-primary">Proses Return</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection