@extends('layouts.app')
@section('content')
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <div class="w-full xl:w-auto flex items-center mt-3 xl:mt-0">
            <a href="{{ route('produksi.create') }}" class="btn btn-primary shadow-md mr-2"> 
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Input Produksi Baru 
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
                            <th class="whitespace-nowrap">KODE PRODUKSI</th>
                            <th class="whitespace-nowrap">TANGGAL</th>
                            <th class="text-center whitespace-nowrap">STATUS</th>
                            <th class="whitespace-nowrap">HASIL PRODUK</th>
                            <th class="text-center whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produksi as $key => $p)
                        <tr class="intro-x">
                            <td class="text-center font-medium">{{ $key + $produksi->firstItem() }}</td>
                            <td class="w-40 !py-4"> 
                                <a href="{{ route('produksi.show', $p->id) }}" class="underline decoration-dotted whitespace-nowrap font-medium text-primary">
                                    #PRD-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                                </a> 
                            </td>
                            <td>
                                <div class="whitespace-nowrap">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</div>
                            </td>
                            <td class="text-left">
                                @if($p->status == 'berhasil')
                                    <div class="flex items-center justify-center whitespace-nowrap text-success font-medium"> 
                                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Berhasil 
                                    </div>
                                @elseif($p->status == 'proses')
                                    <div class="flex items-center justify-center whitespace-nowrap text-pending font-medium"> 
                                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2 animate-spin"></i> Sedang Proses 
                                    </div>
                                @elseif($p->status == 'cancel')
                                    <div class="flex items-center justify-center whitespace-nowrap text-danger font-medium"> 
                                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Dibatalkan 
                                    </div>
                                @else
                                    <div class="flex items-center justify-center whitespace-nowrap text-danger font-medium"> 
                                        <i data-lucide="slash" class="w-4 h-4 mr-2"></i> Reject / Gagal
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="text-slate-600">
                                    @if($p->detail && $p->detail->where('jenis', 'produk')->count() > 0)
                                        <ul class="text-xs">
                                            @foreach($p->detail->where('jenis', 'produk') as $item)
                                                <li>
                                                    <span class="font-medium">• {{ $item->produk->nama ?? 'Produk' }}</span> 
                                                    ({{ $item->qty }} {{ $item->produk->satuan ?? '' }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-slate-400 italic">Tidak ada data produk</span>
                                    @endif
                                </div>
                            </td>
                            <td class="table-report__action">
                                <div class="flex justify-center items-center">
                                    <a class="flex items-center text-primary whitespace-nowrap mr-5" href="{{ route('produksi.show', $p->id) }}"> 
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail 
                                    </a>
                                    
                                    @if($p->status == 'proses')
                                    <a class="flex items-center text-danger whitespace-nowrap" href="javascript:;" data-tw-toggle="modal" data-tw-target="#cancel-modal-{{ $p->id }}"> 
                                        <i data-lucide="slash" class="w-4 h-4 mr-1"></i> Batalkan 
                                    </a>
                                    @else
                                    <span class="text-slate-400 italic text-xs">Selesai / Batal</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Cancel diletakkan di dalam loop agar ID-nya unik --}}
                        <div id="cancel-modal-{{ $p->id }}" class="modal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body p-0">
                                        <form action="{{ route('produksi.cancel', $p->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="p-5 text-center">
                                                <i data-lucide="alert-triangle" class="w-16 h-16 text-warning mx-auto mt-3"></i> 
                                                <div class="text-3xl mt-5">Batalkan Produksi?</div>
                                                <div class="text-slate-500 mt-2">
                                                    Membatalkan produksi akan <b>mengembalikan stok bahan baku</b>. 
                                                    <br>Data ini akan ditandai sebagai <b class="text-danger">Dibatalkan</b>.
                                                </div>
                                            </div>
                                            <div class="px-5 pb-8 text-center">
                                                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Tutup</button>
                                                <button type="submit" class="btn btn-danger w-32">Ya, Batalkan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-5">
        <nav class="w-full sm:w-auto sm:mr-auto">
            {{ $produksi->links() }}
        </nav>
    </div>
</div>
@endsection