@extends('layouts.app')

@section('content')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Input Produksi Baru</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('produksi.index') }}" class="btn btn-outline-secondary shadow-md">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger show mb-5 mt-5">{{ session('error') }}</div>
@endif

<form action="{{ route('produksi.store') }}" method="POST">
    @csrf
    <div class="intro-y grid grid-cols-12 gap-5 mt-5">
        <div class="col-span-12 lg:col-span-8">
            <div class="box p-5">
                <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                    <h2 class="font-medium text-base mr-auto text-primary">1. Bahan Baku</h2>
                    <button type="button" onclick="addBahan()" class="btn btn-primary btn-sm">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Bahan
                    </button>
                </div>
                <div id="bahan-container" class="space-y-3">
                    </div>

                <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5 mt-10">
                    <h2 class="font-medium text-base mr-auto text-success">2. Hasil Produk</h2>
                    <button type="button" onclick="addProduk()" class="btn btn-success text-white btn-sm">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Produk
                    </button>
                </div>
                <div id="produk-container" class="space-y-3">
                    </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="box p-5 sticky top-5">
                <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                    <h2 class="font-medium text-base mr-auto">Konfirmasi Produksi</h2>
                    <i data-lucide="check-square" class="w-5 h-5 text-slate-500"></i>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="form-label font-medium">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div>
                        <label class="form-label font-medium">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="proses">Sedang Proses</option>
                            <option value="berhasil" selected>Berhasil (Update Stok)</option>
                            <option value="reject">Reject / Gagal</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label font-medium">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="4" placeholder="Catatan produksi..."></textarea>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-200/60 dark:border-darkmode-400">
                    <button type="submit" class="btn btn-primary w-full shadow-md py-3">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Produksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function addBahan() {
        const id = Date.now();
        const html = `
        <div class="flex items-center gap-4 pb-3 border-b border-dashed border-slate-200 dark:border-darkmode-400 last:border-0" id="bahan-row-${id}">
            <div class="flex-1">
                <select name="bahan_ids[]" class="tom-select form-control w-full" required>
                    <option value="">Pilih Bahan...</option>
                    @foreach($bahan as $b)
                        <option value="{{ $b->id }}">{{ $b->nama }} </option>
                    @endforeach
                </select>
            </div>
            <div class="w-28 md:w-36">
                <input type="number" step="0.01" name="bahan_qtys[]" class="form-control w-full" placeholder="Qty" required>
            </div>
            <button type="button" onclick="document.getElementById('bahan-row-${id}').remove()" class="btn btn-outline-danger px-3 py-1">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>`;
        
        document.getElementById('bahan-container').insertAdjacentHTML('beforeend', html);
        
        // Langsung targetkan element ID yang baru dibuat
        const newSelect = document.querySelector(`#bahan-row-${id} .tom-select`);

        
        lucide.createIcons();
    }

    function addProduk() {
        const id = Date.now();
        const html = `
        <div class="flex items-center gap-4 pb-3 border-b border-dashed border-slate-200 dark:border-darkmode-400 last:border-0" id="produk-row-${id}">
            <div class="flex-1">
                <select name="produk_ids[]" class="tom-select form-control w-full" required>
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
        
        // Langsung targetkan element ID yang baru dibuat
        const newSelect = document.querySelector(`#produk-row-${id} .tom-select`);

        
        lucide.createIcons();
    }

    window.onload = () => {
        addBahan();
        addProduk();
    };
</script>
@endsection