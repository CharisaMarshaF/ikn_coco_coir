@extends('layouts.app')

@section('content')

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
    <button data-tw-toggle="modal" data-tw-target="#modal-tambah-bahan" class="btn btn-primary shadow-md mr-2">Tambah Bahan Baku</button>
    
    <a href="{{ route('stock-log.index', ['type' => 'bahan_baku']) }}" class="btn btn-outline-secondary shadow-md mr-2">
        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Log Bahan Baku
    </a>
    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-laporan-bahan"
        class="btn btn-danger shadow-md mr-2">
        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Laporan PDF
    </a>
</div>

    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow">
        <div class="preview">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">NO</th>
                            <th class="whitespace-nowrap">NAMA BAHAN</th>
                            <th class="text-center whitespace-nowrap">STOK</th>
                            <th class="whitespace-nowrap">SATUAN</th>
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bahan as $key => $b)
                        <tr class="intro-x">
                            <td class="text-center w-10">{{ $key + 1 }}</td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $b->nama }}</div>
                            </td>
                            <td class="text-">
                                <div class="font-bold {{ ($b->stok->jumlah ?? 0) <= 5 ? 'text-danger' : 'text-success' }}">
                                    {{ (float)($b->stok->jumlah ?? 0) }}
                                </div>
                            </td>
                            <td>
                                <div class="text-slate-500">{{ $b->satuan }}</div>
                            </td>
                            
                            <td class="table-report__action w-56">
                                <div class="flex justify- items-center">
                                    <a class="flex items-center mr-3 text-primary" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-{{ $b->id }}"> 
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit 
                                    </a>
                                    <a class="flex items-center mr-3 text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $b->id }}"> 
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete  
                                    </a>
                                     <a href="{{ route('stock-log.index', ['item_id' => $b->id, 'type' => 'bahan_baku']) }}" class="flex items-center mr-3 text-primary">
                                        <i data-lucide="list" class="w-4 h-4 mr-1"></i> Log
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
@foreach ($bahan as $b)
    {{-- Modal Edit --}}
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
                            <label class="form-label">Satuan</label>
                            <select name="satuan" class="form-select" required>
                                @foreach(['Kg', 'Gram', 'Liter', 'Pcs', 'Meter'] as $satuan)
                                    <option value="{{ $satuan }}" {{ $b->satuan == $satuan ? 'selected' : '' }}>{{ $satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Stok Saat Ini (Sistem)</label>
                            <input type="text" class="form-control bg-slate-100" value="{{ (float)($b->stok->jumlah ?? 0) }}" readonly>
                        </div>

                        {{-- Hak Akses Tinggi: Edit Stok Manual --}}
                        @if(auth()->user()->role == 'admin')
                        <div class="col-span-12 border-t border-slate-200/60 mt-2 pt-3">
                            <label class="form-label text-primary font-bold">Edit Stok Manual (Admin Only)</label>
                            <input type="number" step="any" name="stok_manual" class="form-control border-primary" placeholder="Masukkan jumlah stok baru">
                            <div class="form-help text-xs">Kosongkan jika tidak ingin mengubah jumlah stok.</div>
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Keterangan Alasan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Alasan perubahan stok manual..."></textarea>
                        </div>
                        @endif
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
    <div id="modal-delete-{{ $b->id }}" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <form action="{{ route('bahan-baku.destroy', $b->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="p-5 text-center">
                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i> 
                            <div class="text-3xl mt-5">Apakah Anda yakin?</div>
                            <div class="text-slate-500 mt-2">Data bahan baku <b>{{ $b->nama }}</b> akan dihapus secara permanen.</div>
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
{{-- Modal Laporan Per Bahan --}}
<!-- Modal Laporan PDF -->
<div id="modal-laporan-bahan" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('bahan-baku.laporan') }}" method="GET" target="_blank">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Filter Laporan Mutasi Bahan</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <label class="form-label">Pilih Bahan Baku</label>
                        <select name="bahan_id" class="form-select" required>
                            @foreach($bahan as $b)
                                <option value="{{ $b->id }}">{{ $b->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" 
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Sampai Tanggal (1 Bulan Kedepan)</label>
                        <input type="date" name="end_date" class="form-control" 
                            value="{{ date('Y-m-d', strtotime('+1 month')) }}" required>
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