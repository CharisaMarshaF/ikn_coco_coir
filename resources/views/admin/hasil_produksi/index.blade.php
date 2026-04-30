@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <a href="{{ route('hasil-produksi.create') }}" class="btn btn-primary shadow-md mr-2">Catat Hasil Produksi</a>
    </div>

    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">KODE</th>
                        <th class="whitespace-nowrap">TANGGAL</th>
                        <th class="whitespace-nowrap">PRODUK DIHASILKAN</th>
                        <th class="whitespace-nowrap">PETUGAS</th>
                        <th class="text-center whitespace-nowrap">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hasilProduksi as $h)
                    <tr class="intro-x">
                        <td class="font-bold text-primary">#{{ $h->kode_produksi }}</td>
                        <td>{{ \Carbon\Carbon::parse($h->tanggal)->format('d/m/Y') }}</td>
                        <td>
                            @foreach($h->details as $det)
                                <div class="text-xs">• {{ $det->produk->nama }} ({{ (float)$det->qty }} {{ $det->produk->satuan }})</div>
                            @endforeach
                        </td>
                        <td>{{ $h->user->name ?? 'System' }}</td>
                        <td class="table-report__action w-56">
                            <div class="flex justify- items-center">
                                <!-- Tombol Pemicu Modal (Disamakan dengan Bahan Baku) -->
                                <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-delete-{{ $h->id }}"> 
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Hapus 
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

{{-- Modal Section --}}
@foreach ($hasilProduksi as $h)
    {{-- Modal Delete (Desain disamakan persis dengan Bahan Baku) --}}
    <div id="modal-delete-{{ $h->id }}" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <form action="{{ route('hasil-produksi.destroy', $h->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="p-5 text-center">
                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i> 
                            <div class="text-3xl mt-5">Apakah Anda yakin?</div>
                            <div class="text-slate-500 mt-2">
                                Data produksi <b>#{{ $h->kode_produksi }}</b> akan dihapus.<br>
                                <span class="text-danger font-medium text-xs">Stok produk akan dikurangi kembali secara otomatis!</span>
                            </div>
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

<!-- Script untuk Notifikasi Sukses (Tetap pakai SweetAlert agar elegan saat berhasil) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000
        });
    @endif
</script>
@endsection