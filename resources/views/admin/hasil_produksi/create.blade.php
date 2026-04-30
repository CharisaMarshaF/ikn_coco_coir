@extends('layouts.app')

@section('content')
    <style>
        .ts-wrapper .ts-control {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 0.75rem !important;
            background-color: #fff !important;
        }
        .dark .ts-wrapper .ts-control {
            background-color: #1b253b !important;
            border-color: #2d3748 !important;
        }
    </style>

    <form action="{{ route('hasil-produksi.store') }}" method="POST" id="formProduksi">
        @csrf
        <div class="intro-y grid grid-cols-12 gap-5 mt-5">
            <div class="col-span-12 lg:col-span-8">
                <!-- Header Info -->
                <div class="box p-5 grid grid-cols-12 gap-4 mb-5 border-t-4 border-success">
                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label font-bold text-success">Tanggal Produksi Selesai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label font-bold text-slate-600">Keterangan / Catatan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Misal: Shift 1">
                    </div>
                </div>

                <!-- Search -->
                <div class="intro-y flex flex-wrap sm:flex-nowrap items-center mb-4">
                    <div class="w-full flex-1">
                        <div class="relative text-slate-500">
                            <input type="text" id="searchProduk" onkeyup="filterProduk()"
                                class="form-control w-full box pr-10"
                                placeholder="Cari produk (Proses/Jadi)...">
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        </div>
                    </div>
                </div>

                <!-- List Produk -->
                <div class="grid grid-cols-12 gap-3 mt-5" id="produkList">
                    @foreach ($produkProses as $p)
                        @php $stokSekarang = $p->stok ? $p->stok->jumlah : 0; @endphp

                        <div class="item-produk col-span-6 sm:col-span-4 lg:col-span-3 box p-3 cursor-pointer zoom-in relative border-2 border-transparent hover:border-success"
                            data-nama="{{ strtolower($p->nama) }}"
                            onclick="addItem({{ $p->id }}, '{{ $p->nama }}', '{{ $p->satuan }}', {{ $stokSekarang }}, '{{ $p->jenis }}')">

                            <div class="absolute top-0 right-0 mr-1 mt-1 bg-success/10 text-success rounded-full p-0.5">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            </div>

                            <div class="font-bold text-sm text-slate-700 truncate pr-5" title="{{ $p->nama }}">
                                {{ $p->nama }}
                            </div>

                            <div class="flex justify-between items-center mt-1">
                                <div class="text-slate-500 text-[10px] italic">Sat: {{ $p->satuan }}</div>
                                <div class="text-[9px] px-1 bg-slate-100 rounded text-slate-600 uppercase">{{ $p->jenis }}</div>
                            </div>

                            <div class="mt-2 pt-2 border-t border-slate-100 flex justify-between items-center">
                                <div class="text-xs font-bold">
                                    Stok: <span class="text-primary">{{ (float) $stokSekarang }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sidebar / Keranjang -->
            <div class="col-span-12 lg:col-span-4">
                <div class="intro-y box p-5 sticky top-5 border-t-4 border-success">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                        <h2 class="font-medium text-base mr-auto text-success">Daftar Hasil Produksi</h2>
                        <i data-lucide="package" class="w-5 h-5 text-success"></i>
                    </div>

                    <div id="cart-items" class="overflow-y-auto max-h-[400px] pr-2">
                        <div class="text-center py-5 text-slate-400 italic" id="empty-cart">
                            Klik produk di kiri.
                        </div>
                    </div>

                    <div class="border-t border-slate-200/60 mt-5 pt-5 space-y-3">
                        <div class="flex items-center">
                            <div class="mr-auto text-slate-500 text-sm italic">Total Item Produksi</div>
                            <div class="font-bold text-xl text-success" id="total-items">0</div>
                        </div>

                        <button type="submit" class="btn btn-success w-full shadow-md py-3 mt-4 text-white">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Hasil Produksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

   <script>
    let cart = [];

    function addItem(id, name, unit, stock, type) {
        const existing = cart.find(i => i.produk_id === id);
        if (existing) {
            existing.qty++;
        } else {
            cart.push({
                produk_id: id,
                nama: name,
                satuan: unit,
                qty: 1,
                jenis: type,
                // PERBAIKAN: Berikan default value yang valid untuk semua jenis
                kategori_pola: (type === 'proses') ? 'Bulat' : 'Jadi' 
            });
        }
        renderCart();
    }

    function updateItem(index, value) {
        let val = parseFloat(value) || 0;
        if (val < 0) val = 0;
        cart[index].qty = val;
        // Tidak perlu render ulang, cukup update total
        updateTotalDisplay();
    }

    function updatePola(index, value) {
        // Update data di array agar sinkron saat form disubmit
        cart[index].kategori_pola = value;
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function updateTotalDisplay() {
        const total = cart.reduce((sum, item) => sum + item.qty, 0);
        document.getElementById('total-items').innerText = total;
    }

    function renderCart() {
        let html = '';
        const container = document.getElementById('cart-items');

        if (cart.length === 0) {
            container.innerHTML = `<div class="text-center py-5 text-slate-400 italic" id="empty-cart">Klik produk di kiri.</div>`;
            updateTotalDisplay();
            return;
        }

        cart.forEach((item, index) => {
            let polaHtml = '';
            
            if (item.jenis === 'proses') {
                polaHtml = `
                <div class="mt-2">
                    <label class="text-[10px] uppercase font-bold text-success">Kategori Pola</label>
                    <select name="items[${index}][kategori_pola]" 
                            class="form-select form-select-sm mt-1" 
                            onchange="updatePola(${index}, this.value)">
                        <option value="Bulat" ${item.kategori_pola === 'Bulat' ? 'selected' : ''}>Bulat</option>
                        <option value="Setengah_jadi" ${item.kategori_pola === 'Setengah_jadi' ? 'selected' : ''}>Setengah Jadi</option>
                        <option value="Jadi" ${item.kategori_pola === 'Jadi' ? 'selected' : ''}>Jadi</option>
                    </select>
                </div>`;
            } else {
                // PERBAIKAN: Jangan kirim string kosong. Kirim nilai 'Jadi' atau sesuai Enum database 
                // agar kolom tidak NULL di MySQL.
                polaHtml = `<input type="hidden" name="items[${index}][kategori_pola]" value="Jadi">`;
            }

            html += `
            <div class="mb-4 bg-slate-50 p-3 rounded-lg border border-slate-200 intro-x">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <span class="font-bold text-slate-700">${item.nama}</span>
                        <span class="ml-1 text-[8px] px-1 bg-slate-200 rounded text-slate-500 uppercase">${item.jenis}</span>
                    </div>
                    <button type="button" onclick="removeItem(${index})" class="text-danger ml-2">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <input type="hidden" name="items[${index}][produk_id]" value="${item.produk_id}">
                
                <div>
                    <label class="text-[10px] uppercase font-bold text-slate-500">Jumlah Produksi (${item.satuan})</label>
                    <div class="input-group mt-1">
                        <input type="number" name="items[${index}][qty]" value="${item.qty}" 
                               min="0.01" step="any"
                               oninput="updateItem(${index}, this.value)" class="form-control form-control-sm">
                        <div class="input-group-text text-xs">${item.satuan}</div>
                    </div>
                </div>
                ${polaHtml}
            </div>`;
        });

        container.innerHTML = html;
        updateTotalDisplay();
        lucide.createIcons();
    }

    function filterProduk() {
        let input = document.getElementById("searchProduk").value.toLowerCase();
        let items = document.querySelectorAll(".item-produk");
        items.forEach(item => {
            let nama = item.getAttribute("data-nama");
            item.style.display = nama.includes(input) ? "" : "none";
        });
    }

    document.getElementById('formProduksi').onsubmit = function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Daftar produksi masih kosong!');
        }
    };
</script>
@endsection