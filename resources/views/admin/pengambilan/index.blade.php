@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6 mt-5">
    {{-- ALERT SECTION --}}
    @if(session('success'))
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

        {{-- TOMBOL PDF (Selalu Ada) --}}
        <a href="{{ route('pengambilan.pdf', ['dari' => $dari, 'sampai' => $sampai]) }}" target="_blank" class="btn btn-danger shadow-md mr-2">
            <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export PDF
        </a>

        {{-- FILTER FORM --}}
        <div class="sm:ml-auto mt-3 sm:mt-0 flex items-center gap-2">
            <form action="{{ route('pengambilan.index') }}" method="GET" class="flex items-center gap-2">
                <input type="date" name="dari" class="form-control" value="{{ $dari }}">
                <span class="text-slate-500">s/d</span>
                <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
                <button type="submit" class="btn btn-secondary shadow-md">Filter</button>
            </form>
            @if($dari || $sampai)
                <a href="{{ route('pengambilan.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </div>
    </div>

    {{-- Table Section --}}
    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow mt-5">
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
                    @forelse ($pengambilan as $p)
                    <tr class="intro-x">
                        <td class="text-center font-medium w-10">
                            {{ ($pengambilan->currentPage()-1) * $pengambilan->perPage() + $loop->iteration }}
                        </td>
                        <td class="whitespace-nowrap">
                            <div class="font-medium text-primary">
                                {{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}
                            </div>
                        </td>
                        <td>
                            <div class="text-xs space-y-1">
                                @foreach($p->details as $detail)
                                    <div class="whitespace-nowrap text-slate-600 font-medium italic">
                                        • {{ $detail->bahan->nama ?? 'Bahan Dihapus' }} 
                                        <span class="text-primary">({{ (float)$detail->qty }} {{ $detail->bahan->satuan ?? '' }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-slate-500">
                            <div class="text-xs italic">{{ $p->keterangan ?? '-' }}</div>
                        </td>
                        <td class="table-report__action w-56">
                            <div class="flex justify- items-center">
                                <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-modal-{{ $p->id }}"> 
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Batalkan 
                                </a>

                                {{-- Modal Delete --}}
                                <div id="delete-modal-{{ $p->id }}" class="modal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-body p-0">
                                                <div class="p-5 text-center">
                                                    <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                                    <div class="text-3xl mt-5">Yakin batalkan?</div>
                                                    <div class="text-slate-500 mt-2">Data akan dibatalkan & stok dikembalikan.</div>
                                                </div>
                                                <div class="px-5 pb-8 text-center">
                                                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Batal</button>
                                                    <form action="{{ route('pengambilan.destroy', $p->id) }}" method="POST" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger w-24">Batalkan</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 italic">Data tidak ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">
            {{ $pengambilan->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection