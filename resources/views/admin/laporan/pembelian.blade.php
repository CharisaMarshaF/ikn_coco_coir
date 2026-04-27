@extends('layouts.app')

@section('content')
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Laporan Pembelian (Restock Bahan)</h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        {{-- Filter Section --}}
        <div class="intro-y col-span-12">
            <div class="box p-5">
                <form action="{{ route('laporan.pembelian') }}" method="GET"
                    class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="form-group">
                        <label class="form-label font-bold">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label font-bold">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary shadow-md">Filter</button>
                        <a href="{{ route('laporan.pembelian') }}" class="btn btn-secondary">Reset</a>

                        <a href="{{ route('laporan.pembelian.cetak', request()->all()) }}" target="_blank"
                            class="btn btn-outline-secondary">
                            <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow mt-5">
            <div class="preview">
                <div class="overflow-x-auto">
                    <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap text-center">NO</th>
                                <th class="whitespace-nowrap">TANGGAL</th>
                                <th class="whitespace-nowrap">NO. NOTA</th>
                                <th class="whitespace-nowrap">SUPPLIER</th>
                                <th class="text-center whitespace-nowrap">STATUS BAYAR</th>
                                <th class="text-right whitespace-nowrap">TOTAL PEMBELIAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $key => $row)
                                <tr class="intro-x">
                                    <td class="text-center font-medium">{{ $key + 1 }}</td>
                                    <td class="font-medium">{{ date('d M Y', strtotime($row->tanggal)) }}</td>
                                    <td>
                                        <span class="text-slate-500 font-bold italic">#PB-{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td class="uppercase">{{ $row->supplier->nama ?? 'Tanpa Supplier' }}</td>
                                    <td class="text-">
                                        @if ($row->status_pembayaran == 'lunas')
                                            <span class="px-2 py-1 bg-success/20 text-success rounded text-xs font-bold">LUNAS</span>
                                        @else
                                            <span class="px-2 py-1 bg-warning/20 text-warning rounded text-xs font-bold">CANCEL</span>
                                        @endif
                                    </td>
                                    <td class="text- font-bold text-danger">
                                        Rp {{ number_format($row->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                {{-- DataTables dengan ID example1 akan menangani tampilan jika data kosong secara otomatis --}}
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection