@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-12 gap-6 mt-5">
        {{-- ALERT SECTION --}}
        @if (session('success'))
            <div class="intro-y col-span-12">
                <div class="alert alert-success show flex items-center mb-2" role="alert">
                    <i data-lucide="check-circle" class="w-6 h-6 mr-2"></i> {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a href="{{ route('pengambilan.create') }}" class="btn btn-primary shadow-md mr-2">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Pengambilan
            </a>

            <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-laporan-pengambilan"
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
                                <th class="whitespace-nowrap">TANGGAL</th>
                                <th class="whitespace-nowrap">ITEM BAHAN</th>
                                <th class="whitespace-nowrap">KETERANGAN</th>
                                <th class="text-center whitespace-nowrap">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengambilan as $key => $p)
                                <tr class="intro-x">
                                    {{-- Kolom 1 --}}
                                    <td class="text-center font-medium w-10">
                                        {{ $key + $pengambilan->firstItem() }}
                                    </td>
                                    {{-- Kolom 2 --}}
                                    <td class="whitespace-nowrap">
                                        <div class="font-medium text-primary">
                                            {{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}
                                        </div>
                                    </td>
                                    {{-- Kolom 3 --}}
                                    <td>
                                        <div class="text-xs">
                                            @foreach ($p->details as $detail)
                                                <div class="whitespace-nowrap text-slate-600 font-medium italic mb-1">
                                                    • {{ $detail->bahan->nama ?? 'Bahan Dihapus' }}
                                                    <span class="text-primary">({{ (float) $detail->qty }}
                                                        {{ $detail->bahan->satuan ?? '' }})</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    {{-- Kolom 4 --}}
                                    <td class="text-slate-500">
                                        <div class="text-xs italic">{{ $p->keterangan ?? '-' }}</div>
                                    </td>
                                    {{-- Kolom 5 --}}
                                    <td class="table-report__action w-56">
                                        <div class="flex justify- items-center">
                                            <a class="flex items-center text-danger" href="javascript:;"
                                                data-tw-toggle="modal" data-tw-target="#delete-modal-{{ $p->id }}">
                                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Batalkan
                                            </a>
                                        </div>

                                        {{-- Modal diletakkan tetap di dalam TD terakhir --}}
                                        <div id="delete-modal-{{ $p->id }}" class="modal" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-body p-0">
                                                        <div class="p-5 text-center">
                                                            <i data-lucide="x-circle"
                                                                class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                                            <div class="text-3xl mt-5">Yakin batalkan?</div>
                                                            <div class="text-slate-500 mt-2">Data akan dihapus & stok bahan
                                                                baku akan dikembalikan.</div>
                                                        </div>
                                                        <div class="px-5 pb-8 text-center">
                                                            <button type="button" data-tw-dismiss="modal"
                                                                class="btn btn-outline-secondary w-24 mr-1">Tutup</button>
                                                            <form action="{{ route('pengambilan.destroy', $p->id) }}"
                                                                method="POST" class="inline">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-danger w-24">Ya,
                                                                    Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-5">
            <nav class="w-full sm:w-auto sm:mr-auto">
                {{ $pengambilan->appends(request()->query())->links() }}
            </nav>
        </div>
        {{-- Modal Laporan PDF --}}
        <div id="modal-laporan-pengambilan" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Form diarahkan ke method cetakPengambilan di Controller --}}
                    {{-- Ganti baris ini --}}
                    <form action="{{ route('pengambilan.cetak') }}" method="GET" target="_blank">
                        <div class="modal-header">
                            <h2 class="font-medium text-base mr-auto">Filter Laporan Pengambilan</h2>
                        </div>
                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                            <div class="col-span-12">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" {{-- Mengatur ke tanggal 1 bulan berjalan --}}
                                    value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-span-12">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" {{-- Mengatur ke tanggal terakhir bulan berjalan --}}
                                    value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer text-right">
                            <button type="button" data-tw-dismiss="modal"
                                class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                            <button type="submit" class="btn btn-primary w-32">
                                <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
