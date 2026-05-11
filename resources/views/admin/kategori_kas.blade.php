@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button data-tw-toggle="modal" data-tw-target="#modal-tambah-kategori" class="btn btn-primary shadow-md mr-2">
                Tambah Kategori
            </button>
        </div>
        <div class="intro-y col-span-12">
            {{-- Alert Success --}}
            @if (session('success'))
                <div class="alert alert-success text-white alert-dismissible show flex items-center mb-2" role="alert">
                    <i data-lucide="check-circle" class="w-6 h-6 mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close text-white" data-tw-dismiss="alert" aria-label="Close">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            {{-- Alert Error --}}
            @if (session('error'))
                <div class="alert alert-danger text-white alert-dismissible show flex items-center mb-2" role="alert">
                    <i data-lucide="alert-octagon" class="w-6 h-6 mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close text-white" data-tw-dismiss="alert" aria-label="Close">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif
        </div>

        <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap w-10">NO</th>
                            <th class="whitespace-nowrap">NAMA KATEGORI</th>
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kategori as $k)
                            <tr class="intro-x">
                                <td class="text-center">
                                    {{ ($kategori->currentPage() - 1) * $kategori->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <div class="font-medium whitespace-nowrap">{{ $k->nama }}</div>
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex items-center">
                                        <a class="flex items-center mr-3 text-primary" href="javascript:;"
                                            data-tw-toggle="modal" data-tw-target="#modal-edit-{{ $k->id }}">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                        </a>
                                        <a class="flex items-center text-danger" href="javascript:;"
                                            data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $k->id }}">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">{{ $kategori->links() }}</div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div id="modal-tambah-kategori" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kategori-kas.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Tambah Kategori Kas</h2>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Operasional" required>
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

    @foreach ($kategori as $k)
        {{-- Modal Edit --}}
        <div id="modal-edit-{{ $k->id }}" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('kategori-kas.update', $k->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h2 class="font-medium text-base mr-auto">Edit Kategori</h2>
                        </div>
                        <div class="modal-body">
                            <div>
                                <label class="form-label">Nama Kategori</label>
                                <input type="text" name="nama" class="form-control" value="{{ $k->nama }}" required>
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
        <div id="modal-delete-{{ $k->id }}" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <form action="{{ route('kategori-kas.destroy', $k->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="p-5 text-center">
                                <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                <div class="text-32l mt-5">Apakah Anda yakin?</div>
                                <div class="text-slate-500 mt-2">
                                    Data <b>{{ $k->nama }}</b> akan dihapus secara permanen.
                                </div>
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
@endsection