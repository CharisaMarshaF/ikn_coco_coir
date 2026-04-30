@extends('layouts.app')

@section('content')

@if(session('error'))
<div class="alert alert-danger show mb-5 mt-5">{{ session('error') }}</div>
@endif

<form action="{{ route('produksi.store') }}" method="POST">
    @csrf
    <div class="intro-y grid grid-cols-12 gap-5 mt-5">
        <div class="col-span-12 lg:col-span-8">
            <div class="box p-5">
                {{-- Bagian Bahan Baku --}}
                <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                    <h2 class="font-medium text-base mr-auto text-primary">1. Bahan Baku (Dikeluarkan dari Stok)</h2>
                    <button type="button" onclick="addBahan()" class="btn btn-primary btn-sm">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Bahan
                    </button>
                </div>
                <div id="bahan-container" class="space-y-3">
                    {{-- Row Bahan akan muncul di sini --}}
                </div>

                {{-- Bagian Hasil Produk --}}
                <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5 mt-10">
                    <h2 class="font-medium text-base mr-auto text-success">2. Target Hasil Produk</h2>
                    <button type="button" onclick="addProduk()" class="btn btn-success text-white btn-sm">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Produk
                    </button>
                </div>
                <div id="produk-container" class="space-y-3">
                    {{-- Row Produk akan muncul di sini --}}
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="box p-5 sticky top-5">
                <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                    <h2 class="font-medium text-base mr-auto">Informasi Produksi</h2>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="form-label font-medium">Tanggal Mulai</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="alert alert-secondary show mb-2">
                        <div class="flex items-center">
                            <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                            <span class="text-xs font-medium">Status otomatis: <b>Sedang Proses</b>. Stok produk akan bertambah setelah status diubah menjadi <b>Berhasil</b> di halaman detail.</span>
                        </div>
                    </div>

                    <div>
                        <label class="form-label font-medium">Keterangan / Catatan</label>
                        <textarea name="keterangan" class="form-control" rows="4" placeholder="Contoh: Produksi batch pagi..."></textarea>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-200/60 dark:border-darkmode-400">
                    <button type="submit" class="btn btn-primary w-full shadow-md py-3">
                        <i data-lucide="play" class="w-4 h-4 mr-2"></i> Mulai Produksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Fungsi untuk cek duplikasi dan menampilkan stok
    function handleBahanChange(selectElement, id) {
        // 1. Cek Duplikasi
        const selectedValue = selectElement.value;
        if (!selectedValue) {
            document.getElementById(`stok-info-${id}`).innerText = "Stok: -";
            return;
        }

        const container = document.getElementById('bahan-container');
        const allSelects = container.querySelectorAll('select');
        let count = 0;
        allSelects.forEach(select => { if (select.value === selectedValue) count++; });

        if (count > 1) {
            alert('Bahan ini sudah dipilih!');
            selectElement.value = "";
            document.getElementById(`stok-info-${id}`).innerText = "Stok: -";
            return;
        }

        // 2. Update Info Stok
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const stok = selectedOption.getAttribute('data-stok');
        const satuan = selectedOption.getAttribute('data-satuan');
        
        document.getElementById(`stok-info-${id}`).innerText = `Tersedia: ${stok} ${satuan}`;
    }

    function checkDuplicationProduk(selectElement) {
        const selectedValue = selectElement.value;
        if (!selectedValue) return;

        const container = document.getElementById('produk-container');
        const allSelects = container.querySelectorAll('select');
        let count = 0;
        allSelects.forEach(select => { if (select.value === selectedValue) count++; });

        if (count > 1) {
            alert('Produk ini sudah dipilih!');
            selectElement.value = "";
        }
    }

    function addBahan() {
        const id = Date.now();
        const html = `
        <div class="flex items-start gap-4 pb-3 border-b border-dashed border-slate-200 dark:border-darkmode-400 last:border-0" id="bahan-row-${id}">
            <div class="flex-1">
                <select name="bahan_ids[]" class="form-control w-full" required onchange="handleBahanChange(this, '${id}')">
                    <option value="">Pilih Bahan...</option>
                    @foreach($bahan as $b)
                        <option value="{{ $b->id }}" data-stok="{{ $b->stok->jumlah ?? 0 }}" data-satuan="{{ $b->satuan }}">
                            {{ $b->nama }}
                        </option>
                    @endforeach
                </select>
                <div class="text-xs mt-1 text-slate-500 font-medium" id="stok-info-${id}">Tersedia: -</div>
            </div>
            <div class="w-28 md:w-36">
                <input type="number" step="0.01" name="bahan_qtys[]" class="form-control w-full" placeholder="Qty" required>
            </div>
            <button type="button" onclick="document.getElementById('bahan-row-${id}').remove()" class="btn btn-outline-danger px-3 py-1">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>`;
        document.getElementById('bahan-container').insertAdjacentHTML('beforeend', html);
        lucide.createIcons();
    }

    function addProduk() {
        const id = Date.now();
        const html = `
        <div class="flex items-center gap-4 pb-3 border-b border-dashed border-slate-200 dark:border-darkmode-400 last:border-0" id="produk-row-${id}">
            <div class="flex-1">
                <select name="produk_ids[]" class="form-control w-full" required onchange="checkDuplicationProduk(this)">
                    <option value="">Pilih Produk...</option>
                    @foreach($produk as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-28 md:w-36">
                <input type="number" step="0.01" name="produk_qtys[]" class="form-control w-full" placeholder="Hasil" required>
            </div>
            <button type="button" onclick="document.getElementById('produk-row-${id}').remove()" class="btn btn-outline-danger px-3 py-1">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>`;
        document.getElementById('produk-container').insertAdjacentHTML('beforeend', html);
        lucide.createIcons();
    }

    window.onload = () => {
        addBahan();
        addProduk();
    };
</script>
@endsection