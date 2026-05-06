@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <a href="{{ route('supplier.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
        {{-- <div class="hidden md:block mx-auto text-slate-500">
            Histori Transaksi: <span class="font-bold text-slate-700">{{ $supplier->nama }}</span>
        </div> --}}
        <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-laporan-transaksi"
                class="btn btn-danger shadow-md mr-2">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Laporan PDF
            </a>
    </div>

    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow mt-2">
        <div class="preview">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap w-10 text-center">NO</th>
                            <th class="whitespace-nowrap">INVOICE</th>
                            <th class="text-center whitespace-nowrap">STATUS</th>
                            <th class="whitespace-nowrap text-center">TANGGAL</th>
                            <th class="text-right whitespace-nowrap">TOTAL TRANSAKSI</th>
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembelian as $key => $p)
                            <tr class="intro-x">
                                <td class="text-center font-medium w-10">{{ $key + 1 }}</td>
                                <td class="w-40 !py-4">
                                    <a href="{{ route('pembelian.show', $p->id) }}" class="underline decoration-dotted whitespace-nowrap font-medium text-primary">
                                        #PB-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td class="text-">
                                    @if ($p->status_pembayaran == 'lunas')
                                        <div class="flex items-center justify- text-success font-medium">
                                            <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Lunas
                                        </div>
                                    @elseif($p->status_pembayaran == 'cancel')
                                        <div class="flex items-center justify- text-danger font-medium uppercase italic">
                                            <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Cancelled
                                        </div>
                                    @else
                                        <div class="flex items-center justify- text-pending font-medium">
                                            <i data-lucide="clock" class="w-4 h-4 mr-2"></i> Belum Lunas
                                        </div>
                                    @endif
                                </td>
                                <td class="text-">
                                    <div class="whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($p->tanggal)->format('d F Y') }}
                                    </div>
                                </td>
                                <td class="w-40 text-">
                                    <div class="font-bold text-primary">
                                        Rp {{ number_format($p->total, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="table-report__action text-center">
                                    <div class="flex justify-center items-center">
                                        <a class="flex items-center text-primary font-medium" href="{{ route('pembelian.show', $p->id) }}">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
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

        {{-- Modal Laporan PDF --}}
        <div id="modal-laporan-transaksi" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Form diarahkan ke method cetakPengambilan di Controller --}}
                    {{-- Ganti baris ini --}}
                    {{-- Ganti bagian form di dalam modal-laporan-pengambilan --}}
                    <form action="{{ route('supplier.transaksi.cetak', $supplier->id) }}" method="GET" target="_blank">
                        <div class="modal-header">
                            <h2 class="font-medium text-base mr-auto">Filter Laporan Transaksi Supplier</h2>
                        </div>
                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                            <div class="col-span-12">
                                <label class="form-label">Nama Supplier</label>
                                <input type="text" class="form-control" value="{{ $supplier->nama }}" readonly>
                            </div>
                            <div class="col-span-12">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" 
                                    value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-span-12">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" 
                                    value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer text-right">
                            <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                            <button type="submit" class="btn btn-primary w-32">
                                <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Pastikan ID ini sama dengan ID pada tabel di atas
        $('#example1').DataTable({
            "language": {
                "search": "Cari Invoice:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada transaksi ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Data tidak tersedia",
                "infoFiltered": "(difilter dari _MAX_ total data)"
            },
            "order": [[0, "asc"]], // Urutkan berdasarkan NO
            "responsive": true
        });
    });
</script>
@endsection