@extends('layouts.app')

@section('content')

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <button data-tw-toggle="modal" data-tw-target="#modal-tambah-client" class="btn btn-primary shadow-md mr-2">Tambah Client</button>
    </div>

    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow">
        <div class="preview">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">NO</th>
                            <th class="whitespace-nowrap">NAMA CLIENT</th>
                            <th class="whitespace-nowrap">TELEPON</th>
                            <th class="whitespace-nowrap">ALAMAT</th>
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clients as $key => $c)
                        <tr class="intro-x">
                            <td class="text-center w-10">{{ $key + 1 }}</td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $c->nama }}</div>
                                <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">{{ $c->catatan ?? 'Tidak ada catatan' }}</div>
                            </td>
                            <td>{{ $c->telp ?? '-' }}</td>
                            <td>{{ Str::limit($c->alamat, 50) ?? '-' }}</td>
                            <td class="table-report__action w-56">
                                <div class="flex justify- items-center">
                                    <a class="flex items-center mr-3 text-primary" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-{{ $c->id }}"> 
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit 
                                    </a>
                                    <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $c->id }}"> 
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
@foreach ($clients as $c)
    {{-- Modal Edit --}}
    <div id="modal-edit-{{ $c->id }}" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('client.update', $c->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Edit Client</h2>
                    </div>
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label class="form-label">Nama Client</label>
                            <input type="text" name="nama" class="form-control" value="{{ $c->nama }}" required>
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telp" class="form-control" value="{{ $c->telp }}">
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control">{{ $c->alamat }}</textarea>
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="catatan" class="form-control" value="{{ $c->catatan }}" required>
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
    <div id="modal-delete-{{ $c->id }}" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <form action="{{ route('client.destroy', $c->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="p-5 text-center">
                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i> 
                            <div class="text-3xl mt-5">Hapus Client?</div>
                            <div class="text-slate-500 mt-2">Data client <b>{{ $c->nama }}</b> akan dihapus secara permanen.</div>
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
<div id="modal-tambah-client" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('client.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Tambah Client Baru</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <label class="form-label">Nama Client</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap / Perusahaan" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telp" class="form-control" placeholder="08xxx">
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" placeholder="Alamat Client"></textarea>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Catatan tambahan jika ada" required>
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