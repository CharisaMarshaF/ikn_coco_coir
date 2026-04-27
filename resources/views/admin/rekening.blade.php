@extends('layouts.app')

@section('content')
<h2 class="intro-y text-lg font-medium mt-10">Daftar Rekening Keuangan</h2>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <button data-tw-toggle="modal" data-tw-target="#modal-tambah-rekening" class="btn btn-primary shadow-md mr-2">Tambah Rekening</button>
    </div>

    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow">
        <div class="preview">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap w-10">NO</th>
                            <th class="whitespace-nowrap">NAMA REKENING</th>
                            <th class="whitespace-nowrap">JENIS</th>
                            <th class="whitespace-nowrap text-right">SALDO</th>
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekening as $key => $r)
                        <tr class="intro-x">
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $r->nama }}</div>
                            </td>
                            <td>
                                <div class="text-slate-500 uppercase">{{ $r->jenis }}</div>
                            </td>
                            <td class="text-left font-bold text-primary">
                                Rp {{ number_format($r->saldo, 0, ',', '.') }}
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify- items-center">
                                    <a class="flex items-center mr-3 text-primary" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-{{ $r->id }}"> 
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit 
                                    </a>
                                    <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $r->id }}"> 
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
@foreach ($rekening as $r)
    {{-- Modal Edit --}}
    <div id="modal-edit-{{ $r->id }}" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('rekening.update', $r->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Edit Rekening</h2>
                    </div>
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label class="form-label">Nama Rekening</label>
                            <input type="text" name="nama" class="form-control" value="{{ $r->nama }}" required>
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Jenis</label>
                            <select name="jenis" class="form-select" required>
                                <option value="kas" {{ $r->jenis == 'kas' ? 'selected' : '' }}>KAS</option>
                                <option value="bank" {{ $r->jenis == 'bank' ? 'selected' : '' }}>BANK</option>
                            </select>
                        </div>
                        <div class="col-span-12">
                            <div class="alert alert-warning-soft show flex items-center mb-2" role="alert"> 
                                <i data-lucide="alert-circle" class="w-6 h-6 mr-2"></i> 
                                Saldo saat ini: <b>Rp {{ number_format($r->saldo, 0, ',', '.') }}</b>.
                            </div>
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
    <div id="modal-delete-{{ $r->id }}" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <form action="{{ route('rekening.destroy', $r->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="p-5 text-center">
                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i> 
                            <div class="text-3xl mt-5">Hapus Rekening?</div>
                            <div class="text-slate-500 mt-2">Data rekening <b>{{ $r->nama }}</b> akan dihapus secara permanen.</div>
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
<div id="modal-tambah-rekening" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('rekening.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Tambah Rekening Baru</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <label class="form-label">Nama Rekening</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Kas Toko / Bank BCA" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Jenis</label>
                        <select name="jenis" class="form-select" required>
                            <option value="kas">KAS (Uang Tunai)</option>
                            <option value="bank">BANK (Transfer)</option>
                        </select>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Saldo Awal</label>
                        <input type="number" name="saldo" class="form-control" placeholder="0" min="0" required>
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