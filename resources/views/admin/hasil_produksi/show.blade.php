@extends('layouts.app')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Detail Hasil Produksi</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('hasil-produksi.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
       
    </div>
</div>

<div class="intro-y grid grid-cols-12 gap-5 mt-5">
    <!-- Info Utama (Header) -->
    <div class="col-span-12 lg:col-span-4">
        <div class="box p-5 rounded-md">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate">Informasi Transaksi</div>
            </div>
            <div class="flex items-center">
                <i data-lucide="hash" class="w-4 h-4 text-slate-500 mr-2"></i> Kode:
                <span class="font-medium ml-1 text-primary">{{ $hasil->kode_produksi }}</span>
            </div>
            <div class="flex items-center mt-3">
                <i data-lucide="calendar" class="w-4 h-4 text-slate-500 mr-2"></i> Tanggal:
                <span class="ml-1">{{ \Carbon\Carbon::parse($hasil->tanggal)->format('d F Y') }}</span>
            </div>
            <div class="flex items-center mt-3 border-b border-slate-100 pb-5">
                <i data-lucide="user" class="w-4 h-4 text-slate-500 mr-2"></i> Operator:
                <span class="ml-1">{{ $hasil->user->name ?? 'System' }}</span>
            </div>

            <div class="mt-5">
                <div class="text-xs text-slate-500 mb-2 font-medium">CATATAN:</div>
                <div class="italic text-slate-600 text-sm bg-slate-50 p-3 rounded border border-dashed border-slate-200">
                    "{{ $hasil->keterangan ?? 'Tidak ada catatan.' }}"
                </div>
            </div>

          
        </div>
    </div>

    <!-- Tabel Item (Pola di samping Produk) -->
    <div class="col-span-12 lg:col-span-8">
        <div class="box p-5 rounded-md">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate text-success">Item Hasil Produksi</div>
            </div>
            <div class="overflow-auto lg:overflow-visible">
                <table class="table table-report table-striped mt-2">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">PRODUK & POLA</th>
                            <th class="whitespace-nowrap text-center">JENIS</th>
                            <th class="whitespace-nowrap text-right">QTY MASUK</th>
                            <th class="whitespace-nowrap text-center">STATUS STOK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hasil->details as $detail)
                        <tr class="intro-x">
                            <td class="!py-4">
                                <div class="font-medium whitespace-nowrap">{{ $detail->produk->nama }}</div>
                                <!-- Kategori Pola ditaruh di bawah nama produk sebagai badge -->
                                <div class="mt-1">
                                    @if($detail->kategori_pola)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $detail->kategori_pola == 'Jadi' ? 'bg-success/10 text-success border border-success/20' : 'bg-warning/10 text-warning border border-warning/20' }}">
                                            Pola: {{ $detail->kategori_pola }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[10px] italic">Tanpa Pola</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="text-xs text-slate-500">{{ ucfirst($detail->produk->jenis) }}</div>
                            </td>
                            <td class="text-right font-bold text-success">
                                {{ number_format($detail->qty, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($detail->produk->jenis == 'jadi' || $detail->kategori_pola == 'Jadi')
                                    <div class="flex items-center justify-center text-success text-xs">
                                        <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i> Stok Bertambah
                                    </div>
                                @else
                                    <div class="flex items-center justify-center text-slate-400 text-xs italic">
                                        <i data-lucide="slash" class="w-3 h-3 mr-1"></i> Hanya Log
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-end mt-5 p-5 border-t border-slate-200/60 bg-slate-50 rounded-b-md">
                <div class="text-slate-500 mr-5">Ringkasan:</div>
                <div class="text-xl font-medium text-primary">{{ $hasil->details->count() }} Produk Dihasilkan</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus (Sama seperti sebelumnya) -->
<div id="modal-delete-{{ $hasil->id }}" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-0">
                <form action="{{ route('hasil-produksi.destroy', $hasil->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="p-5 text-center">
                        <i data-lucide="alert-triangle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                        <div class="text-3xl mt-5">Yakin ingin membatalkan?</div>
                        <div class="text-slate-500 mt-2">
                            Data akan dihapus permanen dan stok produk bertipe <b>'Jadi'</b> akan dikurangi otomatis sesuai QTY produksi ini.
                        </div>
                    </div>
                    <div class="px-5 pb-8 text-center">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Tutup</button>
                        <button type="submit" class="btn btn-danger w-24">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection