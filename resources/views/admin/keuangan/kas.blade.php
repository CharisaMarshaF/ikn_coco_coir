@extends('layouts.app')

@section('content')
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Kas Harian</h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
            <button data-tw-toggle="modal" data-tw-target="#modal-tambah-kas" class="btn btn-primary shadow-md mr-2">Tambah Kas
                Manual</button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success show flex items-center mb-2 mt-5" role="alert">
            <i data-lucide="check-circle" class="w-6 h-6 mr-2"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="alert alert-danger show flex items-center mb-2 mt-5" role="alert">
            <i data-lucide="alert-octagon" class="w-6 h-6 mr-2"></i>
            @if (session('error'))
                {{ session('error') }}
            @else
                Cek kembali inputan Anda.
            @endif
        </div>
    @endif
<div class="grid grid-cols-12 gap-6 mt-5">
        <div class="col-span-12 sm:col-span-6 xl:col-span-6 intro-y">
            <div class="report-box zoom-in">
                <div class="box p-5">
                    <div class="flex"><i data-lucide="trending-up" class="report-box__icon text-success"></i></div>
                    <div class="text-3xl font-medium leading-8 mt-6">Rp {{ number_format($totalMasukBulanIni, 0, ',', '.') }}</div>
                    <div class="text-base text-slate-500 mt-1">Total Masuk (Bulan Ini)</div>
                </div>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-6 intro-y">
            <div class="report-box zoom-in">
                <div class="box p-5">
                    <div class="flex"><i data-lucide="trending-down" class="report-box__icon text-danger"></i></div>
                    <div class="text-3xl font-medium leading-8 mt-6">Rp {{ number_format($totalKeluarBulanIni, 0, ',', '.') }}</div>
                    <div class="text-base text-slate-500 mt-1">Total Keluar (Bulan Ini)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <form action="{{ route('kas.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="whitespace-nowrap">Dari:</label>
                    <input type="date" name="tgl_mulai" class="form-control box" value="{{ $tgl_mulai }}">
                </div>
                <div class="flex items-center gap-2">
                    <label class="whitespace-nowrap">Sampai:</label>
                    <input type="date" name="tgl_selesai" class="form-control box" value="{{ $tgl_selesai }}">
                </div>
                <button type="submit" class="btn btn-secondary shadow-md">Filter</button>
                
                <a href="{{ route('kas.pdf', ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai]) }}" 
                   target="_blank" class="btn btn-outline-danger shadow-md ml-2">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export PDF
                </a>
            </form>
        </div>
       

        <div class="intro-y col-span-12 p-5 bg-white rounded-lg shadow mt-5">
            <div class="overflow-x-auto">
                <table id="example1" class="table table-report table-report--bordered display w-full">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap w-10">NO</th>
                            <th class="whitespace-nowrap">WAKTU</th>
                            <th class="whitespace-nowrap">REKENING</th>
                            <th class="whitespace-nowrap">KETERANGAN</th>
                            <th class="text-center whitespace-nowrap">JENIS</th>
                            <th class="text-right whitespace-nowrap">NOMINAL</th>
                            <th class="text-center whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kas as $key => $k)
                            <tr class="intro-x">
                                <td class="text-center">{{ $key + 1 }}</td>

                                <td class="w-40">{{ \Carbon\Carbon::parse($k->tanggal)->format('d/m/Y') }}</td>

                                <td>
                                    <div class="font-medium whitespace-nowrap">{{ $k->rekening->nama ?? 'N/A' }}</div>
                                </td>

                                <td>
                                    <span class="text-slate-500 text-xs block">[{{ strtoupper($k->kategori) }}]</span>
                                    <div class="font-medium">{{ $k->keterangan ?? '-' }}</div>
                                </td>

                                <td class="text-center">
                                    @if ($k->jenis == 'masuk')
                                        <span class="text-success font-medium uppercase italic">Uang Masuk</span>
                                    @else
                                        <span class="text-danger font-medium uppercase italic">Uang Keluar</span>
                                    @endif
                                </td>

                                <td
                                    class="text-right font-bold {{ $k->jenis == 'masuk' ? 'text-success' : 'text-danger' }}">
                                    {{ $k->jenis == 'masuk' ? '+' : '-' }} Rp
                                    {{ number_format($k->total_nominal, 0, ',', '.') }}
                                </td>

                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#delete-confirmation-modal-{{ $k->id }}">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                        </a>
                                    </div>

                                    {{-- Modal Delete per baris --}}
                                    <div id="delete-confirmation-modal-{{ $k->id }}" class="modal" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-body p-0">
                                                    <form action="{{ route('kas.destroy', $k->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="p-5 text-center">
                                                            <i data-lucide="x-circle"
                                                                class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                                            <div class="text-3xl mt-5">Hapus Data Kas?</div>
                                                            <div class="text-slate-500 mt-2">
                                                                Apakah Anda yakin ingin menghapus data kas sebesar
                                                                <b class="text-danger">Rp
                                                                    {{ number_format($k->total_nominal, 0, ',', '.') }}</b>?
                                                                <br>Saldo pada rekening <b>{{ $k->rekening->nama }}</b>
                                                                akan dikembalikan secara otomatis.
                                                            </div>
                                                        </div>
                                                        <div class="px-5 pb-8 text-center">
                                                            <button type="button" data-tw-dismiss="modal"
                                                                class="btn btn-outline-secondary w-24 mr-1">Batal</button>
                                                            <button type="submit"
                                                                class="btn btn-danger w-24">Hapus</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Kas --}}
    <div id="modal-tambah-kas" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
                                @foreach ($rekenings as $rek)
                                    <option value="{{ $rek->id }}">{{ $rek->nama }} (Saldo: Rp
                                        {{ number_format($rek->saldo, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Jenis Kas</label>
                            <select name="jenis" class="form-select" required>
                                <option value="masuk">Uang Masuk</option>
                                <option value="keluar">Uang Keluar</option>
                            </select>
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" id="kategori_select" class="form-select"
                                onchange="toggleKategori(this.value)" required>
                                <option value="modal">Modal/Flat</option>
                                <option value="operasional">Operasional (Detail Item)</option>
                            </select>
                        </div>

                        {{-- Input Modal --}}
                        <div class="col-span-12" id="input-modal-container">
                            <label class="form-label">Nominal Total</label>
                            <input type="number" name="nominal" id="nominal_input" class="form-control"
                                placeholder="Contoh: 500000">
                        </div>

                        {{-- Input Operasional --}}
                        <div class="col-span-12 hidden" id="input-operasional-container">
                            <label class="form-label font-bold">Detail Item</label>
                            <div id="item-list">
                                <div class="grid grid-cols-12 gap-2 mb-2 item-row">
                                    <div class="col-span-5"><input type="text" name="items[0][nama_item]"
                                            class="form-control detail-item-input" placeholder="Nama Barang"></div>
                                    <div class="col-span-2"><input type="number" name="items[0][jumlah]"
                                            class="form-control detail-item-input" placeholder="Jml" value="1">
                                    </div>
                                    <div class="col-span-4"><input type="number" name="items[0][harga]"
                                            class="form-control detail-item-input" placeholder="Harga"></div>
                                    <div class="col-span-1"></div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary mt-2" onclick="addRow()">+ Tambah
                                Baris</button>
                        </div>

                        <div class="col-span-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                        <button type="submit" class="btn btn-primary w-20">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let rowIdx = 1;

        function toggleKategori(val) {
            const modalDiv = document.getElementById('input-modal-container');
            const operasionalDiv = document.getElementById('input-operasional-container');
            const nominalInput = document.getElementById('nominal_input');
            const detailInputs = document.querySelectorAll('.detail-item-input');

            if (val === 'operasional') {
                modalDiv.classList.add('hidden');
                operasionalDiv.classList.remove('hidden');
                nominalInput.required = false;
                // Set minimal baris pertama jadi required
                detailInputs.forEach(el => el.required = false); // Biarkan opsional atau atur sesuai kebutuhan
            } else {
                modalDiv.classList.remove('hidden');
                operasionalDiv.classList.add('hidden');
                nominalInput.required = true;
            }
        }

        function addRow() {
            let container = document.getElementById('item-list');
            let html = `
            <div class="grid grid-cols-12 gap-2 mb-2 item-row">
                <div class="col-span-5"><input type="text" name="items[${rowIdx}][nama_item]" class="form-control" placeholder="Nama Barang" required></div>
                <div class="col-span-2"><input type="number" name="items[${rowIdx}][jumlah]" class="form-control" placeholder="Jml" required></div>
                <div class="col-span-4"><input type="number" name="items[${rowIdx}][harga]" class="form-control" placeholder="Harga" required></div>
                <div class="col-span-1"><button type="button" class="btn btn-danger" onclick="this.closest('.item-row').remove()">X</button></div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
            rowIdx++;
        }
    </script>
@endsection
