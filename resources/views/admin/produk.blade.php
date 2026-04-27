@extends('layouts.app')

@section('content')
<h2 class="intro-y text-lg font-medium mt-10">Daftar Produk</h2>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <button data-tw-toggle="modal" data-tw-target="#modal-tambah-produk" class="btn btn-primary shadow-md mr-2">Tambah Produk</button>
    </div>

    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow">
        <div class="preview">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">NO</th>
                            <th class="whitespace-nowrap">NAMA PRODUK</th>
                            <th class="text-center whitespace-nowrap">STOK</th>
                            <th class="whitespace-nowrap">SATUAN</th>
                            <th class="whitespace-nowrap">HARGA DEFAULT</th>
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produk as $key => $p)
                        <tr class="intro-x">
                            <td class="text-center w-10">{{ $key + 1 }}</td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $p->nama }}</div>
                            </td>
                            <td class="text-center font-bold">
                                <span class="{{ ($p->stok->jumlah ?? 0) <= 0 ? 'text-danger' : 'text-success' }}">
                                    {{ (float)($p->stok->jumlah ?? 0) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-slate-500">{{ $p->satuan }}</div>
                            </td>
                            <td>
                                <div class="text-primary font-bold">Rp {{ number_format($p->harga_default, 0, ',', '.') }}</div>
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify- items-center">
                                    <a class="flex items-center mr-3 text-primary" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-{{ $p->id }}"> 
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit 
                                    </a>
                                    <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $p->id }}"> 
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete 
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Section --}}
@foreach ($produk as $p)
    {{-- Modal Edit --}}
    <div id="modal-edit-{{ $p->id }}" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('produk.update', $p->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Edit Produk & Stok</h2>
                    </div>
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="nama" class="form-control" value="{{ $p->nama }}" required>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="{{ $p->satuan }}" required>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Stok Saat Ini</label>
                            <input type="number" step="any" name="stok" class="form-control" value="{{ (float)($p->stok->jumlah ?? 0) }}" readonly>
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Harga Default (Rp)</label>
                            <input type="number" name="harga_default" class="form-control" value="{{ $p->harga_default }}">
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                        <button type="submit" class="btn btn-primary w-20">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div id="modal-delete-{{ $p->id }}" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <form action="{{ route('produk.destroy', $p->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="p-5 text-center">
                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i> 
                            <div class="text-3xl mt-5">Apakah Anda yakin?</div>
                            <div class="text-slate-500 mt-2">Data produk <b>{{ $p->nama }}</b> akan dihapus secara permanen.</div>
                        </div>
                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                            <button type="submit" class="btn btn-danger w-24">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Modal Tambah --}}
<div id="modal-tambah-produk" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('produk.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Tambah Produk Baru</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Barang Jadi" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" class="form-control" placeholder="Pcs / Pack" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Harga Default (Rp)</label>
                        <input type="number" name="harga_default" class="form-control" placeholder="0">
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                    <button type="submit" class="btn btn-primary w-20">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection