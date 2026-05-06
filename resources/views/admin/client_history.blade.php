@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <a href="{{ route('client.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Data Client
        </a>

    </div>

   
    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow mt-2">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap w-10 text-center">NO</th>
                            <th class="whitespace-nowrap">INVOICE</th>
                            <th class="text-center whitespace-nowrap">STATUS</th>
                            <th class="text-center whitespace-nowrap">TANGGAL</th>
                            <th class="text-right whitespace-nowrap">TOTAL</th>
                            <th class="text-center whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualan as $key => $p)
                            <tr class="intro-x">
                                <td class="text-center font-medium">{{ $key + 1 }}</td>
                                <td>
                                    <a href="{{ route('penjualan.show', $p->id) }}" class="underline decoration-dotted font-medium text-primary">
                                        #PJ-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td class="text-">
                                    @if ($p->status == 'berhasil')
                                        <span class="text-success font-medium flex items-center justify-">
                                            <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> Berhasil
                                        </span>
                                    @elseif($p->status == 'cancel')
                                        <span class="text-danger font-medium flex items-center justify-">
                                            <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i> Batal
                                        </span>
                                    @else
                                        <span class="text-pending font-medium flex items-center justify-">
                                            <i data-lucide="refresh-ccw" class="w-4 h-4 mr-1"></i> Return
                                        </span>
                                    @endif
                                </td>
                                <td class="text-">
                                    {{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="text- font-bold">
                                    Rp {{ number_format($p->total, 0, ',', '.') }}
                                </td>
                                <td class="text-">
                                    <a href="{{ route('penjualan.show', $p->id) }}" ">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
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
        $('#example1').DataTable({
            "language": {
                "search": "Cari Transaksi:",
                "emptyTable": "Client ini belum memiliki histori transaksi"
            },
            "order": [[0, "asc"]],
            "responsive": true
        });
    });
</script>
@endsection