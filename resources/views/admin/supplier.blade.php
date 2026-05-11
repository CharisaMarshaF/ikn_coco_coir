@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button data-tw-toggle="modal" data-tw-target="#modal-tambah-supplier"
                class="btn btn-primary shadow-md mr-2">Tambah Supplier</button>
        </div>
<div class="intro-y col-span-12">
            {{-- Alert Success --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible show flex items-center mb-2" role="alert">
                    <i data-lucide="check-circle" class="w-6 h-6 mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-tw-dismiss="alert" aria-label="Close">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            {{-- Alert Error --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible show flex items-center mb-2" role="alert">
                    <i data-lucide="alert-octagon" class="w-6 h-6 mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-tw-dismiss="alert" aria-label="Close">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif
        </div>
        <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow">
            <div class="preview">
                <div class="overflow-x-auto">
                    <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">NO</th>
                                <th class="whitespace-nowrap">NAMA SUPPLIER</th>
                                <th class="whitespace-nowrap">TELEPON</th>
                                <th class="whitespace-nowrap">ALAMAT SUPPLIER</th>
                                <th class="text-center whitespace-nowrap">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers as $key => $s)
                                <tr class="intro-x">
                                    <td class="text-center w-10">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="font-medium whitespace-nowrap">{{ $s->nama }}</div>
                                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">
                                            {{ $s->keterangan ?? 'No description' }}</div>
                                    </td>
                                    <td>{{ $s->telp }}</td>
                                    <td>{{ Str::limit($s->alamat, 50) }}</td>
                                    <!-- Cari bagian <td class="table-report__action w-56"> dan update isinya -->
                                    <td class="table-report__action w-56">
                                        <div class="flex justify- items-center">
                                            <a class="flex items-center mr-3 text-success"
                                                href="{{ route('supplier.transaksi', $s->id) }}">
                                                <i data-lucide=" shopping-cart" class="w-4 h-4 mr-1"></i> Transaksi
                                            </a>
                                            <a class="flex items-center mr-3 text-primary" href="javascript:;"
                                                data-tw-toggle="modal" data-tw-target="#modal-edit-{{ $s->id }}">
                                                <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                            </a>
                                            <a class="flex items-center text-danger" href="javascript:;"
                                                data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $s->id }}">
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
    @foreach ($suppliers as $s)
        {{-- Modal Edit --}}
        <div id="modal-edit-{{ $s->id }}" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('supplier.update', $s->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h2 class="font-medium text-base mr-auto">Edit Supplier</h2>
                        </div>
                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                            <div class="col-span-12">
                                <label class="form-label">Nama Supplier</label>
                                <input type="text" name="nama" class="form-control" value="{{ $s->nama }}"
                                    required>
                            </div>
                            <div class="col-span-12">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="telp" class="form-control" value="{{ $s->telp }}">
                            </div>
                            <div class="col-span-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3">{{ $s->alamat }}</textarea>
                            </div>
                            <div class="col-span-12">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" value="{{ $s->keterangan }}"
                                    required>
                            </div>
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

        {{-- Modal Delete --}}
        <div id="modal-delete-{{ $s->id }}" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <form action="{{ route('supplier.destroy', $s->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="p-5 text-center">
                                <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                <div class="text-3xl mt-5">Apakah Anda yakin?</div>
                                <div class="text-slate-500 mt-2">Data supplier <b>{{ $s->nama }}</b> akan dihapus
                                    secara permanen.</div>
                            </div>
                            <div class="px-5 pb-8 text-center">
                                <button type="button" data-tw-dismiss="modal"
                                    class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                                <button type="submit" class="btn btn-danger w-24">Hapus</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Tambah --}}
    <div id="modal-tambah-supplier" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('supplier.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Tambah Supplier Baru</h2>
                    </div>
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label class="form-label">Nama Supplier</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama PT/Toko"
                                required>
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telp" class="form-control" placeholder="0812...">
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" placeholder="Alamat lengkap..." rows="3"></textarea>
                        </div>

                        <div class="col-span-12">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control"
                                placeholder="Catatan tambahan..." required>
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
