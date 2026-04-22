@extends('layouts.app')

@section('content')
<h2 class="intro-y text-lg font-medium mt-10">
    Riwayat Produksi
</h2>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap xl:flex-nowrap items-center mt-2">
        {{-- <div class="flex w-full sm:w-auto">
            <div class="w-48 relative text-slate-500">
                <input type="text" class="form-control w-48 box pr-10" placeholder="Cari produksi...">
                <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i> 
            </div>
            <select class="form-select box ml-2">
                <option value="">Semua Status</option>
                <option value="proses">Proses</option>
                <option value="berhasil">Berhasil</option>
                <option value="reject">Reject</option>
            </select>
        </div> --}}
        
        <div class="hidden xl:block mx-auto text-slate-500">
            Showing {{ $produksi->firstItem() }} to {{ $produksi->lastItem() }} of {{ $produksi->total() }} entries
        </div>
        
        <div class="w-full xl:w-auto flex items-center mt-3 xl:mt-0">
            <a href="{{ route('produksi.create') }}" class="btn btn-primary shadow-md mr-2"> 
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Input Produksi Baru 
            </a>
            {{-- <button class="btn btn-box shadow-md mr-2"> 
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export PDF 
            </button> --}}
        </div>
    </div>

    <div class="intro-y col-span-12 overflow-auto 2xl:overflow-visible">
        <table class="table table-report -mt-2">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">KODE PRODUKSI</th>
                    <th class="whitespace-nowrap">TANGGAL</th>
                    <th class="text-center whitespace-nowrap">STATUS</th>
                    <th class="whitespace-nowrap">KETERANGAN</th>
                    <th class="text-center whitespace-nowrap">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produksi as $p)
                <tr class="intro-x">
                    <td class="w-40 !py-4"> 
                        <a href="{{ route('produksi.show', $p->id) }}" class="underline decoration-dotted whitespace-nowrap font-medium text-primary">
                            #PRD-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                        </a> 
                    </td>
                    <td>
                        <div class="whitespace-nowrap">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</div>
                    </td>
                    <td class="text-center">
                        @if($p->status == 'berhasil')
                            <div class="flex items-center justify-center whitespace-nowrap text-success"> 
                                <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Berhasil 
                            </div>
                        @elseif($p->status == 'proses')
                            <div class="flex items-center justify-center whitespace-nowrap text-pending"> 
                                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2 animate-spin"></i> Sedang Proses 
                            </div>
                        @else
                            <div class="flex items-center justify-center whitespace-nowrap text-danger"> 
                                <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Reject 
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="text-slate-500 italic">{{ $p->keterangan ?? '-' }}</div>
                    </td>
                    <td class="table-report__action">
                        <div class="flex justify-center items-center">
                            <a class="flex items-center text-primary whitespace-nowrap mr-5" href="{{ route('produksi.show', $p->id) }}"> 
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
                                <form action="{{ route('produksi.destroy', $p->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="p-5 text-center">
                                        <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i> 
                                        <div class="text-3xl mt-5">Hapus Data Produksi?</div>
                                        <div class="text-slate-500 mt-2">
                                            Menghapus data ini tidak akan mengembalikan stok bahan yang sudah terpakai.
                                            <br>Anda yakin ingin melanjutkan?
                                        </div>
                                    </div>
                                    <div class="px-5 pb-8 text-center">
                                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Batal</button>
                                        <button type="submit" class="btn btn-danger w-24">Ya, Hapus</button>
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

    <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center">
        <nav class="w-full sm:w-auto sm:mr-auto">
            {{ $produksi->links() }}
        </nav>
        <select class="w-20 form-select box mt-3 sm:mt-0">
            <option>10</option>
            <option>25</option>
            <option>50</option>
        </select>
    </div>
</div>
@endsection