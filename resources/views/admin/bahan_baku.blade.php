@extends('layouts.app')

@section('content')
<h2 class="intro-y text-lg font-medium mt-10">Daftar Bahan Baku</h2>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <button data-tw-toggle="modal" data-tw-target="#modal-tambah-bahan" class="btn btn-primary shadow-md mr-2">Tambah Bahan Baku</button>
        <div class="hidden md:block mx-auto text-slate-500">Showing {{ $bahan->firstItem() }} to {{ $bahan->lastItem() }} of {{ $bahan->total() }} entries</div>
    </div>

    <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
        <table class="table table-report -mt-2">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">NAMA BAHAN</th>
                    <th class="text-center whitespace-nowrap">STOK</th>
                    <th class="whitespace-nowrap">SATUAN</th>
                    <th class="whitespace-nowrap">TERDAFTAR PADA</th>
                    <th class="text-center whitespace-nowrap">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bahan as $b)
                <tr class="intro-x">
                    <td>
                        <div class="font-medium whitespace-nowrap">{{ $b->nama }}</div>
                    </td>
                    <td class="text-center">
                        <div class="font-bold {{ ($b->stok->jumlah ?? 0) <= 5 ? 'text-danger' : 'text-success' }}">
                            {{ (float)($b->stok->jumlah ?? 0) }}
                        </div>
                    </td>
                    <td>
                        <div class="text-slate-500">{{ $b->satuan }}</div>
                    </td>
                    <td>{{ $b->created_at->format('d M Y') }}</td>
                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">
                            <a class="flex items-center mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-{{ $b->id }}"> 
                                <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit 
                            </a>
                            <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $b->id }}"> 
                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete 
                            </a>
                        </div>
                    </td>
                </tr>

                <div id="modal-edit-{{ $b->id }}" class="modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('bahan-baku.update', $b->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h2 class="font-medium text-base mr-auto">Edit Bahan Baku & Stok</h2>
                                </div>
                                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                    <div class="col-span-12">
                                        <label class="form-label">Nama Bahan</label>
                                        <input type="text" name="nama" class="form-control" value="{{ $b->nama }}" required>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label class="form-label">Stok Saat Ini</label>
                                        <input type="number" step="any" name="stok" class="form-control" value="{{ (float)($b->stok->jumlah ?? 0) }}" required>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label class="form-label">Satuan</label>
                                        <select name="satuan" class="form-select" required>
                                            @foreach(['Kg', 'Gram', 'Liter', 'Pcs', 'Meter'] as $satuan)
                                                <option value="{{ $satuan }}" {{ $b->satuan == $satuan ? 'selected' : '' }}>{{ $satuan }}</option>
                                            @endforeach
                                        </select>
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
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div id="modal-tambah-bahan" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('bahan-baku.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Tambah Bahan Baku</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <label class="form-label">Nama Bahan</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Tepung Terigu" required>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" step="any" name="stok" class="form-control" value="0">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label">Satuan</label>
                        <select name="satuan" class="form-select" required>
                            <option value="Kg">Kg</option>
                            <option value="Gram">Gram</option>
                            <option value="Liter">Liter</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Meter">Meter</option>
                        </select>
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