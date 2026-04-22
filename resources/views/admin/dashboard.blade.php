@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 2xl:col-span-9">
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 mt-8">
                <div class="intro-y flex items-center h-10">
                    <h2 class="text-lg font-medium truncate mr-5">Ringkasan Performa Bisnis</h2>
                    <a href="" class="ml-auto text-primary truncate">Reload Data</a>
                </div>
                <div class="grid grid-cols-12 gap-6 mt-5">
                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                        <div class="report-box zoom-in">
                            <div class="box p-5">
                                <div class="flex"><i data-lucide="package" class="report-box__icon text-primary"></i></div>
                                <div class="text-3xl font-medium leading-8 mt-6">{{ $totalProduk }}</div>
                                <div class="text-base text-slate-500 mt-1">Item Produk</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                        <div class="report-box zoom-in">
                            <div class="box p-5">
                                <div class="flex"><i data-lucide="shopping-cart" class="report-box__icon text-pending"></i></div>
                                <div class="text-3xl font-medium leading-8 mt-6">Rp {{ number_format($penjualanBulanIni, 0, ',', '.') }}</div>
                                <div class="text-base text-slate-500 mt-1">Penjualan Bulan Ini</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                        <div class="report-box zoom-in">
                            <div class="box p-5">
                                <div class="flex"><i data-lucide="credit-card" class="report-box__icon text-success"></i></div>
                                <div class="text-3xl font-medium leading-8 mt-6 text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                                <div class="text-base text-slate-500 mt-1">Total Kas & Bank</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                        <div class="report-box zoom-in">
                            <div class="box p-5">
                                <div class="flex"><i data-lucide="clipboard" class="report-box__icon text-warning"></i></div>
                                <div class="text-3xl font-medium leading-8 mt-6">{{ $jumlahProduksiBulanIni }}</div>
                                <div class="text-base text-slate-500 mt-1">Produksi Bulan Ini</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-8 mt-8">
                <div class="intro-y flex items-center h-10">
                    <h2 class="text-lg font-medium truncate mr-5">Status Produksi Terakhir</h2>
                </div>
                <div class="intro-y box p-5 mt-5">
                    <div class="overflow-x-auto">
                        <table class="table table-report mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">TANGGAL</th>
                                    <th class="whitespace-nowrap">KETERANGAN</th>
                                    <th class="text-center whitespace-nowrap">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produksiTerbaru as $prod)
                                <tr class="intro-x">
                                    <td class="font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($prod->tanggal)->format('d M Y') }}</td>
                                    <td>
                                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">{{ $prod->keterangan ?? '-' }}</div>
                                    </td>
                                    <td class="w-40">
                                        <div class="flex items-center justify-center {{ $prod->status == 'berhasil' ? 'text-success' : ($prod->status == 'proses' ? 'text-warning' : 'text-danger') }}">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-2"></i> {{ ucfirst($prod->status) }}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-4 mt-8">
                <div class="intro-y flex items-center h-10">
                    <h2 class="text-lg font-medium truncate mr-5">Penjualan Terbaru</h2>
                </div>
                <div class="mt-5">
                    @foreach($recentTransactions as $rt)
                    <div class="intro-y">
                        <div class="box px-4 py-4 mb-3 flex items-center zoom-in">
                            <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($rt->client->nama ?? 'C') }}&background=random" alt="User">
                            </div>
                            <div class="ml-4 mr-auto">
                                <div class="font-medium">#PJ-{{ str_pad($rt->id, 5, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-slate-500 text-xs mt-0.5">{{ $rt->client->nama ?? 'Umum' }}</div>
                            </div>
                            <div class="text-success font-bold">Rp {{ number_format($rt->total, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @endforeach
                    <a href="{{ route('penjualan.index') }}" class="intro-y w-full block text-center rounded-md py-3 border border-dotted border-slate-400 text-slate-500">Lihat Semua</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection