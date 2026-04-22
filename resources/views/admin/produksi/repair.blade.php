@extends('layouts.app')

@section('content')
<div class="intro-y flex items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Perbaiki Produksi (Repair)</h2>
    <a href="{{ route('produksi.show', $produksi->id) }}" class="btn btn-outline-secondary w-24">Batal</a>
</div>

<form action="{{ route('produksi.repair-store', $produksi->id) }}" method="POST">
    @csrf
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="col-span-12 lg:col-span-4 space-y-5">
            <div class="box p-5">
                <h3 class="font-medium text-base mb-4 border-b pb-2 text-warning flex items-center">
                    <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i> Detail Gagal
                </h3>
                <div class="text-sm space-y-2 mb-5">
                    <div class="flex justify-between text-slate-500"><span>Kode Produksi:</span> <b class="text-slate-700">#PRD-{{ $produksi->id }}</b></div>
                    <div class="flex justify-between text-slate-500"><span>Tanggal Gagal:</span> <b class="text-slate-700">{{ \Carbon\Carbon::parse($produksi->tanggal)->format('d M Y') }}</b></div>
                </div>
                
                <div class="mt-5">
                    <label class="form-label font-bold text-slate-700">Tanggal Penyelesaian Baru</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                
                <div class="mt-4">
                    <label class="form-label font-bold text-slate-700">Catatan Perbaikan</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Jelaskan alasan penambahan bahan..." required></textarea>
                </div>
            </div>

            <div class="box p-5 bg-success/5 border border-success/10">
                <h3 class="font-medium text-base mb-4 text-success flex items-center">
                    <i data-lucide="layers" class="w-4 h-4 mr-2"></i> Target Produk
                </h3>
                @foreach($produksi->detail->where('jenis', 'produk') as $p)
                <div class="flex justify-between items-center bg-white p-3 rounded border border-success/20 mb-2 shadow-sm">
                    <span class="text-sm font-medium text-slate-700">{{ $p->produk->nama }}</span>
                    <span class="badge bg-success text-white px-3 py-1 rounded-full text-xs font-bold ml-2">
                        {{ $p->qty }} Item
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="col-span-12 lg:col-span-8 space-y-5">
            <div class="box p-5">
                <h3 class="font-medium text-base mb-4 border-b pb-2 text-danger flex items-center">
                    <i data-lucide="archive" class="w-4 h-4 mr-2"></i> Bahan Baku Terpakai (Sebelumnya)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($produksi->detail->where('jenis', 'bahan') as $b)
                    <div class="flex items-center p-3 border border-slate-200 rounded-lg bg-slate-50">
                        <div class="mr-auto"> <div class="text-sm font-bold text-slate-700">{{ $b->bahan->nama }}</div>
                            <div class="text-xs text-slate-500 italic">{{ $b->bahan->satuan }}</div>
                        </div>
                        <div class="text-right ml-4">
                            <span class="text-danger font-black text-lg">- {{ $b->qty }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 p-2 bg-slate-100 rounded text-xs text-slate-500 italic flex items-center">
                    <i data-lucide="info" class="w-3 h-3 mr-2"></i> Bahan di atas sudah dikurangi dari stok sebelumnya.
                </div>
            </div>

            <div class="box p-5 border-2 border-dashed border-primary/20 shadow-sm">
                <div class="flex items-center mb-6 border-b pb-4">
    <div>
        <h3 class="font-medium text-base text-primary flex items-center">
            <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
            Tambah Bahan Ekstra (Repair)
        </h3>
    </div>

    <div class="ml-auto">
        <button type="button" onclick="addBahanRepair()" class="btn btn-primary shadow-md">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Tambah Item
        </button>
    </div>
</div>

                <div id="repair-bahan-container" class="space-y-4">
                    <div class="text-center py-10 text-slate-400 italic" id="empty-state">
                        <i data-lucide="package" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                        <p>Belum ada bahan tambahan.</p>
                    </div>
                </div>

                <div class="mt-10 pt-5 border-t border-slate-200 flex justify-end gap-3">
                    <button type="submit" class="btn btn-warning text-white px-10 py-3 shadow-md font-bold">
                        <i data-lucide="check-square" class="w-4 h-4 mr-2"></i> Simpan & Selesaikan Produksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
function addBahanRepair() {
    const emptyState = document.getElementById('empty-state');
    if (emptyState) emptyState.remove();

    const id = Date.now();

    const html = `
    <div id="row-${id}"
        class="flex items-end gap-4 p-3 mb-3 border border-slate-200 rounded-lg bg-white shadow-sm hover:shadow-md transition">

        <!-- Bahan -->
        <div class="flex-1">
            <label class="text-xs text-slate-500 mb-1 block">Bahan Baku</label>

            <select name="bahan_ids[]" class="form-control h-10 w-full" required>
                <option value="">-- Pilih Bahan --</option>
                @foreach($bahan as $b)
                    <option value="{{ $b->id }}">
                        {{ $b->nama }} (Stok: {{ $b->stok }} {{ $b->satuan }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Qty -->
        <div class="w-28 md:w-36">
            <label class="text-xs text-slate-500 mb-1 block">Jumlah</label>

            <input type="number"
                step="0.01"
                name="bahan_qtys[]"
                class="form-control h-10 w-full text-right font-semibold text-danger"
                placeholder="0.00"
                required>
        </div>

        <!-- Delete -->
        <div>
            <button type="button"
                onclick="document.getElementById('row-${id}').remove()"
                class="btn btn-outline-danger w-10 h-10 flex items-center justify-center self-end">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>

    </div>`;

    document.getElementById('repair-bahan-container').insertAdjacentHTML('beforeend', html);

    setTimeout(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 0);
}
</script>
@endsection