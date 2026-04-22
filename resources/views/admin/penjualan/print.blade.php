@extends('layouts.app')

@section('content')
@php
    $mode = request('mode'); // 'sj' untuk Surat Jalan
    $isSJ = ($mode == 'sj');
    $title = $isSJ ? 'SURAT JALAN' : 'INVOICE';
    $nomor = $isSJ ? $penjualan->suratJalan->nomor : $penjualan->invoice->nomor;
@endphp

{{-- <div class="intro-y flex flex-col sm:flex-row items-center mt-8 no-print">
    <h2 class="text-lg font-medium mr-auto">Preview {{ $title }}</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0 gap-2">
        <a href="{{ route('penjualan.print', [$penjualan->id, 'mode' => 'inv']) }}" class="btn {{ !$isSJ ? 'btn-primary' : 'btn-outline-secondary' }}">Mode Invoice</a>
        <a href="{{ route('penjualan.print', [$penjualan->id, 'mode' => 'sj']) }}" class="btn {{ $isSJ ? 'btn-primary' : 'btn-outline-secondary' }}">Mode Surat Jalan</a>
        <button onclick="window.print()" class="btn btn-success text-white shadow-md">
            <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak A5
        </button>
        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
    </div>  
</div> --}}
<div class="intro-y flex flex-col sm:flex-row items-center mt-8 no-print">
    <h2 class="text-lg font-medium mr-auto">Cetak Dokumen Penjualan</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0 gap-2">
        <a href="{{ route('penjualan.pdf', [$penjualan->id, 'type' => 'invoice']) }}" target="_blank" class="btn btn-primary shadow-md">
            <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Cetak Invoice (PDF)
        </a>
        <a href="{{ route('penjualan.pdf', [$penjualan->id, 'type' => 'sj']) }}" target="_blank" class="btn btn-pending text-white shadow-md">
            <i data-lucide="truck" class="w-4 h-4 mr-2"></i> Cetak Surat Jalan (PDF)
        </a>
        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
<div class="intro-y box overflow-hidden mt-5 print-container">
    <div class="flex flex-col lg:flex-row pt-10 px-5 sm:px-20 sm:pt-20 text-center sm:text-left">
        <div class="font-semibold text-primary text-3xl uppercase">{{ $title }}</div>
        <div class="mt-20 lg:mt-0 lg:ml-auto lg:text-right">
            <div class="text-xl text-primary font-medium">IKN COCO COIR</div>
                {{-- <div class="mt-1">tokoanda@email.com</div> --}}
            <div class="mt-1">Jumantono ,Karanganyar</div>
        </div>
    </div>
    
    <div class="flex flex-col lg:flex-row border-b px-5 sm:px-20 pt-10 pb-10 text-center sm:text-left">
        <div>
            <div class="text-lg font-medium text-primary mt-2">{{ $penjualan->client->nama ?? 'Pembeli Umum' }}</div>
            <div class="mt-1">{{ $penjualan->client->telp ?? '-' }}</div>
            <div class="mt-1">{{ $penjualan->client->alamat ?? 'Alamat tidak dicantumkan' }}</div>
        </div>
        <div class="mt-10 lg:mt-0 lg:ml-auto lg:text-right">
            <div class="text-base text-slate-500">Nomor Dokumen</div>
            <div class="text-lg text-primary font-medium mt-2">{{ $nomor }}</div>
            <div class="mt-1">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d M Y') }}</div>
        </div>
    </div>

    <div class="px-5 sm:px-16 py-10">
        <div class="overflow-x-auto">
            <table class="table table-bordered">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="border-b-2 dark:border-darkmode-400 whitespace-nowrap text-center" style="width: 50px;">NO</th>
                        <th class="border-b-2 dark:border-darkmode-400 whitespace-nowrap">DESKRIPSI PRODUK</th>
                        <th class="border-b-2 dark:border-darkmode-400 text-center whitespace-nowrap">QTY</th>
                        @if(!$isSJ)
                        <th class="border-b-2 dark:border-darkmode-400 text-right whitespace-nowrap">HARGA</th>
                        <th class="border-b-2 dark:border-darkmode-400 text-right whitespace-nowrap">SUBTOTAL</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($penjualan->detail as $index => $item)
                    <tr>
                        <td class="text-center border-b">{{ $index + 1 }}</td>
                        <td class="border-b">
                            <div class="font-medium whitespace-nowrap">{{ $item->produk->nama }}</div>
                        </td>
                        <td class="text-center border-b">{{ $item->qty }}</td>
                        @if(!$isSJ)
                        <td class="text-right border-b">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="text-right border-b font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="px-5 sm:px-20 pb-10 flex flex-col-reverse sm:flex-row">
        {{-- <div class="text-center sm:text-left mt-10 sm:mt-0">
            <div class="text-base text-slate-500">Tanda Terima,</div>
            <div class="mt-12 border-b border-slate-200 w-32 mx-auto sm:mx-0"></div>
            <div class="mt-1 font-medium">( .......................... )</div>
        </div> --}}
        <div class="text-center sm:text-right sm:ml-auto">
            @if(!$isSJ)
                <div class="text-base text-slate-500">Total Pembayaran</div>
                <div class="text-xl text-primary font-medium mt-2">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</div>
                <div class="mt-1 font-bold text-success uppercase">LUNAS</div>
            @else
                {{-- <div class="text-base text-slate-500">Hormat Kami,</div>
                <div class="mt-12 border-b border-slate-200 w-32 ml-auto"></div>
                <div class="mt-1 font-medium">Gudang / Admin</div> --}}
            @endif
        </div>
    </div>
</div>

<style>
    @media print {
        /* Setting ukuran kertas A5 Landscape */
        @page {
            size: 210mm 148mm;
            margin: 5mm;
        }
        
        .no-print { display: none !important; }
        .print-container {
            border: none !important;
            box-shadow: none !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        body {
            background: #fff !important;
            -webkit-print-color-adjust: exact;
        }
        .box { border: none !important; }
        table { font-size: 11px; } /* Dot Matrix biasanya lebih jelas dengan font kecil */
    }
</style>
@endsection