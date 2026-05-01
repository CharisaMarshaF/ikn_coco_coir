@extends('layouts.app')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger-soft show flex items-center mb-5 mt-5" role="alert">
            <i data-lucide="alert-octagon" class="w-6 h-6 mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success-soft show flex items-center mb-5 mt-5" role="alert">
            <i data-lucide="check-circle" class="w-6 h-6 mr-2"></i>
            {{ session('success') }}
        </div>
    @endif
    <form action="{{ route('pembelian.store') }}" method="POST">
        @csrf
        <div class="intro-y grid grid-cols-12 gap-5 mt-5">
            <div class="intro-y col-span-12 lg:col-span-8">
                <div class="box p-5 grid grid-cols-12 gap-4 mb-5">
                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label font-medium">Supplier</label>
                        <select name="supplier_id" class="tom-select w-full" required>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label font-medium">Tanggal Pembelian</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mb-4">
                    <div class="w-full mt-3 sm:mt-0">
                        <div class="w-full relative text-slate-500">
                            <input type="text" id="searchBahan" onkeyup="filterBahan()"
                                class="form-control w-full box pr-10 py-2"
                                placeholder="Cari bahan baku secara spesifik di sini...">

                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class="w-5 h-5 text-slate-400" data-lucide="search"> </i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-3 mt-5" id="bahanList">
                    @foreach ($bahan as $b)
                        @php $jmlStok = $b->stok->jumlah ?? 0; @endphp
                        
                        <div class="item-bahan col-span-6 sm:col-span-4 lg:col-span-3 box p-3 cursor-pointer zoom-in relative border-2 border-transparent hover:border-primary"
                            data-nama="{{ strtolower($b->nama) }}"
                            onclick="addItem({{ $b->id }}, '{{ $b->nama }}', '{{ $b->satuan }}')">

                            <div class="absolute top-0 right-0 mr-1 mt-1 bg-primary/10 text-primary rounded-full p-0.5">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            </div>

                            <div class="font-bold text-sm text-slate-700 truncate pr-5" title="{{ $b->nama }}">
                                {{ $b->nama }}
                            </div>
                            
                            {{-- <div class="text-slate-500 text-xs mt-1">
                                Satuan: <span class="font-medium italic text-slate-600">{{ $b->satuan }}</span>
                            </div> --}}

                            <div class="mt-2 pt-2 border-t border-slate-100 flex justify-between items-center">
                                <div class="text-xs font-bold">
                                    Stok : <span class="text-xs font-bold {{ $jmlStok <= 5 ? 'text-danger' : 'text-success' }}">
                                        {{ (float) $jmlStok }} {{ $b->satuan }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-span-12 lg:col-span-4">
                <div class="intro-y box p-5 sticky top-10">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                        <h2 class="font-medium text-base mr-auto">Keranjang Pembelian</h2>
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-slate-500"></i>
                    </div>

                    <div id="cart-items" class="max-h-[400px] overflow-y-auto pr-2">
                        <div class="text-center text-slate-500 py-10">
                            <i data-lucide="shopping-cart" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                            <p class="italic text-xs">Klik pada bahan di kiri untuk menambah</p>
                        </div>
                    </div>

                    <div class="border-t border-slate-200/60 mt-5 pt-5">
                        <div class="flex items-center mb-4">
                            <div class="mr-auto text-slate-500 text-xs">STATUS PEMBAYARAN</div>
                            <div class="font-bold text-success uppercase text-xs">Lunas</div>
                        </div>
                        <div class="flex items-center border-t border-slate-100 pt-4">
                            <div class="mr-auto font-medium text-base">Total Akhir</div>
                            <div class="font-bold text-xl text-primary" id="grand-total">Rp 0</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-full mt-6 py-3">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Transaksi
                        </button>
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
                cart.push({
                    bahan_id: id,
                    nama: name,
                    satuan: unit,
                    qty: 1,
                    harga: 0
                });
            }
            renderCart();
        }

        // Fungsi update sekarang lebih tenang karena dipanggil via onchange
        function updateItem(index, field, value) {
            cart[index][field] = parseFloat(value) || 0;
            renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function renderCart() {
            let html = '';
            let grandTotal = 0;

            if (cart.length === 0) {
                html = `<div class="text-center text-slate-500 py-10">
                        <i data-lucide="shopping-cart" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                        <p class="italic text-xs">Klik pada bahan di kiri untuk menambah</p>
                    </div>`;
            } else {
                cart.forEach((item, index) => {
                    let subtotal = item.qty * item.harga;
                    grandTotal += subtotal;
                    html += `
                <div class="mb-4 bg-slate-50 p-3 rounded-md border border-slate-200 intro-x">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-slate-700">${item.nama}</span>
                        <button type="button" onclick="removeItem(${index})" class="text-danger hover:bg-danger/10 p-1 rounded">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <input type="hidden" name="items[${index}][bahan_id]" value="${item.bahan_id}">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] uppercase text-slate-500 font-bold">Qty (${item.satuan})</label>
                            <input type="number" step="any" name="items[${index}][qty]" value="${item.qty}" 
                                   onchange="updateItem(${index}, 'qty', this.value)" class="form-control form-control-sm">
                        </div>
<div>
    <label class="text-[10px] uppercase text-slate-500 font-bold">Harga Satuan</label>
    <input type="text" 
           class="form-control form-control-sm input-mask-harga" 
           value="${item.harga.toLocaleString('id-ID')}" 
           oninput="formatCurrency(this, ${index})" 
           placeholder="Rp..">
    {{-- Input hidden inilah yang akan terkirim ke server (angka murni) --}}
    <input type="hidden" name="items[${index}][harga]" id="harga-real-${index}" value="${item.harga}">
</div>
                    </div>
                    <div class="text-right text-[11px] mt-2 font-bold text-slate-500 italic text-primary">Subtotal: Rp ${subtotal.toLocaleString('id-ID')}</div>
                </div>`;
                });
            }

            document.getElementById('cart-items').innerHTML = html;
            document.getElementById('grand-total').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');

            // Menjalankan createIcons hanya jika ada elemen baru untuk menghindari glitch kursor
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Untuk pencarian, kita tetap pakai onkeyup tapi tanpa re-render cart
        function filterBahan() {
            let input = document.getElementById("searchBahan").value.toLowerCase();
            let items = document.querySelectorAll(".item-bahan");
            items.forEach(item => {
                let namaBahan = item.getAttribute("data-nama");
                item.style.display = namaBahan.includes(input) ? "" : "none";
            });
        }

        function formatCurrency(input, index) {
    // 1. Ambil angka saja dari input
    let value = input.value.replace(/[^0-9]/g, '');
    
    // 2. Jika kosong, set ke 0
    let numberValue = parseFloat(value) || 0;
    
    // 3. Update tampilan input dengan titik (ID-id)
    input.value = numberValue.toLocaleString('id-ID');
    
    // 4. Update data di array cart
    cart[index].harga = numberValue;
    
    // 5. Update hidden input untuk dikirim ke server
    document.getElementById(`harga-real-${index}`).value = numberValue;
    
    // 6. Hitung ulang total tanpa re-render seluruh HTML (agar kursor tidak lari)
    calculateLiveTotal();
}

function calculateLiveTotal() {
    let grandTotal = 0;
    cart.forEach((item, index) => {
        let subtotal = item.qty * item.harga;
        grandTotal += subtotal;
        
        // Update teks subtotal di baris tersebut secara langsung
        const subtotalElement = document.querySelectorAll('.text-right.text-\\[11px\\]')[index];
        if (subtotalElement) {
            subtotalElement.innerText = `Subtotal: Rp ${subtotal.toLocaleString('id-ID')}`;
        }
    });
    
    // Update Grand Total
    document.getElementById('grand-total').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
}
    </script>
@endsection
