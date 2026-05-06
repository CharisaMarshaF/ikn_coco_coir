@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <a href="{{ route('client.index') }}" class="btn btn-secondary shadow-md mr-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
                    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-laporan-client"
                class="btn btn-danger shadow-md mr-2">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Laporan PDF
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

        {{-- Modal Laporan PDF --}}
{{-- Modal Laporan PDF --}}
<div id="modal-laporan-client" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{-- Action diarahkan ke route cetak history dengan ID client --}}
            <form action="{{ route('client.history.cetak', $client->id) }}" method="GET" target="_blank">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Filter Laporan Histori Client</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
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