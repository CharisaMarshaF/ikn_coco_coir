@extends('layouts.app')

@section('content')

<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">Input Pembelian Bahan</h2>
</div>
@if(session('error'))
<div class="alert alert-danger-soft show flex items-center mb-5" role="alert">
    <i data-lucide="alert-octagon" class="w-6 h-6 mr-2"></i> 
    {{ session('error') }}
</div>
@endif

@if(session('success'))
<div class="alert alert-success-soft show flex items-center mb-5" role="alert">
    <i data-lucide="check-circle" class="w-6 h-6 mr-2"></i> 
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger-soft show mb-5">
    <ul class="list-disc ml-5">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<form action="{{ route('pembelian.store') }}" method="POST">
    @csrf
    <div class="intro-y grid grid-cols-12 gap-5 mt-5">
        <div class="intro-y col-span-12 lg:col-span-8">
            <div class="box p-5 grid grid-cols-12 gap-4 mb-5">
                <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="tom-select w-full" required>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mb-4">
                <div class="w-full sm:w-auto mt-3 sm:mt-0">
                    <div class="w-56 relative text-slate-500">
                        <input type="text" id="searchBahan" onkeyup="filterBahan()" class="form-control w-56 box pr-10" placeholder="Cari bahan baku...">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i> 
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-5 mt-5" id="bahanList">
                @foreach($bahan as $b)
                <div class="item-bahan col-span-12 sm:col-span-4 2xl:col-span-3 box p-5 cursor-pointer zoom-in" 
                    data-nama="{{ strtolower($b->nama) }}"
                    onclick="addItem({{ $b->id }}, '{{ $b->nama }}', '{{ $b->satuan }}')">
                    <div class="font-medium text-base">{{ $b->nama }}</div>
                    <div class="text-slate-500">{{ $b->satuan }}</div>
                </div>
                @endforeach
            </div>
            
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="intro-y box p-5">
                <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                    <h2 class="font-medium text-base mr-auto">Daftar Item</h2>
                    <i data-lucide="shopping-cart" class="w-4 h-4 text-slate-500"></i>
                </div>
                
                <div id="cart-items">
                    </div>
                <div class="col-span-12 sm:col-span-12 mt-3">
                    <label class="form-label">Metode Pembayaran / Rekening</label>
                    <select name="rekening_id" class="form-select" id="pilih-rekening">
                        <option value="">-- Pilih Rekening (Kosongkan jikaBelum Bayar) --</option>
                        @foreach($rekening as $rk)
                            <option value="{{ $rk->id }}">{{ $rk->nama }} (Saldo: Rp {{ number_format($rk->saldo_saat_ini, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                    <div class="text-slate-500 text-xs mt-1">*Jika pilih rekening, status otomatis LUNAS dan saldo berkurang.</div>
                </div>
                <div class="border-t border-slate-200/60 mt-5 pt-5">
                    <div class="flex items-center">
                        <div class="mr-auto">Total Tagihan</div>
                        <div class="font-medium text-lg text-primary" id="grand-total">Rp 0</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-full mt-5">Simpan Transaksi</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    let cart = [];

    function addItem(id, name, unit) {
        const existing = cart.find(i => i.bahan_id === id);
        if (existing) {
            existing.qty++;
        } else {
            cart.push({ bahan_id: id, nama: name, satuan: unit, qty: 1, harga: 0 });
        }
        renderCart();
    }

    function updateItem(index, field, value) {
        cart[index][field] = value;
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function renderCart() {
        let html = '';
        let grandTotal = 0;

        cart.forEach((item, index) => {
            let subtotal = item.qty * item.harga;
            grandTotal += subtotal;
            html += `
            <div class="mb-4 bg-slate-50 p-3 rounded-md">
                <div class="flex justify-between mb-2">
                    <span class="font-medium">${item.nama}</span>
                    <button type="button" onclick="removeItem(${index})" class="text-danger"><i data-lucide="x" class="w-4 h-4"></i></button>
                </div>
                <input type="hidden" name="items[${index}][bahan_id]" value="${item.bahan_id}">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs">Qty (${item.satuan})</label>
                        <input type="number" name="items[${index}][qty]" value="${item.qty}" 
                               onchange="updateItem(${index}, 'qty', this.value)" class="form-control form-control-sm">
                    </div>
                    <div>
                        <label class="text-xs">Harga Satuan</label>
                        <input type="number" name="items[${index}][harga]" value="${item.harga}" 
                               onchange="updateItem(${index}, 'harga', this.value)" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="text-right text-xs mt-1 text-slate-500">Subtotal: Rp ${subtotal.toLocaleString()}</div>
            </div>`;
        });

        document.getElementById('cart-items').innerHTML = html;
        document.getElementById('grand-total').innerText = 'Rp ' + grandTotal.toLocaleString();
        lucide.createIcons();
    }
    function filterBahan() {
    let input = document.getElementById("searchBahan").value.toLowerCase();
    
    let items = document.querySelectorAll(".item-bahan");

    items.forEach(item => {
        // Ambil nama dari atribut data-nama
        let namaBahan = item.getAttribute("data-nama");
        
        // Sembunyikan jika tidak cocok, tampilkan jika cocok
        if (namaBahan.includes(input)) {
            item.style.display = "";
        } else {
            item.style.display = "none";
        }
    });
}
</script>
@endsection