@extends('layouts.app')

@section('content')
<div class="intro-y flex items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Form Pengambilan Bahan Baku</h2>
</div>

{{-- Tampilkan Error --}}
@if(session('error'))
<div class="alert alert-danger show flex items-center mb-5 mt-5">
    <i data-lucide="alert-octagon" class="w-6 h-6 mr-2"></i> {{ session('error') }}
</div>
@endif

<form action="{{ route('pengambilan.store') }}" method="POST">
    @csrf
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="col-span-12 lg:col-span-8">
            <div class="intro-y box p-5">
                <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                    <h2 class="font-medium text-base mr-auto text-primary">Daftar Bahan yang Diambil</h2>
                    <button type="button" onclick="addRow()" class="btn btn-primary btn-sm">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Item
                    </button>
                </div>
                
                <div id="item-container" class="space-y-4">
                    {{-- Row akan diisi oleh JS --}}
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="intro-y box p-5 sticky top-5">
                <div class="space-y-4">
                    <div>
                        <label class="form-label font-medium">Tanggal Pengambilan</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div>
                        <label class="form-label font-medium">Kategori Pola</label>
                        <select name="kategori_pola" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="bulat">Bulat</option>
                            <option value="set jadi">Set Jadi</option>
                            <option value="jadi">Jadi</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label font-medium">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional..."></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="btn btn-primary w-full shadow-md py-3">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Pengambilan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function addRow() {
        const id = Date.now();
        const html = `
        <div class="flex items-start gap-4 pb-4 border-b border-dashed border-slate-200 last:border-0" id="row-${id}">
            <div class="flex-1">
                <select name="bahan_ids[]" class="form-control bahan-select" required onchange="handleBahanChange(this, '${id}')">
                    <option value="">Pilih Bahan...</option>
                    @foreach($bahanBaku as $b)
                        <option value="{{ $b->id }}" data-stok="{{ $b->stok->jumlah ?? 0 }}" data-satuan="{{ $b->satuan }}">
                            {{ $b->nama }}
                        </option>
                    @endforeach
                </select>
                <div class="text-xs mt-1 text-slate-500 font-medium" id="info-stok-${id}">Stok Tersedia: -</div>
            </div>
            <div class="w-32">
                <input type="number" step="0.01" name="qtys[]" class="form-control" placeholder="Qty" required min="0.01">
            </div>
            <button type="button" onclick="document.getElementById('row-${id}').remove()" class="btn btn-outline-danger px-2">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>`;
        
        document.getElementById('item-container').insertAdjacentHTML('beforeend', html);
        lucide.createIcons();
    }

    function handleBahanChange(select, id) {
        const val = select.value;
        if(!val) return;

        // VALIDASI JS: Cek apakah bahan sudah dipilih di row lain
        let duplicate = false;
        const allSelects = document.querySelectorAll('.bahan-select');
        
        allSelects.forEach(s => {
            if (s !== select && s.value === val) {
                duplicate = true;
            }
        });

        if (duplicate) {
            alert("Bahan ini sudah ada di daftar! Silakan tambah Qty di baris yang sudah ada.");
            select.value = ""; // Reset pilihan
            document.getElementById(`info-stok-${id}`).innerText = "Stok Tersedia: -";
            return;
        }

        // Update Info Stok
        const option = select.options[select.selectedIndex];
        const stok = option.getAttribute('data-stok') || 0;
        const satuan = option.getAttribute('data-satuan') || '';
        document.getElementById(`info-stok-${id}`).innerText = `Stok Tersedia: ${stok} ${satuan}`;
    }

    window.onload = addRow;
</script>
@endsection