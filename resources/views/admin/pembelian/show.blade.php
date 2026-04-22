@extends('layouts.app')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Rincian Pembelian</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button onclick="window.print()" class="btn btn-primary shadow-md mr-2">
            <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Print
        </button>
        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary shadow-md">Kembali</a>
    </div>
</div>

<div class="intro-y grid grid-cols-11 gap-5 mt-5">
    <div class="col-span-12 lg:col-span-4 2xl:col-span-3">
        <div class="box p-5 rounded-md">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate">Detail Transaksi</div>
            </div>
            <div class="flex items-center"> 
                <i data-lucide="clipboard" class="w-4 h-4 text-slate-500 mr-2"></i> Invoice: 
                <span class="font-medium ml-1">#PB-{{ str_pad($pembelian->id, 5, '0', STR_PAD_LEFT) }}</span> 
            </div>
            <div class="flex items-center mt-3"> 
                <i data-lucide="calendar" class="w-4 h-4 text-slate-500 mr-2"></i> Tanggal Beli: 
                <span class="ml-1">{{ \Carbon\Carbon::parse($pembelian->tanggal)->format('d F Y') }}</span> 
            </div>
            <div class="flex items-center mt-3"> 
                <i data-lucide="clock" class="w-4 h-4 text-slate-500 mr-2"></i> Status: 
                @if($pembelian->status_pembayaran == 'lunas')
                    <span class="bg-success/20 text-success rounded px-2 ml-1 text-xs font-medium">Lunas</span>
                @else
                    <span class="bg-warning/20 text-warning rounded px-2 ml-1 text-xs font-medium">Belum Lunas</span>
                @endif
            </div>
            
        </div>

        <div class="box p-5 rounded-md mt-5">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate">Informasi Supplier</div>
            </div>
            <div class="flex items-center"> 
                <i data-lucide="user" class="w-4 h-4 text-slate-500 mr-2"></i> Nama: 
                <span class="ml-1 font-medium">{{ $pembelian->supplier->nama }}</span> 
            </div>
            <div class="flex items-center mt-3"> 
                <i data-lucide="phone" class="w-4 h-4 text-slate-500 mr-2"></i> Telp: 
                <span class="ml-1">{{ $pembelian->supplier->telp ?? '-' }}</span> 
            </div>
            <div class="flex items-center mt-3"> 
                <i data-lucide="map-pin" class="w-4 h-4 text-slate-500 mr-2"></i> Alamat: 
                <div class="ml-1 text-slate-500 text-xs">{{ $pembelian->supplier->alamat ?? '-' }}</div>
            </div>
        </div>

        <div class="box p-5 rounded-md mt-5">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate">Rincian Pembayaran</div>
            </div>
            <div class="flex items-center">
                <i data-lucide="wallet" class="w-4 h-4 text-slate-500 mr-2"></i> Metode:
                <div class="ml-auto font-medium">
@if($pembelian->data_pembayaran && $pembelian->data_pembayaran->rekening)
            {{ $pembelian->data_pembayaran->rekening->nama }}
        @else
            Hutang / Belum Ada
        @endif                </div>
            </div>
            <div class="flex items-center border-t border-slate-200/60 pt-5 mt-5 font-medium text-lg text-primary">
                <i data-lucide="credit-card" class="w-4 h-4 text-slate-500 mr-2"></i> Total Bayar:
                <div class="ml-auto">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="box p-5 rounded-md mt-5">
    <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
        <div class="font-medium text-base truncate">Status & Pembayaran</div>
    </div>
    
    @if($pembelian->status_pembayaran == 'lunas')
        <div class="flex items-center text-success">
            <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> 
            Terbayar menggunakan: {{ $pembelian->data_pembayaran->rekening->nama ?? '-' }}
        </div>
    @else
        <form action="{{ route('pembelian.bayar', $pembelian->id) }}" method="POST">
            @csrf
            <div class="mt-3">
                <label class="form-label">Pilih Rekening Pembayaran</label>
                <select name="rekening_id" class="form-select" required>
                    <option value="">-- Pilih Rekening --</option>
                    @foreach(\App\Models\Rekening::all() as $rk)
                        <option value="{{ $rk->id }}">{{ $rk->nama }} (Saldo: Rp {{ number_format($rk->saldo_saat_ini) }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-full mt-4">
                <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i> Tandai Sebagai Lunas
            </button>
        </form>
    @endif
</div>
    </div>

    <div class="col-span-12 lg:col-span-7 2xl:col-span-8">
        <div class="box p-5 rounded-md">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate">Daftar Bahan yang Dibeli</div>
            </div>
            <div class="overflow-auto lg:overflow-visible -mt-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap !py-5">Bahan Baku</th>
                            <th class="whitespace-nowrap text-right">Harga Satuan</th>
                            <th class="whitespace-nowrap text-right">Qty</th>
                            <th class="whitespace-nowrap text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembelian->detail as $detail)
                        <tr>
                            <td class="!py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 image-fit">
                                        <div class="rounded-lg border border-slate-200 flex items-center justify-center bg-slate-100">
                                            <i data-lucide="box" class="w-5 h-5 text-slate-400"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium whitespace-nowrap">{{ $detail->bahan->nama }}</div>
                                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">Satuan: {{ $detail->bahan->satuan }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td class="text-right">{{ $detail->qty }}</td>
                            <td class="text-right font-medium">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-bold !py-5">GRAND TOTAL</td>
                            <td class="text-right font-bold text-lg text-primary">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection