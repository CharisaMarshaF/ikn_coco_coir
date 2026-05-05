@extends('layouts.app')

@section('content')

    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Detail Penjualan #PJ-{{ str_pad($penjualan->id, 5, '0', STR_PAD_LEFT) }}</h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0 gap-2 no-print">

            {{-- Tombol Invoice & SJ --}}
            @if ($penjualan->status == 'berhasil' || $penjualan->status == 'return')
                <a href="{{ route('penjualan.pdf', [$penjualan->id, 'type' => 'invoice']) }}" target="_blank"
                    class="btn btn-outline-secondary shadow-md">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Invoice
                </a>
                <a href="{{ route('penjualan.pdf', [$penjualan->id, 'type' => 'sj']) }}" target="_blank"
                    class="btn btn-outline-success shadow-md">
                    <i data-lucide="truck" class="w-4 h-4 mr-2"></i> Surat Jalan
                </a>
            @endif

            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary shadow-md">Kembali</a>
        </div>
    </div>
    @if (session('success'))
        <div class="intro-y col-span-12">
            <div class="alert alert-success show flex items-center mb-2" role="alert">
                <i data-lucide="check-circle" class="w-6 h-6 mr-2"></i> {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="intro-y col-span-12">
            <div class="alert alert-danger show flex items-center mb-2" role="alert">
                <i data-lucide="alert-octagon" class="w-6 h-6 mr-2"></i> {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="intro-y grid grid-cols-12 gap-5 mt-5">
        <!-- KIRI: INFORMASI TRANSAKSI -->
        <div class="col-span-12 lg:col-span-4">
            <div class="box p-5 rounded-md">
                <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                    <div class="font-medium text-base truncate">Informasi Pelanggan</div>
                </div>
                <div class="flex items-center">
                    <i data-lucide="user" class="w-4 h-4 text-slate-500 mr-2"></i> Nama:
                    <span class="font-medium ml-1">{{ $penjualan->client->nama ?? 'Pembeli Umum (Anonim)' }}</span>
                </div>
                <div class="flex items-center mt-3">
                    <i data-lucide="calendar" class="w-4 h-4 text-slate-500 mr-2"></i> Tanggal:
                    <span class="ml-1">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d F Y') }}</span>
                </div>
                <div class="flex items-center mt-3 border-b border-slate-100 pb-5">
                    <i data-lucide="activity" class="w-4 h-4 text-slate-500 mr-2"></i> Status:
                    @if ($penjualan->status == 'berhasil')
                        <span
                            class="bg-success/20 text-success rounded px-2 ml-1 text-xs font-medium uppercase">Berhasil</span>
                    @elseif($penjualan->status == 'return')
                        <span
                            class="bg-warning/20 text-warning rounded px-2 ml-1 text-xs font-medium uppercase">Dikembalikan
                            (Return)</span>
                    @else
                        <span class="bg-danger/20 text-danger rounded px-2 ml-1 text-xs font-medium uppercase">Dibatalkan
                            (Cancel)</span>
                    @endif
                </div>

                {{-- Tombol tindakan hanya muncul jika status masih 'berhasil' --}}
                {{-- Cek apakah status berhasil DAN bukan transaksi resend (total > 0) --}}
                @if ($penjualan->status == 'berhasil' && $penjualan->total > 0)
                    <div class="mt-5 space-y-2">
                        <p class="text-xs text-slate-500 mb-2 font-medium">TINDAKAN TRANSAKSI:</p>

                        <button data-tw-toggle="modal" data-tw-target="#modal-return"
                            class="btn btn-warning w-full text-white shadow-md">
                            <i data-lucide="refresh-ccw" class="w-4 h-4 mr-2"></i> Return Produk (Sebagian)
                        </button>

                        <form action="{{ route('penjualan.cancel', $penjualan->id) }}" method="POST"
                            onsubmit="return confirm('Batalkan transaksi sepenuhnya? Semua stok akan dikembalikan ke gudang.')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-full mt-2">
                                <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Cancel Transaksi (Semua)
                            </button>
                        </form>
                    </div>
                @else
                    <div class="mt-5 p-3 bg-slate-100 rounded text-slate-500 text-xs italic text-center">
                        @if ($penjualan->total == 0 && $penjualan->status == 'berhasil')
                            {{-- Pesan khusus untuk transaksi Resend/Pengganti --}}
                            Dokumen ini adalah <b>INVOICE PENGGANTI (RESEND)</b>.
                            Tindakan return/cancel tidak tersedia untuk transaksi Rp 0.
                        @else
                            {{-- Pesan untuk status Cancel atau Return --}}
                            Transaksi ini sudah berstatus <b class="uppercase">{{ $penjualan->status }}</b> dan tidak dapat
                            diubah lagi.
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- KANAN: DAFTAR ITEM -->
        <div class="col-span-12 lg:col-span-8">
            <div class="box p-5 rounded-md">
                <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                    <div class="font-medium text-base truncate">Item Terjual (Aktif)</div>
                </div>
                <div class="overflow-auto">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">Nama Produk</th>
                                <th class="text-center whitespace-nowrap">Qty Awal</th>
                                <th class="text-center whitespace-nowrap">Qty Return</th>
                                <th class="text-center whitespace-nowrap">Qty Netto</th>
                                <th class="text-right whitespace-nowrap">Harga</th>
                                <th class="text-right whitespace-nowrap">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualan->detail as $item)
                                <tr>
                                    <td>{{ $item->produk->nama }}</td>
                                    <td class="text-center">{{ $item->qty + 0 }}</td> {{-- Qty Awal Beli --}}
                                    <td class="text-center text-danger">
                                        {{ $item->qty_return > 0 ? '-' . ($item->qty_return + 0) : '0' }}
                                    </td>
                                    <td class="text-center font-bold text-lg">
                                        {{ $item->qty - $item->qty_return + 0 }} {{-- Sisa yang masih di tangan pembeli --}}
                                    </td>
                                    <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50">
                                <td colspan="5" class="text-right font-bold uppercase">Total Tagihan Akhir</td>
                                <td class="text-right font-bold text-primary text-lg">Rp
                                    {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Info Detail Return Tambahan --}}
                {{-- Bagian Histori Dokumen Return --}}
                @if ($penjualan->returns->count() > 0)
                    <div class="mt-10 pt-6 border-t-2 border-dashed border-slate-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 rounded-full bg-danger/10 flex items-center justify-center mr-3">
                                <i data-lucide="rotate-ccw" class="w-5 h-5 text-danger"></i>
                            </div>
                            <div>
                                <div class="font-bold text-base text-slate-700">Histori Dokumen Return</div>
                                <div class="text-slate-500 text-xs">Daftar pengembalian barang untuk transaksi ini</div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            @foreach ($penjualan->returns as $ret)
                                <div class="box border border-slate-200 shadow-sm overflow-hidden">
                                    {{-- Header Dokumen --}}
                                    <div
                                        class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex justify-between items-center">
                                        <div>
                                            <span class="text-slate-500 text-xs uppercase font-semibold">Nomor
                                                Dokumen:</span>
                                            <a href="{{ route('penjualan.show_return', $ret->id) }}"
                                                class="font-bold text-primary underline decoration-dotted">
                                                {{ $ret->nomor_return }}
                                            </a>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-slate-500 text-xs uppercase font-semibold">Tanggal
                                                Return:</span>
                                            <div class="font-medium">
                                                {{ \Carbon\Carbon::parse($ret->tanggal)->format('d F Y') }}</div>
                                        </div>
                                    </div>

                                    {{-- Tabel Detail Return (Disamakan dengan Table Utama) --}}
                                    <div class="overflow-x-auto">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="whitespace-nowrap">Nama Produk</th>
                                                    <th class="text-center whitespace-nowrap">Qty Direturn</th>
                                                    <th class="text-right whitespace-nowrap">Harga Satuan</th>
                                                    <th class="text-right whitespace-nowrap">Total Potongan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($ret->detail as $rd)
                                                    <tr>
                                                        <td class="py-3">
                                                            <div class="font-medium whitespace-nowrap">
                                                                {{ $rd->produk->nama }}
                                                            </div>
                                                        </td>
                                                        <td class="text-center font-bold text-danger">
                                                            {{ $rd->qty + 0 }}
                                                        </td>
                                                        <td class="text-right text-slate-600">
                                                            Rp {{ number_format($rd->harga, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-right font-medium">
                                                            Rp {{ number_format($rd->subtotal, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-danger/5">
                                                    <td colspan="3" class="text-right font-bold uppercase text-danger">
                                                        Total Refund Dokumen Ini</td>
                                                    <td class="text-right font-bold text-danger text-lg">
                                                        Rp {{ number_format($ret->total_refund, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MODAL RETURN -->
    <div id="modal-return" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Pastikan Route sesuai dengan penamaan di web.php -->
                <form action="{{ route('penjualan.return', $penjualan->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Proses Pengembalian Barang (Return)</h2>
                    </div>
                    <div class="modal-body">
                        <!-- PERBAIKAN: Tambahkan Input Tanggal & Keterangan (Wajib bagi Controller) -->
                        <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">

                        <div class="alert alert-warning mb-4">
                            <div class="flex items-center">
                                <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                                <div>
                                    <p class="font-bold">Ketentuan Return:</p>
                                    <ul class="text-xs list-disc ml-4">
                                        <li><b>Qty Return</b> tidak boleh melebihi sisa barang yang ada.</li>
                                        <li>Jika <b>"Kembali Ke Stok"</b> dicentang, stok produk di gudang akan bertambah.
                                        </li>
                                        <li>Total tagihan invoice akan berkurang otomatis.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <table class="table table-sm">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th>Produk</th>
                                    <th class="text-center">Sisa Qty Jual</th>
                                    <th class="text-center w-32">Jumlah Return</th>
                                    <th class="text-center">Masuk Stok?</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penjualan->detail as $index => $item)
                                    @php
                                        $sisaBisaReturn = $item->qty - $item->qty_return;
                                    @endphp
                                    @if ($sisaBisaReturn > 0)
                                        <tr>
                                            <td class="py-3">
                                                <div class="font-medium text-primary">{{ $item->produk->nama }}</div>
                                                <div class="text-slate-500 text-xs">Harga: Rp
                                                    {{ number_format($item->harga, 0, ',', '.') }}</div>
                                            </td>
                                            <td class="text-center font-bold">{{ $sisaBisaReturn + 0 }}</td>
                                            <td>
                                                <input type="hidden" name="items[{{ $index }}][produk_id]"
                                                    value="{{ $item->produk_id }}">
                                                <input type="number" name="items[{{ $index }}][qty_return]"
                                                    class="form-control text-center input-qty-return" min="0"
                                                    max="{{ $sisaBisaReturn }}" value="0">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="items[{{ $index }}][kembalikan_stok]"
                                                    value="1" class="form-check-input">
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-24 mr-1">Batal</button>
                        <button type="submit" id="btnReturn" class="btn btn-primary" disabled>
                            Simpan & Update Tagihan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const qtyInputs = document.querySelectorAll('.input-qty-return');
            const btnSubmit = document.getElementById('btnReturn');

            function checkReturn() {
                let totalReturn = 0;
                qtyInputs.forEach(input => {
                    totalReturn += parseInt(input.value) || 0;
                });
                btnSubmit.disabled = totalReturn === 0;
            }

            qtyInputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Validasi jangan sampai melebihi max
                    const max = parseInt(this.getAttribute('max'));
                    if (parseInt(this.value) > max) this.value = max;
                    if (parseInt(this.value) < 0 || this.value === "") this.value = 0;

                    checkReturn();
                });
            });
        });
    </script>
@endsection
