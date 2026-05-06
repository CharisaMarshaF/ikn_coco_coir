@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <a href="{{ route('supplier.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke List Supplier
        </a>
        {{-- <div class="hidden md:block mx-auto text-slate-500">
            Histori Transaksi: <span class="font-bold text-slate-700">{{ $supplier->nama }}</span>
        </div> --}}
    </div>

    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow mt-5">
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
                                <td class="text-center">
                                    @if ($p->status_pembayaran == 'lunas')
                                        <div class="flex items-center justify-center text-success font-medium">
                                            <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Lunas
                                        </div>
                                    @elseif($p->status_pembayaran == 'cancel')
                                        <div class="flex items-center justify-center text-danger font-medium uppercase italic">
                                            <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Cancelled
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center text-pending font-medium">
                                            <i data-lucide="clock" class="w-4 h-4 mr-2"></i> Belum Lunas
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($p->tanggal)->format('d F Y') }}
                                    </div>
                                </td>
                                <td class="w-40 text-right">
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