@extends('layouts.app')

@section('content')


    <div class="grid grid-cols-12 gap-6 mt-5">
        {{-- Tombol Navigasi / Action --}}
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary shadow-md mr-2">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Penjualan
            </a>

            <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-laporan-return"
                class="btn btn-danger shadow-md mr-2">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Laporan PDF
            </a>
        </div>

        {{-- Table Section --}}
        <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow mt-5">
            <div class="preview">
                <div class="overflow-x-auto">
                    <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap w-10 text-center">NO</th>
                                <th class="whitespace-nowrap">INVOICE</th>
                                <th class="whitespace-nowrap">CLIENT / PEMBELI</th>
                                <th class="text-center whitespace-nowrap">STATUS</th>
                                <th class="text-center whitespace-nowrap">TANGGAL</th>
                                <th class="text-right whitespace-nowrap">TOTAL AKHIR</th>
                                <th class="text-center whitespace-nowrap">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($returns as $key => $r)
                                <tr class="intro-x">
                                    <td class="text-center font-medium">
                                        {{ $key + $returns->firstItem() }}
                                    </td>
                                    <td class="w-40 !py-4">
                                        <span class="font-medium text-primary">{{ $r->nomor_return }}</span>
                                        <div class="text-slate-500 text-xs">Ref: #PJ-{{ str_pad($r->penjualan_id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="w-60">
                                        <div class="font-medium whitespace-nowrap">
                                            {{ $r->penjualan->client->nama ?? 'Pembeli Umum' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex items-center justify-center text-pending font-medium">
                                            <i data-lucide="refresh-ccw" class="w-4 h-4 mr-2"></i> Return
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}
                                    </td>
                                    <td class="w-40 text-right">
                                        <div class="font-bold text-danger">
                                            Rp {{ number_format($r->total_refund, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="table-report__action">
                                        <div class="flex justify-center items-center">
                                            <a class="flex items-center text-primary mr-3" href="{{ route('penjualan.show_return', $r->id) }}">
                                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                            </a>

                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-slate-500 italic">Data tidak ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination Section (Di luar div table) --}}
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-5">
            <nav class="w-full sm:w-auto sm:mr-auto">
                {{ $returns->appends(request()->query())->links() }}
            </nav>
        </div>
    </div>

    {{-- Modal Laporan --}}
    <div id="modal-laporan-return" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('penjualan.cetak_return') }}" method="GET" target="_blank">
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Filter Laporan Return</h2>
                    </div>
                    <div class="modal-body grid grid-cols-12 gap-4">
                        <div class="col-span-12">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                        <button type="submit" class="btn btn-primary w-32">
                            <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection