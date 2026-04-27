@extends('layouts.app')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Kas Harian</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button data-tw-toggle="modal" data-tw-target="#modal-tambah-kas" class="btn btn-primary shadow-md mr-2">Tambah Kas Manual</button>
    </div>
</div>

{{-- Alert Error jika ada --}}
@if(session('error'))
<div class="alert alert-danger show flex items-center mb-2 mt-5" role="alert"> 
    <i data-lucide="alert-octagon" class="w-6 h-6 mr-2"></i> {{ session('error') }} 
</div>
@endif

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-lucide="trending-up" class="report-box__icon text-success"></i> 
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
                <div class="text-base text-slate-500 mt-1">Total Masuk (Hari Ini)</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5">
                <div class="flex">
                    <i data-lucide="trending-down" class="report-box__icon text-danger"></i> 
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
                <div class="text-base text-slate-500 mt-1">Total Keluar (Hari Ini)</div>
            </div>
        </div>
    </div>
    <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
        <div class="report-box zoom-in">
            <div class="box p-5 {{ $saldoHariIni < 0 ? 'bg-danger text-white' : 'bg-primary text-white' }}">
                <div class="flex">
                    <i data-lucide="wallet" class="report-box__icon text-white"></i> 
                </div>
                <div class="text-3xl font-medium leading-8 mt-6">Rp {{ number_format($saldoHariIni, 0, ',', '.') }}</div>
                <div class="text-base opacity-70 mt-1">Net Cashflow Hari Ini</div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-12 gap-6 mt-5">
    <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        <form action="{{ route('kas.index') }}" method="GET" class="flex items-center gap-2">
            <input type="date" name="tanggal" class="form-control box" value="{{ $tanggal }}" onchange="this.form.submit()">
            <label class="ml-2 text-slate-500 italic text-xs">* Menampilkan data berdasarkan tanggal</label>
        </form>
    </div>

    <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow mt-5">
        <div class="preview">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display datatable w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap w-10">NO</th>
                            <th class="whitespace-nowrap">WAKTU</th>
                            <th class="whitespace-nowrap">REKENING</th>
                            <th class="whitespace-nowrap">KETERANGAN</th>
                            <th class="text-center whitespace-nowrap">JENIS KAS </th>
                            <th class="text-right whitespace-nowrap">NOMINAL</th>
                            <th class="text-center whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kas as $key => $k)
                        <tr class="intro-x">
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td class="w-40">{{ \Carbon\Carbon::parse($k->tanggal)->format('d/m/Y') }} </td>
                            <td>
                                <span class="text-slate-500 text-xs block">Sumber/Tujuan:</span>
                                <div class="font-medium whitespace-nowrap">{{ $k->rekening->nama ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $k->keterangan }}</div>
                            </td>
                            <td class="text-">
                                @if($k->jenis == 'masuk')
                                    <span class="text-success font-medium uppercase italic">Uang Masuk</span>
                                @else
                                    <span class="text-danger font-medium uppercase italic">Uang Keluar</span>
                                @endif
                            </td>
                            <td class="text- font-bold {{ $k->jenis == 'masuk' ? 'text-success' : 'text-danger' }}">
                                {{ $k->jenis == 'masuk' ? '+' : '-' }} Rp {{ number_format($k->nominal, 0, ',', '.') }}
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify- items-center">
                                    <form action="{{ route('kas.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus record kas ini? Saldo rekening akan dikembalikan otomatis.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="flex items-center text-danger"> 
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete 
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        {{-- Data kosong ditangani secara otomatis oleh DataTables jika menggunakan ID example1 --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Kas --}}
<div id="modal-tambah-kas" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kas.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Input Kas Manual</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <label class="form-label">Pilih Rekening</label>
                        <select name="rekening_id" class="form-select" required>
                            <option value="">-- Pilih Rekening --</option>
                            @foreach($rekenings as $rek)
                                <option value="{{ $rek->id }}">{{ $rek->nama }} (Saldo: Rp {{ number_format($rek->saldo,0,',','.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Jenis Kas</label>
                        <select name="jenis" class="form-select" required>
                            <option value="masuk">Uang Masuk (Pemasukan Lainnya)</option>
                            <option value="keluar">Uang Keluar (Beban/Biaya/Operasional)</option>
                        </select>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Nominal</label>
                        <input type="number" name="nominal" class="form-control" placeholder="0" required>
                    </div>
                    <div class="col-span-12">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Bayar Listrik, Pemasukan Service, dll" required></textarea>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="submit" class="btn btn-primary w-20">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection