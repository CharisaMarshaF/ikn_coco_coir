@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <div class="grid grid-cols-12 gap-6">
            {{-- BAGIAN 1: RINGKASAN LAPORAN --}}
            <div class="col-span-12 mt-8">
                <div class="intro-y flex items-center h-10">
                    <h2 class="text-lg font-medium truncate mr-5">Ringkasan Laporan</h2>
                </div>
                <div class="grid grid-cols-12 gap-6 mt-5">
                    <!-- Omset Hari Ini -->
                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                        <div class="report-box zoom-in">
                            <div class="box p-5">
                                <div class="flex">
                                    <i data-lucide="coins" class="report-box__icon text-primary"></i>
                                    <div class="ml-auto">
                                        <div class="report-box__indicator bg-success cursor-pointer" title="Hari Ini">Hari Ini</div>
                                    </div>
                                </div>
                                <div class="text-2xl font-medium leading-8 mt-6">
                                    <span class="text-sm align-middle">Rp</span> {{ number_format($penjualanHariIni, 0, ',', '.') }}
                                </div>
                                <div class="text-base text-slate-500 mt-1">Omset Hari Ini</div>
                            </div>
                        </div>
                    </div>

                    <!-- Omset Bulan Ini -->
                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                        <div class="report-box zoom-in">
                            <div class="box p-5">
                                <div class="flex">
                                    <i data-lucide="shopping-cart" class="report-box__icon text-pending"></i>
                                    <div class="ml-auto">
                                        <div class="report-box__indicator bg-pending cursor-pointer" title="Bulan Ini">Bulan Ini</div>
                                    </div>
                                </div>
                                <div class="text-2xl font-medium leading-8 mt-6 text-pending">
                                    <span class="text-sm align-middle">Rp</span> {{ number_format($penjualanBulanIni, 0, ',', '.') }}
                                </div>
                                <div class="text-base text-slate-500 mt-1">Omset Bulan Ini</div>
                            </div>
                        </div>
                    </div>

                    <!-- Pemasukan Kas -->
                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                        <div class="report-box zoom-in">
                            <div class="box p-5">
                                <div class="flex">
                                    <i data-lucide="credit-card" class="report-box__icon text-success"></i>
                                </div>
                                <div class="text-2xl font-medium leading-8 mt-6 text-success">
                                    <span class="text-sm align-middle">Rp</span> {{ number_format($totalPemasukan, 0, ',', '.') }}
                                </div>
                                <div class="text-base text-slate-500 mt-1">Kas Masuk (Bulan Ini)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Produksi -->
                    <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                        <div class="report-box zoom-in">
                            <div class="box p-5">
                                <div class="flex">
                                    <i data-lucide="clipboard" class="report-box__icon text-warning"></i>
                                </div>
                                <div class="text-2xl font-medium leading-8 mt-6 text-warning">
                                    {{ number_format($jumlahProduksiBulanIni, 0, ',', '.') }}
                                </div>
                                <div class="text-base text-slate-500 mt-1">Total Produksi (Bulan Ini)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 2: GRAFIK PENJUALAN --}}
            <div class="col-span-12 lg:col-span-8 mt-8">
                <div class="intro-y flex items-center h-10">
                    <h2 class="text-lg font-medium truncate mr-5">Grafik Penjualan 12 Bulan Terakhir</h2>
                </div>
                <div class="intro-y box p-5 mt-5">
                    <div style="height: 350px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 3: PENJUALAN TERBARU --}}
            <div class="col-span-12 lg:col-span-4 mt-8">
                <div class="intro-y flex items-center h-10">
                    <h2 class="text-lg font-medium truncate mr-5">Penjualan Terbaru</h2>
                </div>
                <div class="mt-5">
                    @forelse ($recentTransactions as $rt)
                        <div class="intro-y">
                            <div class="box px-4 py-4 mb-3 flex items-center zoom-in">
                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($rt->client->nama ?? 'C') }}&background=random" alt="Client">
                                </div>
                                <div class="ml-4 mr-auto">
                                    <div class="font-medium">#PJ-{{ str_pad($rt->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-slate-500 text-xs mt-0.5">{{ $rt->client->nama ?? 'Umum' }}</div>
                                </div>
                                <div class="text-success font-bold whitespace-nowrap">
                                    Rp {{ number_format($rt->total, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-slate-500 py-4 italic">Tidak ada transaksi</div>
                    @endforelse
                    <a href="{{ route('penjualan.index') }}" class="intro-y w-full block text-center rounded-md py-3 border border-dotted border-slate-400 text-slate-500 mt-2 hover:bg-slate-50 transition-colors">
                        Lihat Semua Transaksi
                    </a>
                </div>
            </div>

            {{-- BAGIAN 4: STATUS PRODUKSI TERAKHIR --}}
            <div class="col-span-12 mt-4">
                <div class="intro-y flex items-center h-10">
                    <h2 class="text-lg font-medium truncate mr-5">Status Produksi Terakhir</h2>
                    <a href="{{ route('produksi.index') }}" class="ml-auto text-primary truncate">Lihat Semua</a>
                </div>
                <div class="intro-y box p-5 mt-5">
                    <div class="overflow-x-auto">
                        <table class="table table-report mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">TANGGAL</th>
                                    <th class="whitespace-nowrap">HASIL PRODUKSI</th>
                                    <th class="text-center whitespace-nowrap">STATUS</th>
                                    <th class="text-center whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($produksiTerbaru as $prod)
                                    <tr class="intro-x">
                                        <td class="font-medium whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($prod->tanggal)->format('d M Y') }}
                                        </td>
                                        <td>
                                            @foreach ($prod->detail->where('jenis', 'produk') as $item)
                                                <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">
                                                    <i data-lucide="package" class="w-3 h-3 inline-block mr-1"></i>
                                                    {{ $item->produk->nama ?? 'N/A' }} <strong>({{ $item->qty }} pcs)</strong>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="w-40">
                                            <div class="flex items-center justify-center {{ $prod->status == 'berhasil' ? 'text-success' : ($prod->status == 'proses' ? 'text-warning' : 'text-danger') }}">
                                                <i data-lucide="{{ $prod->status == 'berhasil' ? 'check-square' : ($prod->status == 'proses' ? 'clock' : 'x-circle') }}" class="w-4 h-4 mr-2"></i>
                                                {{ ucfirst($prod->status) }}
                                            </div>
                                        </td>
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center text-primary" href="{{ route('produksi.show', $prod->id) }}">
                                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-slate-500 py-4 italic">Belum ada data produksi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Total Penjualan',
                    data: {!! json_encode($chartValues) !!},
                    backgroundColor: '#1e40af',
                    borderRadius: 5,
                    barPercentage: 0.6
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Total: Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000) + 'rb';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection