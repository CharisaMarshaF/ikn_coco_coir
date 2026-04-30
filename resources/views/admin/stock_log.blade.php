@extends('layouts.app')

@section('content')
<div class="intro-y box p-5 mt-5">
    <div class="overflow-x-auto">
        <table id="example1" class="table table-report table-report--bordered display datatable w-full">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">WAKTU</th>
                    <th width="100" class="whitespace-nowrap">Bahan / Produk </th>
                    <th class="whitespace-nowrap">ITEM</th>
                    <th class="text-center whitespace-nowrap">JENIS</th>
                    <th class="text-center whitespace-nowrap">JUMLAH</th>
                    <th class="text-center whitespace-nowrap">STOK (AWAL -> AKHIR)</th>
                    <th class="whitespace-nowrap">SUMBER</th>
                    <th class="whitespace-nowrap">USER </th>
                    <th class="whitespace-nowrap">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                <tr class="intro-x">
                    <td class="whitespace-nowrap text-slate-500">
                        {{ $log->created_at->format('d M Y, H:i') }}
                    </td>
                    <td>
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $log->item_type == 'produk' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                            {{ strtoupper(str_replace('_', ' ', $log->item_type)) }}
                        </span>
                    </td>
                    <td class="font-medium">
                        {{-- Logika untuk mengambil nama item berdasarkan ID dan Tipe --}}
                        @if($log->item_type == 'bahan_baku')
                            {{ \App\Models\BahanBaku::find($log->item_id)->nama ?? 'Item Dihapus' }}
                        @else
                            {{ \App\Models\Produk::find($log->item_id)->nama ?? 'Item Dihapus' }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if($log->jenis == 'masuk')
                            <div class="flex items-center justify-center text-success"> <i data-lucide="arrow-up-right" class="w-4 h-4 mr-1"></i> MASUK </div>
                        @elseif($log->jenis == 'keluar')
                            <div class="flex items-center justify-center text-danger"> <i data-lucide="arrow-down-left" class="w-4 h-4 mr-1"></i> KELUAR </div>
                        @else
                            <div class="flex items-center justify-center text-warning"> <i data-lucide="refresh-ccw" class="w-4 h-4 mr-1"></i> MANUAL </div>
                        @endif
                    </td>
                    <td class="text-center font-bold">
                        {{ (float)$log->jumlah }}
                    </td>
                    <td class="text-center text-slate-500 text-xs">
                        {{ (float)$log->stok_sebelum }} <i data-lucide="chevrons-right" class="inline w-3 h-3"></i> {{ (float)$log->stok_sesudah }}
                    </td>
                    <td>
                        <span class="text-xs uppercase px-2 py-1 bg-slate-100 rounded border">
                            {{ $log->sumber }}
                        </span>
                    </td>
                    <td>
                        <div class="text-xs font-medium">{{ $log->user->name ?? 'System' }}</div>
                    </td>
                    <td class="text-slate-500 italic text-xs">
                        {{ $log->keterangan ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection