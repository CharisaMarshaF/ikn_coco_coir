@extends('layouts.app')

@section('content')


<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        {{-- <div class="flex w-full sm:w-auto">
            <form action="{{ route('penjualan.index') }}" method="GET" class="flex w-full sm:w-auto">
                <div class="w-48 relative text-slate-500">
                    <input type="text" name="search" class="form-control w-48 box pr-10" placeholder="Cari invoice/client..." value="{{ request('search') }}">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i> 
                </div>
                <select name="status" class="form-select box ml-2" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="berhasil" {{ request('status') == 'berhasil' ? 'selected' : '' }}>Berhasil</option>
                    <option value="cancel" {{ request('status') == 'cancel' ? 'selected' : '' }}>Cancel</option>
                    <option value="return" {{ request('status') == 'return' ? 'selected' : '' }}>Return</option>
                </select>
            </form>
        </div>
        
        <div class="hidden xl:block mx-auto text-slate-500">
            Showing {{ $penjualan->firstItem() }} to {{ $penjualan->lastItem() }} of {{ $penjualan->total() }} entries
        </div> --}}
        
        <div class="w-full xl:w-auto flex items-center mt-3 xl:mt-0">
            <a href="{{ route('penjualan.create') }}" class="btn btn-primary shadow-md mr-2"> 
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Input Penjualan Baru 
            </a>
        </div>
    </div>

    {{-- Table Section dengan Box Putih --}}
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
                            <th class="text-right whitespace-nowrap">TOTAL TRANSAKSI</th>
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penjualan as $key => $p)
                        <tr class="intro-x">
                            <td class="text-center font-medium">{{ $key + $penjualan->firstItem() }}</td>
                            <td class="w-40 !py-4"> 
                                <a href="{{ route('penjualan.show', $p->id) }}" class="underline decoration-dotted whitespace-nowrap font-medium text-primary">
                                    #PJ-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                                </a> 
                            </td>
                            <td class="w-60">
                                <div class="font-medium whitespace-nowrap">{{ $p->client->nama ?? 'Pembeli Umum (Anonim)' }}</div>
                                <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">
                                    {{ $p->client->perusahaan ?? 'Personal' }}
                                </div>
                            </td>
                            <td class="text-center">
                                @if($p->status == 'berhasil')
                                    <div class="flex items-center justify-center whitespace-nowrap text-success font-medium"> 
                                        <i data-lucide="check-square" class="w-4 h-4 mr-2"></i> Berhasil 
                                    </div>
                                @elseif($p->status == 'cancel')
                                    <div class="flex items-center justify-center whitespace-nowrap text-danger font-medium"> 
                                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Dibatalkan 
                                    </div>
                                @else
                                    <div class="flex items-center justify-center whitespace-nowrap text-pending font-medium"> 
                                        <i data-lucide="refresh-ccw" class="w-4 h-4 mr-2"></i> Return 
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="whitespace-nowrap">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</div>
                            </td>
                            <td class="w-40 text-center">
                                <div class="font-bold text-primary">Rp {{ number_format($p->total, 0, ',', '.') }}</div>
                            </td>
                            <td class="table-report__action">
                                <div class="flex justify-center items-center">
                                    <a class="flex items-center text-primary whitespace-nowrap mr-5" href="{{ route('penjualan.show', $p->id) }}"> 
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail 
                                    </a>
                                    @if($p->status != 'cancel')
                                    <a class="flex items-center text-danger whitespace-nowrap" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-modal-{{ $p->id }}"> 
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Batalkan 
                                    </a>
                                    @else
                                    <span class="text-slate-400 italic text-xs">Sudah Batal</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Cancel --}}
                        <div id="delete-modal-{{ $p->id }}" class="modal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body p-0">
                                        <form action="{{ route('penjualan.cancel', $p->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="p-5 text-center">
                                                <i data-lucide="alert-triangle" class="w-16 h-16 text-danger mx-auto mt-3"></i> 
                                                <div class="text-3xl mt-5">Batalkan Transaksi?</div>
                                                <div class="text-slate-500 mt-2">
                                                    Transaksi <b class="text-slate-700">#PJ-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</b> akan dibatalkan.
                                                    <br>
                                                    <strong class="text-primary">Info:</strong> Stok produk akan otomatis dikembalikan ke gudang.
                                                </div>
                                            </div>
                                            <div class="px-5 pb-8 text-center">
                                                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Tutup</button>
                                                <button type="submit" class="btn btn-danger w-32 text-white">Ya, Batalkan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        {{-- DataTables example1 handles empty state --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination Section --}}
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-5">
        <nav class="w-full sm:w-auto sm:mr-auto">
            {{ $penjualan->appends(request()->query())->links() }}
        </nav>
    </div>
</div>
@endsection