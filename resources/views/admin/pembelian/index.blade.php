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

    @if(session('error'))
    <div class="intro-y col-span-12">
        <div class="alert alert-danger show flex items-center mb-2" role="alert"> 
            <i data-lucide="alert-octagon" class="w-6 h-6 mr-2"></i> {{ session('error') }} 
        </div>
    </div>
    @endif

    <div class="intro-y col-span-12 flex flex-wrap xl:flex-nowrap items-center mt-2">
        {{-- SEARCH BOX --}}
        {{-- <div class="flex w-full sm:w-auto">
            <form action="{{ route('pembelian.index') }}" method="GET" class="relative text-slate-500">
                <input type="text" name="search" class="form-control w-48 box pr-10" placeholder="Cari supplier..." value="{{ request('search') }}">
                <button type="submit" class="absolute my-auto inset-y-0 mr-3 right-0">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </form>
        </div> --}}
        
        {{-- <div class="hidden xl:block mx-auto text-slate-500">
            Showing {{ $pembelian->firstItem() }} to {{ $pembelian->lastItem() }} of {{ $pembelian->total() }} entries
        </div> --}}
        
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a href="{{ route('pembelian.create') }}" class="btn btn-primary shadow-md mr-2"> 
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Pembelian 
            </a>
            
            @if(request('search'))
                <a href="{{ route('pembelian.index') }}" class="btn btn-secondary shadow-md">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i> Reset
                </a>
            @endif
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
                            <th class="whitespace-nowrap">SUPPLIER</th>
                            <th class="text-center whitespace-nowrap">STATUS</th>
                            <th class="whitespace-nowrap">TANGGAL</th>
                            <th class="text-right whitespace-nowrap">TOTAL TRANSAKSI</th>
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembelian as $key => $p)
                        <tr class="intro-x">
                            <td class="text-center font-medium">{{ $key + $pembelian->firstItem() }}</td>
                            <td class="w-40 !py-4"> 
                                <a href="{{ route('pembelian.show', $p->id) }}" class="underline decoration-dotted whitespace-nowrap font-medium">
                                    #PB-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                                </a> 
                            </td>
                            <td class="w-40">
                                <a href="javascript:;" class="font-medium whitespace-nowrap">{{ $p->supplier->nama }}</a> 
                            </td>
                            <td class="text-left">
                                @if($p->status_pembayaran == 'lunas')
                                    <div class="flex items-center justify-center whitespace-nowrap text-success font-medium"> 
                                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Lunas 
                                    </div>
                                @elseif($p->status_pembayaran == 'cancel')
                                    <div class="flex items-center justify-center whitespace-nowrap text-danger font-medium uppercase italic"> 
                                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Cancelled 
                                    </div>
                                @else
                                    <div class="flex items-center justify-center whitespace-nowrap text-pending font-medium"> 
                                        <i data-lucide="clock" class="w-4 h-4 mr-2"></i> Belum Lunas 
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="whitespace-nowrap">{{ \Carbon\Carbon::parse($p->tanggal)->format('d F Y') }}</div>
                            </td>
                            <td class="w-40 text-center">
                                <div class="font-bold text-primary">Rp {{ number_format($p->total, 0, ',', '.') }}</div>
                            </td>
                            <td class="table-report__action">
                                <div class="flex justify-center items-center">
                                    <a class="flex items-center mr-3 text-primary" href="{{ route('pembelian.show', $p->id) }}"> 
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail 
                                    </a>
                                    
                                    @if($p->status_pembayaran != 'cancel')
                                        <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-cancel-{{ $p->id }}"> 
                                            <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i> Cancel 
                                        </a>

                                        {{-- Modal Cancel --}}
                                        <div id="modal-cancel-{{ $p->id }}" class="modal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-body p-0">
                                                        <div class="p-5 text-center">
                                                            <i data-lucide="alert-triangle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                                            <div class="text-3xl mt-5">Batalkan Transaksi?</div>
                                                            <div class="text-slate-500 mt-2">
                                                                Data tetap tersimpan namun status berubah menjadi <b>Cancelled</b>.<br>
                                                                Stok bahan baku akan otomatis dikurangi kembali.
                                                            </div>
                                                        </div>
                                                        <div class="px-5 pb-8 text-center">
                                                            <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Kembali</button>
                                                            <form action="{{ route('pembelian.cancel', $p->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger w-32">Ya, Batalkan</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center text-slate-400 italic">
                                            <i data-lucide="slash" class="w-4 h-4 mr-1"></i> Cancel
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination Section --}}
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-5">
        <nav class="w-full sm:w-auto sm:mr-auto">
            {{ $pembelian->appends(request()->query())->links() }}
        </nav>
    </div>
</div>
@endsection