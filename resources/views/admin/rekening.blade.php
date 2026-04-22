@extends('layouts.app')

@section('content')
<h2 class="intro-y text-lg font-medium mt-10">Daftar Rekening Keuangan</h2>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <button data-tw-toggle="modal" data-tw-target="#modal-tambah-rekening" class="btn btn-primary shadow-md mr-2">Tambah Rekening</button>
        
        <div class="hidden md:block mx-auto text-slate-500">Showing {{ $rekening->firstItem() }} to {{ $rekening->lastItem() }} of {{ $rekening->total() }} entries</div>
        
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
                    <th class="whitespace-nowrap">NAMA REKENING</th>
                    <th class="whitespace-nowrap">JENIS</th>
                    <th class="whitespace-nowrap text-right">SALDO AWAL</th>
                    <th class="whitespace-nowrap text-right">SALDO SAAT INI</th>
                    <th class="text-center whitespace-nowrap">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekening as $r)
                <tr class="intro-x">
                    <td>
                        <div class="font-medium whitespace-nowrap">{{ $r->nama }}</div>
                    </td>
                    <td>
                        <div class="text-slate-500 uppercase">{{ $r->jenis }}</div>
                    </td>
                    <td class="text-right">Rp {{ number_format($r->saldo_awal, 0, ',', '.') }}</td>
                    <td class="text-right font-bold text-primary">Rp {{ number_format($r->saldo_saat_ini, 0, ',', '.') }}</td>
                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">
                            <a class="flex items-center mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-{{ $r->id }}"> 
                                <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit 
                            </a>
                            <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $r->id }}"> 
                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete 
                            </a>
                        </div>
                    </td>
                </tr>

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
                                            <i data-lucide="alert-circle" class="w-6 h-6 mr-2"></i> Saldo tidak dapat diubah dari sini untuk menjaga integritas data transaksi. 
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
                                        <div class="text-slate-500 mt-2">Menghapus rekening <b>{{ $r->nama }}</b> akan menghapus riwayat transaksi terkait.</div>
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
        {{ $rekening->links() }}
    </div>
</div>

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
                        <input type="number" name="saldo_awal" class="form-control" placeholder="0" required>
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