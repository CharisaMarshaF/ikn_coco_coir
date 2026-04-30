@extends('layouts.app')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">

</div>

<div class="intro-y grid grid-cols-12 gap-5 mt-5">
    <div class="col-span-12 lg:col-span-4">
        <div class="box p-5 rounded-md">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate">Informasi Produksi</div>
            </div>
            <div class="flex items-center"> 
                <i data-lucide="hash" class="w-4 h-4 text-slate-500 mr-2"></i> Kode: 
                <span class="font-medium ml-1 text-primary">#PRD-{{ str_pad($produksi->id, 5, '0', STR_PAD_LEFT) }}</span> 
            </div>
            <div class="flex items-center mt-3"> 
                <i data-lucide="calendar" class="w-4 h-4 text-slate-500 mr-2"></i> Tanggal: 
                <span class="ml-1">{{ \Carbon\Carbon::parse($produksi->tanggal)->format('d F Y') }}</span> 
            </div>
            <div class="flex items-center mt-3 border-b border-slate-100 pb-5"> 
                <i data-lucide="activity" class="w-4 h-4 text-slate-500 mr-2"></i> Status: 
                    @if($produksi->status == 'berhasil')
                        <span class="bg-success/20 text-success rounded px-2 ml-1 text-xs font-medium">Berhasil & Update Stok</span>
                    @elseif($produksi->status == 'proses')
                        <span class="bg-warning/20 text-warning rounded px-2 ml-1 text-xs font-medium">Sedang Proses</span>
                    @elseif($produksi->status == 'cancel')
                        <span class="bg-danger/20 text-danger rounded px-2 ml-1 text-xs font-medium">Dibatalkan</span>
                    @else
                        <span class="bg-danger/20 text-danger rounded px-2 ml-1 text-xs font-medium">Reject / Gagal</span>
                    @endif

            </div>

            @if($produksi->status == 'proses')
            <div class="mt-5 space-y-2">
                <p class="text-xs text-slate-500 mb-2 font-medium">TINDAKAN CEPAT:</p>
                <form action="{{ route('produksi.update-status', $produksi->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="berhasil">
                    <button type="submit" class="btn btn-primary w-full shadow-md">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Tandai Selesai
                    </button>
                </form>
                
                <form action="{{ route('produksi.update-status', $produksi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mereject produksi ini?')">
                    @csrf
                    <input type="hidden" name="status" value="reject">
                    <button type="submit" class="btn btn-outline-danger w-full mt-2">
                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Reject (Gagal)
                    </button>
                </form>
            </div>
            @endif

            @if($produksi->status == 'reject')
            <div class="mt-5">
                <a href="{{ route('produksi.repair', $produksi->id) }}" class="btn btn-warning w-full text-white shadow-md">
                    <i data-lucide="wrench" class="w-4 h-4 mr-2"></i> Perbaiki Produksi (Repair)
                </a>
            </div>
            @endif
        </div>

        <div class="box p-5 rounded-md mt-5">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate">Catatan / Keterangan</div>
            </div>
            <div class="text-slate-600 leading-relaxed italic text-sm">
                {{ $produksi->keterangan ?? 'Tidak ada catatan tambahan untuk produksi ini.' }}
            </div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-8">
        <div class="box p-5 rounded-md">
            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                <div class="font-medium text-base truncate text-danger">Bahan Baku yang Digunakan (Keluar)</div>
            </div>
            <div class="overflow-auto lg:overflow-visible">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Nama Bahan</th>
                            <th class="text-right whitespace-nowrap">Jumlah Digunakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produksi->detail->where('jenis', 'bahan') as $detail)
                        <tr>
                            <td>
                                <div class="font-medium whitespace-nowrap">
                                    {{ $detail->bahan->nama ?? 'Bahan Tidak Ditemukan' }}
                                    @if($detail->keterangan)
                                        <span class="ml-2 px-2 py-0.5 bg-warning/20 text-warning rounded text-[10px] font-bold">{{ $detail->keterangan }}</span>
                                    @endif
                                </div>
                                <div class="text-slate-500 text-xs mt-0.5">{{ $detail->bahan->satuan ?? '-' }}</div>
                            </td>
                            <td class="text-right font-medium text-danger">- {{ $detail->qty }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5 mt-10">
                <div class="font-medium text-base truncate text-success">Hasil Produk Jadi (Masuk)</div>
            </div>
            <div class="overflow-auto lg:overflow-visible">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Nama Produk</th>
                            <th class="text-right whitespace-nowrap">Jumlah Dihasilkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produksi->detail->where('jenis', 'produk') as $detail)
                        <tr>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $detail->produk->nama ?? 'Produk Tidak Ditemukan' }}</div>
                            </td>
                            <td class="text-right font-medium text-success">+ {{ $detail->qty }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection