@extends('layouts.app')

@section('content')


<div class="grid grid-cols-12 gap-6 mt-5">
    @if(auth()->user()->role == 'admin')
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <button data-tw-toggle="modal" data-tw-target="#modal-tambah-user" class="btn btn-primary shadow-md mr-2">Tambah User</button>
    </div>
    @endif

    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow">
        <div class="preview">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">NO</th>
                            <th class="whitespace-nowrap">NAMA</th>
                            <th class="whitespace-nowrap">EMAIL</th>
                            <th class="whitespace-nowrap">ROLE</th>
                            <th class="text-center whitespace-nowrap">TANGGAL DAFTAR</th>
                            @if(auth()->user()->role == 'admin')
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $key => $u)
                        <tr class="intro-x">
                            <td class="text-center w-10">{{ ($users->currentPage() - 1) * $users->perPage() + $key + 1 }}</td>
                            <td><div class="font-medium whitespace-nowrap">{{ $u->name }}</div></td>
                            <td><div class="text-slate-500">{{ $u->email }}</div></td>
                            <td><div class="text-slate-500">{{ $u->role }}</div></td>
                            <td class="text-center">{{ $u->created_at->format('d M Y') }}</td>
                            @if(auth()->user()->role == 'admin')
                            <td class="table-report__action w-56">
                                <div class="flex justify- items-center">
                                    <a class="flex items-center mr-3 text-primary" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-{{ $u->id }}"> 
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit 
                                    </a>
                                    <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $u->id }}"> 
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete 
                                    </a>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-5">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT & DELETE (Looping) --}}
@if(auth()->user()->role == 'admin')
@foreach ($users as $u)
    <div id="modal-edit-{{ $u->id }}" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('users.update', $u->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Edit Pengguna</h2>
                    </div>
                    <div class="modal-body grid grid-cols-12 gap-4">
                        <div class="col-span-12">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="admin" {{ $u->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ $u->role == 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>
                        <div class="col-span-12">
                        <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ganti">
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

    <div id="modal-delete-{{ $u->id }}" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <form action="{{ route('users.destroy', $u->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="p-5 text-center">
                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                            <div class="text-3xl mt-5">Apakah Anda yakin?</div>
                            <div class="text-slate-500 mt-2">Data user <b>{{ $u->name }}</b> akan dihapus secara permanen.</div>
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

{{-- MODAL TAMBAH USER --}}
<div id="modal-tambah-user" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Tambah Pengguna Baru</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Masukkan nama" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="admin">Admin</option>
                            <option value="staff" selected>Staff</option>
                        </select>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="******" required>
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
@endif

@endsection