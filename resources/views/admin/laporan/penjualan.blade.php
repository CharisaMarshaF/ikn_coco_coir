@extends('layouts.app')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Laporan Penjualan</h2>
</div>

<div class="grid grid-cols-12 gap-6 mt-5">
    {{-- Filter Section --}}
    <div class="intro-y col-span-12">
        <div class="box p-5">
            <form action="{{ route('laporan.penjualan') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="form-group">
                    <label class="form-label font-bold">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                </div>
                <div class="form-group">
                    <label class="form-label font-bold">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                </div>
                <div class="form-group">
                    <label class="form-label font-bold">Status</label>
                    <select name="status" class="form-select w-40">
                        <option value="">Semua Status</option>
                        <option value="berhasil" {{ $status == 'berhasil' ? 'selected' : '' }}>Berhasil</option>
                        <option value="cancel" {{ $status == 'cancel' ? 'selected' : '' }}>Cancel</option>
                        <option value="return" {{ $status == 'return' ? 'selected' : '' }}>Return</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary shadow-md">Filter</button>
                    <a href="{{ route('laporan.penjualan') }}" class="btn btn-secondary">Reset</a>
                    <a href="{{ route('laporan.penjualan.cetak', request()->query()) }}" target="_blank" class="btn btn-outline-secondary">
                        <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="col-span-12 md:col-span-4 intro-y">
        <div class="box p-5 zoom-in bg-primary">
            <div class="text-white font-medium text-base">Total Omzet Bersih</div>
            <div class="text-white text-2xl font-bold mt-1">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-span-12 md:col-span-4 intro-y">
        <div class="box p-5 zoom-in">
            <div class="text-slate-500 font-medium text-base">Transaksi Dibatalkan (Loss)</div>
            <div class="text-danger text-2xl font-bold mt-1">Rp {{ number_format($summary['total_cancel'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-span-12 md:col-span-4 intro-y">
        <div class="box p-5 zoom-in">
            <div class="text-slate-500 font-medium text-base">Total Transaksi</div>
            <div class="text-slate-700 text-2xl font-bold mt-1">{{ $summary['count_transaksi'] }} Transaksi</div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow mt-5">
        <div class="preview">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowraptext-center">NO</th>
                            <th class="whitespace-nowrap">TANGGAL</th>
                            <th class="whitespace-nowrap">NO. NOTA</th>
                            <th class="whitespace-nowrap">CLIENT</th>
                            <th class="text-center whitespace-nowrap">STATUS</th>
                            <th class="text-right whitespace-nowrap">TOTAL NILAI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $key => $row)
                        <tr class="intro-x">
                            <td class="text- font-medium">{{ $key + 1 }}</td>
                            <td class="font-medium">{{ date('d M Y', strtotime($row->tanggal)) }}</td>
                            <td><span class="text-slate-500">#PJ-</span>{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="uppercase">{{ $row->client->nama ?? 'Umum' }}</td>
                            <td class="text-">
                                @if($row->status == 'berhasil')
                                    <span class="px-2 py-1 bg-success/20 text-success rounded text-xs">BERHASIL</span>
                                @elseif($row->status == 'cancel')
                                    <span class="px-2 py-1 bg-danger/20 text-danger rounded text-xs">CANCEL</span>
                                @else
                                    <span class="px-2 py-1 bg-warning/20 text-warning rounded text-xs">RETURN</span>
                                @endif
                            </td>
                            <td class="text- font-bold">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        {{-- DataTables dengan ID example1 akan menangani tampilan jika data kosong --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection