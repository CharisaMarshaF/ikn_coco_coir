@extends('layouts.app')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Rincian Pembelian</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        
        {{-- Hanya tampilkan tombol cetak jika status BUKAN cancel --}}
        @if($pembelian->status_pembayaran !== 'cancel')
            <a href="{{ route('pembelian.pdf', $pembelian->id) }}" target="_blank" class="btn btn-primary shadow-md mr-2">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak PDF
            </a>
        @endif

        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary shadow-md">Kembali</a>
    </div>
</div>

<div class="intro-y grid grid-cols-12 gap-5 mt-5">
    <div class="col-span-12 lg:col-span-4">
        {{-- Detail Transaksi --}}
        <div class="box p-5 rounded-md">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate">Informasi Transaksi</div>
            </div>
            <div class="flex items-center"> 
                <i data-lucide="clipboard" class="w-4 h-4 text-slate-500 mr-2"></i> Invoice: 
                <span class="font-medium ml-1">#PB-{{ str_pad($pembelian->id, 5, '0', STR_PAD_LEFT) }}</span> 
            </div>
            <div class="flex items-center mt-3"> 
                <i data-lucide="calendar" class="w-4 h-4 text-slate-500 mr-2"></i> Tanggal: 
                <span class="ml-1">{{ \Carbon\Carbon::parse($pembelian->tanggal)->format('d F Y') }}</span> 
            </div>
            <div class="flex items-center mt-3"> 
                <i data-lucide="activity" class="w-4 h-4 text-slate-500 mr-2"></i> Status: 
                @if($pembelian->status_pembayaran  == 'cancel')
                    <span class="bg-danger/20 text-danger rounded px-2 ml-1 text-xs font-medium uppercase italic">Cancelled</span>
                @else
                    <span class="bg-success/20 text-success rounded px-2 ml-1 text-xs font-medium uppercase">Lunas / Aktif</span>
                @endif
            </div>
        </div>

        {{-- Detail Supplier --}}
        <div class="box p-5 rounded-md mt-5">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate">Supplier</div>
            </div>
            <div class="flex items-center"> 
                <i data-lucide="user" class="w-4 h-4 text-slate-500 mr-2"></i>
                <span class="font-medium text-primary">{{ $pembelian->supplier->nama }}</span> 
            </div>
            <div class="flex items-center mt-3 text-slate-500"> 
                <i data-lucide="phone" class="w-4 h-4 mr-2"></i> {{ $pembelian->supplier->telp ?? '-' }}
            </div>
            <div class="flex items-center mt-3 text-slate-500"> 
                <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i> 
                <div class="text-xs">{{ $pembelian->supplier->alamat ?? '-' }}</div>
            </div>
        </div>
        
        @if($pembelian->keterangan)
        <div class="box p-5 rounded-md mt-5">
            <div class="font-medium text-base mb-2">Catatan:</div>
            <div class="text-slate-600 italic">"{{ $pembelian->keterangan }}"</div>
        </div>
        @endif
    </div>

    <div class="col-span-12 lg:col-span-8">
        <div class="box p-5 rounded-md">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base">Item Pembelian</div>
            </div>
            <div class="overflow-auto">
                <table class="table table-report">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Bahan Baku</th>
                            <th class="text-right whitespace-nowrap">Harga</th>
                            <th class="text-center whitespace-nowrap">Qty</th>
                            <th class="text-right whitespace-nowrap">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembelian->detail as $item)
                        <tr>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $item->bahan->nama }}</div>
                                <div class="text-slate-500 text-xs mt-0.5">Satuan: {{ $item->bahan->satuan }}</div>
                            </td>
                            <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-bold text-lg !py-5">TOTAL PEMBAYARAN</td>
                            <td class="text-right font-bold text-lg text-primary">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection