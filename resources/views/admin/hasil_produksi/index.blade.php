@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a href="{{ route('hasil-produksi.create') }}" class="btn btn-primary shadow-md mr-2">Catat Hasil Produksi</a>
            <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-laporan-hasil-produksi"
                class="btn btn-danger shadow-md mr-2">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Laporan PDF
            </a>
        </div>

        <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">NO</th>
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
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-bold text-primary">#{{ $h->kode_produksi }}</td>
                                <td>{{ \Carbon\Carbon::parse($h->tanggal)->format('d/m/Y') }}</td>
                                <td>
                                    @foreach ($h->details as $det)
                                        <div class="text-xs">• {{ $det->produk->nama }} ({{ (float) $det->qty }}
                                            {{ $det->produk->satuan }})</div>
                                    @endforeach
                                </td>
                                <td>{{ $h->user->name ?? 'System' }}</td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify- items-center">
                                        <!-- Tombol Pemicu Modal (Disamakan dengan Bahan Baku) -->
                                        <a class="flex items-center mr-3 text-primary"
                                            href="{{ route('hasil-produksi.show', $h->id) }}">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                        </a>
                                        <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#modal-delete-{{ $h->id }}">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Batalkan
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
                                    Data produksi <b>#{{ $h->kode_produksi }}</b> akan dibatalkan.<br>
                                    <span class="text-danger font-medium text-xs">Stok produk akan dikurangi kembali secara
                                        otomatis!</span>
                                </div>
                            </div>
                            <div class="px-5 pb-8 text-center">
                                <button type="button" data-tw-dismiss="modal"
                                    class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                                <button type="submit" class="btn btn-danger w-24">Batalkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- Modal Laporan PDF --}}
        <div id="modal-laporan-hasil-produksi" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Form diarahkan ke method cetakPembelian di Controller --}}
                    {{-- Ganti baris ini --}}
                    <form action="{{ route('hasil-produksi.cetak') }}" method="GET" target="_blank">
                        <div class="modal-header">
                            <h2 class="font-medium text-base mr-auto">Filter Laporan Hasil Produksi</h2>
                        </div>
                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                            <div class="col-span-12">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" {{-- Mengatur ke tanggal 1 bulan berjalan --}}
                                    value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-span-12">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" {{-- Mengatur ke tanggal terakhir bulan berjalan --}}
                                    value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer text-right">
                            <button type="button" data-tw-dismiss="modal"
                                class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                            <button type="submit" class="btn btn-primary w-32">
                                <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Script untuk Notifikasi Sukses (Tetap pakai SweetAlert agar elegan saat berhasil) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
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
