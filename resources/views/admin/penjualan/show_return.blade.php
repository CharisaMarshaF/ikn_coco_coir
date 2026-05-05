@extends('layouts.app')

@section('content')
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Detail Return #{{ $return->nomor_return }}</h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0 gap-2 no-print">
            <a href="{{ route('penjualan.show', $return->penjualan_id) }}" class="btn btn-secondary shadow-md">Kembali ke
                Penjualan</a>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-5 mt-5">
        <!-- INFO RETURN -->
        <div class="col-span-12 lg:col-span-4">
            <div class="box p-5">
                <div class="font-medium text-base border-b border-slate-200/60 pb-3 mb-3">Informasi Return</div>
                <div class="leading-relaxed">
                    <p><b>Pelanggan:</b> {{ $return->penjualan->client->nama ?? 'Umum' }}</p>
                    <p><b>Tanggal Return:</b> {{ \Carbon\Carbon::parse($return->tanggal)->format('d F Y') }}</p>
                    <p><b>Asal Transaksi:</b> #PJ-{{ str_pad($return->penjualan_id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>

                <div class="mt-5 pt-5 border-t border-slate-200">
                    @if ($return->is_resend == 1)
                        <!-- Tampilan jika sudah dikirim ulang -->
                        <div class="alert alert-outline-success flex items-center mb-2 p-3 border-dashed border-2">
                            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                            <span class="font-medium">Sudah Dikirim Ulang</span>
                        </div>
                        <button class="btn btn-secondary w-full" disabled>
                            <i data-lucide="truck" class="w-4 h-4 mr-2 text-slate-500"></i> Kirim Ulang Selesai
                        </button>
                    @else
                        <!-- Tampilan tombol asli jika belum dikirim ulang -->
                        <form action="{{ route('penjualan.resend_return', $return->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-xs">Tanggal Pengiriman Pengganti</label>
                                <input type="date" name="tanggal_kirim" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-primary w-full shadow-md"
                                onclick="return confirm('Konfirmasi pengiriman ulang barang?')">
                                <i data-lucide="truck" class="w-4 h-4 mr-2"></i> Kirim Ulang Sekarang
                            </button>
                        </form>
                    @endif
                    
                </div>
            </div>
        </div>

        <!-- DAFTAR BARANG RETURN -->
        <div class="col-span-12 lg:col-span-8">
            <div class="box p-5">
                <div class="font-medium text-base border-b border-slate-200/60 pb-3 mb-3">Barang yang Dikembalikan</div>
                <div class="overflow-x-auto">
                    <table class="table table-striped">
                        <thead>
                            <tr class="bg-slate-50">
                                <th>Nama Produk</th>
                                <th class="text-center">Qty Return</th>
                                <th class="text-right">Harga (Adjustment)</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($return->detail as $item)
                                <tr>
                                    <td>{{ $item->produk->nama }}</td>
                                    <td class="text-center font-bold">{{ $item->qty + 0 }}</td>
                                    <!-- Harga ditampilkan 0 karena ini detail barang return -->
                                    <td class="text-right text-slate-400">Rp 0</td>
                                    <td class="text-right text-slate-400">Rp 0</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 alert alert-outline-secondary italic text-xs">
                    * Harga ditampilkan Rp 0 sebagai penyesuaian stok return/kirim ulang tanpa mengubah nilai piutang awal
                    secara langsung di dokumen pengiriman ini.
                </div>
            </div>
        </div>
    </div>
@endsection
