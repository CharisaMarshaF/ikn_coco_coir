@extends('layouts.app')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Laporan Detail Barang Return</h2>
</div>

<div class="box p-5 mt-5">
    <form action="{{ route('penjualan.return_report') }}" method="GET" class="flex flex-col md:flex-row gap-4 mb-5">
        <div class="w-full md:w-56">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
        </div>
        <div class="w-full md:w-56">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
        </div>
        <div class="flex items-end">
            <button type="submit" class="btn btn-primary shadow-md">Filter</button>
            <a href="{{ route('penjualan.return_report') }}" class="btn btn-secondary ml-2">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="table table-bordered table-hover">
            <thead class="bg-slate-100">
                <tr>
                    <th class="whitespace-nowrap">TANGGAL</th>
                    <th class="whitespace-nowrap">NAMA BARANG</th>
                    <th class="text-center whitespace-nowrap">QTY RETURN</th>
                    <th class="whitespace-nowrap">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->produk->nama ?? 'Produk Dihapus' }}</td>
                    <td class="text-center font-bold text-danger">{{ $log->jumlah }}</td>
                    <td>{{ $log->keterangan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data return pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection