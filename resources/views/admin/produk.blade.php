@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button data-tw-toggle="modal" data-tw-target="#modal-tambah-produk" class="btn btn-primary shadow-md mr-2">Tambah
                Produk</button>
            <a href="{{ route('stock-log.index', ['type' => 'produk']) }}" class="btn btn-outline-secondary">Lihat Log
                Produk</a>
        </div>

        <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">NO</th>
                            <th class="whitespace-nowrap">NAMA PRODUK</th>
                            <th class="text-center whitespace-nowrap">JENIS</th>
                            <th class="text-center whitespace-nowrap">STOK</th>
                            <th class="whitespace-nowrap">SATUAN</th>
                            <th class="whitespace-nowrap">HARGA DEFAULT</th>
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produk as $p)
                            <tr class="intro-x">
                                <td class="text-center w-10">
                                    {{ ($produk->currentPage() - 1) * $produk->perPage() + $loop->iteration }}</td>
                                <td>
                                    <div class="font-medium whitespace-nowrap">{{ $p->nama }}</div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium {{ $p->jenis == 'jadi' ? 'bg-success/20 text-success' : 'bg-warning/20 text-warning' }}">
                                        {{ strtoupper($p->jenis) }}
                                    </span>
                                </td>
                                <td class="text-center font-bold">
                                    <span class="{{ ($p->stok->jumlah ?? 0) <= 0 ? 'text-danger' : 'text-success' }}">
                                        {{ (float) ($p->stok->jumlah ?? 0) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-slate-500">{{ $p->satuan }}</div>
                                </td>
                                <td>
                                    <div class="text-primary font-bold">Rp
                                        {{ number_format($p->harga_default, 0, ',', '.') }}</div>
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify- items-center"> {{-- Pastikan justify-center --}}
                                        <a class="flex items-center mr-3 text-primary" href="javascript:;"
                                            data-tw-toggle="modal" data-tw-target="#modal-edit-{{ $p->id }}">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                        </a>
                                        <a class="flex items-center mr-3 text-danger" href="javascript:;"
                                            data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $p->id }}">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                        </a>
                                        <a class="flex items-center text-secondary"
                                            href="{{ route('stock-log.index', ['item_id' => $p->id, 'type' => 'produk']) }}">
                                            <i data-lucide="list" class="w-4 h-4 mr-1"></i> Log
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">{{ $produk->links() }}</div>
            </div>
        </div>
    </div>

    @foreach ($produk as $p)
        {{-- Modal Edit --}}
        <div id="modal-edit-{{ $p->id }}" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('produk.update', $p->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h2 class="font-medium text-base mr-auto">Edit Produk</h2>
                        </div>
                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                            <div class="col-span-12">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" name="nama" class="form-control" value="{{ $p->nama }}"
                                    required>
                            </div>
                            <div class="col-span-12 sm:col-span-6">
                                <label class="form-label">Jenis Produk</label>
                                <select name="jenis" class="form-select" required>
                                    <option value="jadi" {{ $p->jenis == 'jadi' ? 'selected' : '' }}>Barang Jadi</option>
                                    <option value="proses" {{ $p->jenis == 'proses' ? 'selected' : '' }}>Dalam Proses
                                    </option>
                                </select>
                            </div>
                            <div class="col-span-12 sm:col-span-6">
                                <label class="form-label">Satuan</label>
                                <input type="text" name="satuan" class="form-control" value="{{ $p->satuan }}"
                                    required>
                            </div>
                            <div class="col-span-12 sm:col-span-6">
                                <label class="form-label">Harga Default (Rp)</label>
                                <input type="number" name="harga_default" class="form-control"
                                    value="{{ $p->harga_default }}">
                            </div>
                            <div class="col-span-12 sm:col-span-6">
                                <label class="form-label">Stok Saat Ini</label>
                                <input type="text" class="form-control bg-slate-100"
                                    value="{{ (float) ($p->stok->jumlah ?? 0) }}" readonly>
                            </div>

                            {{-- Section Admin Only --}}
                            @if (auth()->user()->role == 'admin')
                                <div class="col-span-12 border-t border-slate-200/60 mt-2 pt-3">
                                    <label class="form-label text-primary font-bold">Koreksi Stok Manual (Admin
                                        Only)</label>
                                    <input type="number" step="any" name="stok_manual"
                                        class="form-control border-primary" placeholder="Masukkan jumlah stok baru">
                                </div>
                                <div class="col-span-12">
                                    <label class="form-label">Keterangan Perubahan</label>
                                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Alasan perubahan stok..."></textarea>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer text-right">
                            <button type="button" data-tw-dismiss="modal"
                                class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                            <button type="submit" class="btn btn-primary w-20">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="modal-delete-{{ $p->id }}" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <form action="{{ route('produk.destroy', $p->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="p-5 text-center">
                                <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                <div class="text-32l mt-5">Apakah Anda yakin?</div>
                                <div class="text-slate-500 mt-2">
                                    Apakah Anda benar-benar ingin menghapus produk <b>{{ $p->nama }}</b>? <br>Data
                                    yang sudah dihapus tidak dapat dikembalikan.
                                </div>
                            </div>
                            <div class="px-5 pb-8 text-center">
                                <button type="button" data-tw-dismiss="modal"
                                    class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                                <button type="submit" class="btn btn-danger w-24">Delete</button>
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
                            <input type="text" name="nama" class="form-control" placeholder="Nama Produk"
                                required>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Jenis Produk</label>
                            <select name="jenis" class="form-select" required>
                                <option value="jadi">Jadi</option>
                                <option value="proses">Proses</option>
                            </select>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" placeholder="Pcs/Pack" required>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Stok Awal</label>
                            <input type="number" step="any" name="stok" class="form-control" value="0">
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Harga Default (Rp)</label>
                            <input type="number" name="harga_default" class="form-control" placeholder="0">
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                        <button type="submit" class="btn btn-primary w-20">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
