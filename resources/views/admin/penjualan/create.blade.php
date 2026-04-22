@extends('layouts.app')

@section('content')
<style>
    /* Style TomSelect agar rapi tanpa mematikan fungsi klik */
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

<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Input Penjualan Baru</h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary shadow-md">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
        </a>
    </div>
</div>

<form action="{{ route('penjualan.store') }}" method="POST">
    @csrf
    <div class="intro-y grid grid-cols-12 gap-5 mt-5">
        <div class="col-span-12 lg:col-span-8">
            <div class="box p-5 grid grid-cols-12 gap-4 mb-5">
                <div class="col-span-12 sm:col-span-6">
                    <label class="form-label font-bold text-primary">Client / Pembeli</label>
                    <select name="client_id" class="tom-select w-full">
                        <option value="">-- Pembeli Umum (Anonim) --</option>
                        @foreach($clients as $c)
                        <option value="{{ $c->id }}">{{ $c->nama }} ({{ $c->perusahaan ?? 'Personal' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="form-label font-bold">Tanggal Penjualan</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="intro-y flex flex-wrap sm:flex-nowrap items-center mb-4">
                <div class="w-full sm:w-auto">
                    <div class="w-56 relative text-slate-500">
                        <input type="text" id="searchProduk" onkeyup="filterProduk()" class="form-control w-56 box pr-10" placeholder="Cari produk...">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i> 
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-5 mt-5" id="produkList">
                @foreach($produk as $p)
                @php $stokSekarang = $p->stok ? $p->stok->jumlah : 0; @endphp
                <div class="item-produk col-span-12 sm:col-span-4 box p-5 cursor-pointer zoom-in" 
                    data-nama="{{ strtolower($p->nama) }}"
                    onclick="addItem({{ $p->id }}, '{{ $p->nama }}', {{ $p->harga_default }}, {{ $stokSekarang }})">
                    <div class="font-medium text-base">{{ $p->nama }}</div>
                    <div class="text-slate-500 text-xs">Stok: {{ $stokSekarang }} {{ $p->satuan }}</div>
                    <div class="text-primary font-bold mt-2">Rp {{ number_format($p->harga_default, 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="intro-y box p-5 sticky top-5">
                <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                    <h2 class="font-medium text-base mr-auto">Keranjang Belanja</h2>
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-slate-500"></i>
                </div>
                
                <div id="cart-items" class="overflow-y-auto max-h-[400px] pr-2">
                    <div class="text-center py-5 text-slate-400 italic" id="empty-cart">
                        Belum ada produk dipilih.
                    </div>
                </div>

                <div class="border-t border-slate-200/60 mt-5 pt-5 space-y-3">
                    <div class="flex items-center">
                        <div class="mr-auto text-slate-500 text-base">Total Tagihan</div>
                        <div class="font-bold text-2xl text-primary" id="grand-total">Rp 0</div>
                        <input type="hidden" name="total" id="input-grand-total" value="0">
                    </div>

                    <input type="hidden" name="status" value="berhasil">

                    <button type="submit" class="btn btn-primary w-full shadow-md py-3 mt-4">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Simpan Penjualan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    let cart = [];

    function addItem(id, name, defaultPrice, stock) {
        if(stock <= 0) {
            alert('Stok produk habis!');
            return;
        }

        const existing = cart.find(i => i.produk_id === id);
        if (existing) {
            if(existing.qty < stock) {
                existing.qty++;
            } else {
                alert('Maksimal stok tercapai');
            }
        } else {
            // Awalnya menggunakan harga_default, tapi bisa diubah nanti di updateItem
            cart.push({ 
                produk_id: id, 
                nama: name, 
                qty: 1, 
                harga: defaultPrice, 
                stok_max: stock 
            });
        }
        renderCart();
    }

    function updateItem(index, field, value) {
        let val = parseFloat(value) || 0;
        
        if(field === 'qty') {
            if(val > cart[index].stok_max) {
                alert('Stok tidak mencukupi (Maks: ' + cart[index].stok_max + ')');
                val = cart[index].stok_max;
            }
            if(val < 0.01) val = 1;
        }
        
        cart[index][field] = val;
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function renderCart() {
        let html = '';
        let grandTotal = 0;
        const container = document.getElementById('cart-items');

        if (cart.length === 0) {
            container.innerHTML = `<div class="text-center py-5 text-slate-400 italic" id="empty-cart">Belum ada produk dipilih.</div>`;
            document.getElementById('grand-total').innerText = 'Rp 0';
            document.getElementById('input-grand-total').value = 0;
            return;
        }

        cart.forEach((item, index) => {
            let subtotal = item.qty * item.harga;
            grandTotal += subtotal;
            
            html += `
            <div class="mb-4 bg-slate-50 p-3 rounded-lg  border-slate-200 intro-x">
                <div class="flex justify-between items-center mb-2">
    <span class="font-bold text-slate-700">${item.nama}</span>
    
    <button type="button" onclick="removeItem(${index})" class="text-danger ml-2">
        <i data-lucide="trash-2" class="w-4 h-4"></i>
    </button>
</div>
                <input type="hidden" name="items[${index}][produk_id]" value="${item.produk_id}">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Jumlah (Qty)</label>
                        <input type="number" name="items[${index}][qty]" value="${item.qty}" 
                               min="0.01" step="0.01" max="${item.stok_max}"
                               onchange="updateItem(${index}, 'qty', this.value)" class="form-control form-control-sm">
                    </div>
                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-500">Harga Satuan</label>
                        <input type="number" name="items[${index}][harga]" value="${item.harga}" 
                               onchange="updateItem(${index}, 'harga', this.value)" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="text-right text-xs mt-2 font-bold text-primary">Subtotal: Rp ${subtotal.toLocaleString('id-ID')}</div>
            </div>`;
        });

        container.innerHTML = html;
        document.getElementById('grand-total').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        document.getElementById('input-grand-total').value = grandTotal;
        lucide.createIcons(); // Re-render icon lucide setelah HTML berubah
    }

    function filterProduk() {
        let input = document.getElementById("searchProduk").value.toLowerCase();
        let items = document.querySelectorAll(".item-produk");
        items.forEach(item => {
            let nama = item.getAttribute("data-nama");
            item.style.display = nama.includes(input) ? "" : "none";
        });
    }
</script>
@endsection