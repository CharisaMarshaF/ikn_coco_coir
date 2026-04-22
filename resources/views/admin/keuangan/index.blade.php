@extends('layouts.app')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Laporan & Arus Keuangan</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0 gap-2">
        <form action="{{ route('keuangan.index') }}" method="GET" class="flex gap-2">
            <input type="date" name="tanggal" class="form-control box" value="{{ $tanggal }}" onchange="this.form.submit()">
        </form>
        {{-- <button data-tw-toggle="modal" data-tw-target="#modal-transaksi" class="btn btn-primary shadow-md">Record Transaksi</button>
        <button data-tw-toggle="modal" data-tw-target="#modal-kas" class="btn btn-secondary shadow-md">Record Kas</button> --}}
    </div>
</div>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="col-span-12 sm:col-span-6 intro-y">
        <div class="box p-5 flex items-center zoom-in">
            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                <i data-lucide="landmark" class="w-5 h-5"></i>
            </div>
            <div class="ml-4">
                <div class="text-slate-500 text-xs">Net Saldo Bank (Hari Ini)</div>
                <div class="text-lg font-medium">Rp {{ number_format($totalBank, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 intro-y">
        <div class="box p-5 flex items-center zoom-in">
            <div class="w-10 h-10 rounded-full bg-success/10 text-success flex items-center justify-center">
                <i data-lucide="wallet" class="w-5 h-5"></i>
            </div>
            <div class="ml-4">
                <div class="text-slate-500 text-xs">Net Saldo Tunai (Hari Ini)</div>
                <div class="text-lg font-medium">Rp {{ number_format($totalTunai, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="intro-y box mt-5">
    <div class="p-5">
        <ul class="nav nav-link-tabs flex-col sm:flex-row justify-center lg:justify-start text-center" role="tablist">
            <li id="bank-tab" class="nav-item" role="presentation">
                <button class="nav-link py-4 flex items-center active" data-tw-target="#bank-content" aria-controls="bank-content" aria-selected="true" role="tab">
                    <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i> Transaksi Rekening/Bank
                </button>
            </li>
            <li id="tunai-tab" class="nav-item" role="presentation">
                <button class="nav-link py-4 flex items-center" data-tw-target="#tunai-content" aria-controls="tunai-content" aria-selected="false" role="tab">
                    <i data-lucide="coins" class="w-4 h-4 mr-2"></i> Kas Harian (Tunai)
                </button>
            </li>
        </ul>

        <div class="tab-content mt-5">
            <div id="bank-content" class="tab-pane active" role="tabpanel" aria-labelledby="bank-tab">
                <div class="overflow-x-auto">
                    <table class="table table-report table-striped">
                        <thead>
                            <tr>
                                <th>REKENING</th>
                                <th>SUMBER / REF</th>
                                <th class="text-center">JENIS</th>
                                <th class="text-right">NOMINAL</th>
                                <th>KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksi as $t)
                            <tr class="intro-x">
                                <td class="font-medium">{{ $t->rekening->nama }}</td>
                                <td>{{ $t->sumber }}</td>
                                <td class="text-center">
                                    <span class="{{ $t->jenis == 'masuk' ? 'text-success' : 'text-danger' }} font-bold uppercase text-xs">
                                        {{ $t->jenis }}
                                    </span>
                                </td>
                                <td class="text-right font-medium">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                <td class="text-slate-500 text-xs">{{ $t->keterangan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center italic text-slate-400">Belum ada transaksi bank hari ini</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tunai-content" class="tab-pane" role="tabpanel" aria-labelledby="tunai-tab">
                <div class="overflow-x-auto">
                    <table class="table table-report table-striped">
                        <thead>
                            <tr>
                                <th>WAKTU</th>
                                <th class="text-center">JENIS</th>
                                <th class="text-right">NOMINAL</th>
                                <th>KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kas as $k)
                            <tr class="intro-x">
                                <td>{{ $k->tanggal->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <span class="{{ $k->jenis == 'masuk' ? 'bg-success/20 text-success' : 'bg-danger/20 text-danger' }} px-2 py-1 rounded text-xs">
                                        {{ strtoupper($k->jenis) }}
                                    </span>
                                </td>
                                <td class="text-right font-medium">Rp {{ number_format($k->nominal, 0, ',', '.') }}</td>
                                <td>{{ $k->keterangan }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center italic text-slate-400">Belum ada kas tunai hari ini</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-transaksi" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('keuangan.storeTransaksi') }}" method="POST">
                @csrf
                <div class="modal-header"><h2 class="font-medium text-base mr-auto">Input Transaksi Bank</h2></div>
                <div class="modal-body grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <label class="form-label">Rekening</label>
                        <select name="rekening_id" class="form-select">
                            @foreach($rekening as $r)
                                <option value="{{ $r->id }}">{{ $r->nama_bank }} - {{ $r->nomor_rekening }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <div class="col-span-6">
                        <label class="form-label">Jenis</label>
                        <select name="jenis" class="form-select">
                            <option value="masuk">Masuk</option>
                            <option value="keluar">Keluar</option>
                        </select>
                    </div>
                    <div class="col-span-6">
                        <label class="form-label">Nominal</label>
                        <input type="number" name="nominal" class="form-control" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Sumber (Contoh: Transfer Bank, Piutang)</label>
                        <input type="text" name="sumber" class="form-control" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<div id="modal-kas" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('keuangan.storeKas') }}" method="POST">
                @csrf
                <div class="modal-header"><h2 class="font-medium text-base mr-auto">Input Kas Tunai</h2></div>
                <div class="modal-body grid grid-cols-12 gap-4">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <div class="col-span-6">
                        <label class="form-label">Jenis</label>
                        <select name="jenis" class="form-select">
                            <option value="masuk">Masuk</option>
                            <option value="keluar">Keluar</option>
                        </select>
                    </div>
                    <div class="col-span-6">
                        <label class="form-label">Nominal</label>
                        <input type="number" name="nominal" class="form-control" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Keterangan (Contoh: Bayar Parkir, Uang Makan)</label>
                        <textarea name="keterangan" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection