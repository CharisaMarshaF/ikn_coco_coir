@extends('layouts.app')

@section('content')
    <style>
        /* Style TomSelect agar rapi */
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
                <!-- Bagian Tanggal dan Keterangan (Pindah ke Atas) -->
                <div class="box p-5 grid grid-cols-12 gap-4 mb-5 border-t-4 border-success">
                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label font-bold text-success">Tanggal Produksi Selesai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label font-bold text-slate-600">Keterangan / Catatan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Misal: Produksi Batch Pagi / Shift 1">
                    </div>
                </div>

                <!-- Search Produk -->
                <div class="intro-y flex flex-wrap sm:flex-nowrap items-center mb-4">
                    <div class="w-full flex-1">
                        <div class="relative text-slate-500">
                            <input type="text" id="searchProduk" onkeyup="filterProduk()"
                                class="form-control w-full box pr-10"
                                placeholder="Cari produk proses (setengah jadi / jadi)...">
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
                            onclick="addItem({{ $p->id }}, '{{ $p->nama }}', '{{ $p->satuan }}', {{ $stokSekarang }})">

                            <div class="absolute top-0 right-0 mr-1 mt-1 bg-success/10 text-success rounded-full p-0.5">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            </div>

                            <div class="font-bold text-sm text-slate-700 truncate pr-5" title="{{ $p->nama }}">
                                {{ $p->nama }}
                            </div>

                            <div class="text-slate-500 text-[10px] mt-1 italic">
                                Satuan: {{ $p->satuan }}
                            </div>

                            <div class="mt-2 pt-2 border-t border-slate-100 flex justify-between items-center">
                                <div class="text-xs font-bold">
                                    Stok: <span class="text-xs font-bold text-primary"> {{ (float) $stokSekarang }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Keranjang Produksi -->
            <div class="col-span-12 lg:col-span-4">
                <div class="intro-y box p-5 sticky top-5 border-t-4 border-success">
                    <div class="flex items-center border-b border-slate-200/60 pb-5 mb-5">
                        <h2 class="font-medium text-base mr-auto text-success">Daftar Hasil Produksi</h2>
                        <i data-lucide="package" class="w-5 h-5 text-success"></i>
                    </div>

                    <div id="cart-items" class="overflow-y-auto max-h-[400px] pr-2">
                        <div class="text-center py-5 text-slate-400 italic" id="empty-cart">
                            Klik produk di kiri untuk menambah hasil produksi.
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

        function addItem(id, name, unit, stock) {
            const existing = cart.find(i => i.produk_id === id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({
                    produk_id: id,
                    nama: name,
                    satuan: unit,
                    qty: 1
                });
            }
            renderCart();
        }

        function updateItem(index, value) {
            let val = parseFloat(value) || 0;
            if (val < 0) val = 0;
            cart[index].qty = val;
            renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function renderCart() {
            let html = '';
            let totalItems = 0;
            const container = document.getElementById('cart-items');

            if (cart.length === 0) {
                container.innerHTML = `<div class="text-center py-5 text-slate-400 italic" id="empty-cart">Klik produk di kiri untuk menambah hasil produksi.</div>`;
                document.getElementById('total-items').innerText = '0';
                return;
            }

            cart.forEach((item, index) => {
                totalItems += item.qty;
                html += `
                <div class="mb-4 bg-slate-50 p-3 rounded-lg border border-slate-200 intro-x">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-slate-700">${item.nama}</span>
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
                                   onchange="updateItem(${index}, this.value)" class="form-control form-control-sm">
                            <div class="input-group-text text-xs">${item.satuan}</div>
                        </div>
                    </div>
                </div>`;
            });

            container.innerHTML = html;
            document.getElementById('total-items').innerText = totalItems;
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