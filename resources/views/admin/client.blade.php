@extends('layouts.app')

@section('content')
<h2 class="intro-y text-lg font-medium mt-10">Daftar Client</h2>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <button data-tw-toggle="modal" data-tw-target="#modal-tambah-client" class="btn btn-primary shadow-md mr-2">Tambah Client</button>
        
        <div class="hidden md:block mx-auto text-slate-500">Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} of {{ $clients->total() }} entries</div>
        
        <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            {{-- <div class="w-56 relative text-slate-500">
                <input type="text" class="form-control w-56 box pr-10" placeholder="Search...">
                <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i> 
            </div> --}}
        </div>
    </div>

    <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
        <table class="table table-report -mt-2">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">NAMA CLIENT</th>
                    <th class="whitespace-nowrap">TELEPON</th>
                    <th class="whitespace-nowrap">ALAMAT</th>
                    <th class="text-center whitespace-nowrap">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clients as $c)
                <tr class="intro-x">
                    <td>
                        <div class="font-medium whitespace-nowrap">{{ $c->nama }}</div>
                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">{{ $c->catatan ?? 'Tidak ada catatan' }}</div>
                    </td>
                    <td>{{ $c->telp ?? '-' }}</td>
                    <td>{{ Str::limit($c->alamat, 50) ?? '-' }}</td>
                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">
                            <a class="flex items-center mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-{{ $c->id }}"> 
                                <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit 
                            </a>
                            <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $c->id }}"> 
                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete 
                            </a>
                        </div>
                    </td>
                </tr>

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
                                        <textarea name="catatan" class="form-control">{{ $c->catatan }}</textarea>
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
                                        <div class="text-slate-500 mt-2">Data client <b>{{ $c->nama }}</b> akan dihapus permanen.</div>
                                    </div>
                                    <div class="px-5 pb-8 text-center">
                                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                                        <button type="submit" class="btn btn-danger w-24">Delete</button>
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

    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center">
        {{ $clients->links() }}
    </div>
</div>

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
                        <textarea name="catatan" class="form-control" placeholder="Catatan tambahan jika ada"></textarea>
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