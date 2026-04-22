@extends('layouts.app')

@section('content')
<h2 class="intro-y text-lg font-medium mt-10">
    Riwayat Penjualan Produk
</h2>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap xl:flex-nowrap items-center mt-2">
        <div class="flex w-full sm:w-auto">
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
        </div>
        
        <div class="w-full xl:w-auto flex items-center mt-3 xl:mt-0">
            <a href="{{ route('penjualan.create') }}" class="btn btn-primary shadow-md mr-2"> 
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Input Penjualan Baru 
            </a>
        </div>
    </div>

    <div class="intro-y col-span-12 overflow-auto 2xl:overflow-visible">
        <table class="table table-report -mt-2">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">INVOICE</th>
                    <th class="whitespace-nowrap">CLIENT / PEMBELI</th>
                    <th class="text-center whitespace-nowrap">STATUS</th>
                    <th class="text-center whitespace-nowrap">TANGGAL</th>
                    <th class="text-right whitespace-nowrap">TOTAL TRANSAKSI</th>
                    <th class="text-center whitespace-nowrap">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penjualan as $p)
                <tr class="intro-x">
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
                            <div class="flex items-center justify-center whitespace-nowrap text-success"> 
                                <i data-lucide="check-square" class="w-4 h-4 mr-2"></i> Berhasil 
                            </div>
                        @elseif($p->status == 'cancel')
                            <div class="flex items-center justify-center whitespace-nowrap text-danger"> 
                                <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Dibatalkan 
                            </div>
                        @else
                            <div class="flex items-center justify-center whitespace-nowrap text-pending"> 
                                <i data-lucide="refresh-ccw" class="w-4 h-4 mr-2"></i> Return 
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="whitespace-nowrap">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</div>
                    </td>
                    <td class="w-40 text-right">
                        <div class="font-bold text-primary">Rp {{ number_format($p->total, 0, ',', '.') }}</div>
                    </td>
                    <td class="table-report__action">
                        <div class="flex justify-center items-center">
                            <a class="flex items-center text-primary whitespace-nowrap mr-5" href="{{ route('penjualan.show', $p->id) }}"> 
                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail 
                            </a>
                            <a class="flex items-center text-danger whitespace-nowrap" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-modal-{{ $p->id }}"> 
                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Hapus 
                            </a>
                        </div>
                    </td>
                </tr>

                <div id="delete-modal-{{ $p->id }}" class="modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body p-0">
                                <form action="{{ route('penjualan.destroy', $p->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="p-5 text-center">
                                        <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i> 
                                        <div class="text-3xl mt-5">Hapus Transaksi?</div>
                                        <div class="text-slate-500 mt-2">
                                            Data penjualan <b class="text-slate-700">#PJ-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</b> akan dihapus secara permanen.
                                            <br>
                                            <strong class="text-danger">Catatan:</strong> Penghapusan data riwayat tidak mengembalikan stok produk yang sudah terjual.
                                        </div>
                                    </div>
                                    <div class="px-5 pb-8 text-center">
                                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Batal</button>
                                        <button type="submit" class="btn btn-danger w-24">Hapus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <tr class="intro-x">
                    <td colspan="6" class="text-center py-10 text-slate-500 italic">
                        Tidak ada data penjualan ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center">
        <nav class="w-full sm:w-auto sm:mr-auto">
            {{ $penjualan->links() }}
        </nav>
    </div>
</div>
@endsection