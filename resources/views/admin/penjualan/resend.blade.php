@extends('layouts.app')

@section('content')
<div class="intro-y flex items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Kirim Ulang Barang Return</h2>
</div>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 lg:col-span-6">
        <div class="box p-5">
            <form action="{{ route('penjualan.store_resend', $penjualanAsal->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label font-bold">Client</label>
                    <input type="text" class="form-control bg-slate-100" value="{{ $penjualanAsal->client->nama }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label font-bold">Tanggal Pengiriman Baru</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="table table-bordered">
                        <thead class="bg-slate-100">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty dikirim</th>
                                <th class="text-right">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penjualanAsal->detail as $item)
                            <tr>
                                <td>{{ $item->produk->nama_produk }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-right text-danger">Rp 0 (Default)</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 flex justify-end">
                    <a href="{{ route('penjualan.return_list') }}" class="btn btn-secondary w-24 mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary w-40">Konfirmasi Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection