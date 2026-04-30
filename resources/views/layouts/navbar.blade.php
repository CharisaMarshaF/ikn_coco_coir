                <!-- BEGIN: Mobile Menu -->
                <div class="mobile-menu md:hidden">
                    <div class="mobile-menu-bar">
                        <a href="" class="flex mr-auto">
                            <img alt="Midone - HTML Admin Template" class="w-6" src="{{ asset('template/logo.png') }}">
                        </a>
                        <a href="javascript:;" class="mobile-menu-toggler"> <i data-lucide="bar-chart-2"
                                class="w-8 h-8 text-white transform -rotate-90"></i> </a>
                    </div>
                    <div class="scrollable">
                        <a href="javascript:;" class="mobile-menu-toggler">
                            <i data-lucide="x-circle" class="w-8 h-8 text-white transform -rotate-90"></i>
                        </a>
                        <ul class="scrollable__content py-2">
                            <li>
                                <a href="{{ route('admin.dashboard') }}"
                                    class="menu {{ request()->routeIs('admin.dashboard') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="home"></i> </div>
                                    <div class="menu__title"> Dashboard </div>
                                </a>
                            </li>

                            <li>
                                <a href="javascript:;"
                                    class="menu {{ request()->routeIs('users.*', 'supplier.*', 'bahan-baku.*', 'produk.*', 'client.*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="database"></i> </div>
                                    <div class="menu__title">
                                        Master Data
                                        <i data-lucide="chevron-down"
                                            class="menu__sub-icon {{ request()->routeIs('users.*', 'supplier.*', 'bahan-baku.*', 'produk.*', 'client.*') ? 'transform rotate-180' : '' }}"></i>
                                    </div>
                                </a>
                                <ul
                                    class="{{ request()->routeIs('users.*', 'supplier.*', 'bahan-baku.*', 'produk.*', 'client.*') ? 'menu__sub-open' : '' }}">
                                    <li>
                                        <a href="{{ route('users.index') }}"
                                            class="menu {{ request()->routeIs('users.*') ? 'menu--active' : '' }}">
                                            <div class="menu__icon"> <i data-lucide="user"></i> </div>
                                            <div class="menu__title"> User </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('supplier.index') }}"
                                            class="menu {{ request()->routeIs('supplier.*') ? 'menu--active' : '' }}">
                                            <div class="menu__icon"> <i data-lucide="truck"></i> </div>
                                            <div class="menu__title"> Supplier </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('bahan-baku.index') }}"
                                            class="menu {{ request()->routeIs('bahan-baku.*') ? 'menu--active' : '' }}">
                                            <div class="menu__icon"> <i data-lucide="package"></i> </div>
                                            <div class="menu__title"> Bahan Baku </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('produk.index') }}"
                                            class="menu {{ request()->routeIs('produk.*') ? 'menu--active' : '' }}">
                                            <div class="menu__icon"> <i data-lucide="box"></i> </div>
                                            <div class="menu__title"> Produk </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('client.index') }}"
                                            class="menu {{ request()->routeIs('client.*') ? 'menu--active' : '' }}">
                                            <div class="menu__icon"> <i data-lucide="users"></i> </div>
                                            <div class="menu__title"> Client </div>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="javascript:;"
                                    class="menu {{ request()->routeIs('rekening.*', 'kas.*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="dollar-sign"></i> </div>
                                    <div class="menu__title">
                                        Keuangan
                                        <i data-lucide="chevron-down"
                                            class="menu__sub-icon {{ request()->routeIs('rekening.*', 'kas.*') ? 'transform rotate-180' : '' }}"></i>
                                    </div>
                                </a>
                                <ul class="{{ request()->routeIs('rekening.*', 'kas.*') ? 'menu__sub-open' : '' }}">
                                    <li>
                                        <a href="{{ route('rekening.index') }}"
                                            class="menu {{ request()->routeIs('rekening.*') ? 'menu--active' : '' }}">
                                            <div class="menu__icon"> <i data-lucide="credit-card"></i> </div>
                                            <div class="menu__title"> Rekening </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('kas.index') }}"
                                            class="menu {{ request()->routeIs('kas.*') ? 'menu--active' : '' }}">
                                            <div class="menu__icon"> <i data-lucide="calendar"></i> </div>
                                            <div class="menu__title"> Kas Harian </div>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="javascript:;"
                                    class="menu {{ request()->routeIs('laporan.*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="file-text"></i> </div>
                                    <div class="menu__title">
                                        Laporan
                                        <i data-lucide="chevron-down"
                                            class="menu__sub-icon {{ request()->routeIs('laporan.*') ? 'transform rotate-180' : '' }}"></i>
                                    </div>
                                </a>
                                <ul class="{{ request()->routeIs('laporan.*') ? 'menu__sub-open' : '' }}">
                                    <li>
                                        <a href="{{ route('laporan.penjualan') }}"
                                            class="menu {{ request()->routeIs('laporan.penjualan') ? 'menu--active' : '' }}">
                                            <div class="menu__icon"> <i data-lucide="clipboard"></i> </div>
                                            <div class="menu__title"> Laporan Penjualan </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('laporan.pembelian') }}"
                                            class="menu {{ request()->routeIs('laporan.pembelian') ? 'menu--active' : '' }}">
                                            <div class="menu__icon"> <i data-lucide="clipboard"></i> </div>
                                            <div class="menu__title"> Laporan Pembelian </div>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="side-nav__devider my-6"></li>

                            <li>
                                <a href="{{ route('pembelian.index') }}"
                                    class="menu {{ request()->routeIs('pembelian.*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="shopping-bag"></i> </div>
                                    <div class="menu__title"> Pembelian </div>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('produksi.index') }}"
                                    class="menu {{ request()->routeIs('produksi.*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="wrench"></i> </div>
                                    <div class="menu__title"> Produksi </div>
                                </a>
                            </li> --}}
                            <li>
                                <a href="{{ route('penjualan.index') }}"
                                    class="menu {{ request()->routeIs('penjualan.*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="shopping-cart"></i> </div>
                                    <div class="menu__title"> Penjualan </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pengambilan.index') }}"
                                    class="menu {{ request()->routeIs('pengambilan.*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="truck"></i> </div>
                                    <div class="menu__title"> Pengambilan Bahan</div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('hasil-produksi.index') }}"
                                    class="menu {{ request()->routeIs('hasil-produksi.*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="clipboard"></i> </div>
                                    <div class="menu__title"> Hasil Produksi </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- END: Mobile Menu -->
                <div class="flex mt-[4.7rem] md:mt-0 overflow-hidden">
                    <nav class="side-nav">
                        <a href="" class="intro-x flex items-center pl-5 pt-4 mt-3">
                            <img alt="IKN Coco Coir Logo" class="w-6" src="{{ asset('template/logo.png') }}">
                            <span class="hidden xl:block text-white text-lg ml-3"> IKN COCO COIR </span>
                        </a>
                        <div class="side-nav__devider my-6"></div>
                        <ul>
                            <li>
                                <a href="{{ route('admin.dashboard') }}"
                                    class="side-menu {{ request()->routeIs('admin.dashboard') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="home"></i> </div>
                                    <div class="side-menu__title"> Dashboard </div>
                                </a>
                            </li>

                            <li>
                                <!-- Menu Utama Master Data -->
                                <a href="javascript:;"
                                    class="side-menu {{ request()->routeIs('users.*', 'supplier.*', 'bahan-baku.*', 'produk.*', 'client.*', 'konfigurasi.*', 'stock-log.*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="database"></i> </div>
                                    <div class="side-menu__title">
                                        Master Data
                                        <div
                                            class="side-menu__sub-icon {{ request()->routeIs('users.*', 'supplier.*', 'bahan-baku.*', 'produk.*', 'client.*', 'konfigurasi.*', 'stock-log.*') ? 'transform rotate-180' : '' }}">
                                            <i data-lucide="chevron-down"></i>
                                        </div>
                                    </div>
                                </a>

                                <ul
                                    class="{{ request()->routeIs('users.*', 'supplier.*', 'bahan-baku.*', 'produk.*', 'client.*', 'konfigurasi.*', 'stock-log.*') ? 'side-menu__sub-open' : '' }}">
                                    <!-- Submenu User -->
                                    <li>
                                        <a href="{{ route('users.index') }}"
                                            class="side-menu {{ request()->routeIs('users.*') ? 'side-menu--active' : '' }}">
                                            <div class="side-menu__icon"> <i data-lucide="user"></i> </div>
                                            <div class="side-menu__title"> User </div>
                                        </a>
                                    </li>

                                    <!-- Submenu Supplier -->
                                    <li>
                                        <a href="{{ route('supplier.index') }}"
                                            class="side-menu {{ request()->routeIs('supplier.*') ? 'side-menu--active' : '' }}">
                                            <div class="side-menu__icon"> <i data-lucide="truck"></i> </div>
                                            <div class="side-menu__title"> Supplier </div>
                                        </a>
                                    </li>

                                    <!-- Submenu Bahan Baku -->
                                    {{-- Aktif jika di route bahan-baku ATAU di stock-log dengan parameter type 'bahan' --}}
                                    <li>
                                        <a href="{{ route('bahan-baku.index') }}"
                                            class="side-menu {{ request()->routeIs('bahan-baku.*') || (request()->routeIs('stock-log.*') && request('type') == 'bahan') ? 'side-menu--active' : '' }}">
                                            <div class="side-menu__icon"> <i data-lucide="package"></i> </div>
                                            <div class="side-menu__title"> Bahan Baku </div>
                                        </a>
                                    </li>

                                    <!-- Submenu Produk -->
                                    {{-- Aktif jika di route produk ATAU di stock-log dengan parameter type 'produk' --}}
                                    <li>
                                        <a href="{{ route('produk.index') }}"
                                            class="side-menu {{ request()->routeIs('produk.*') || (request()->routeIs('stock-log.*') && request('type') == 'produk') ? 'side-menu--active' : '' }}">
                                            <div class="side-menu__icon"> <i data-lucide="box"></i> </div>
                                            <div class="side-menu__title"> Produk </div>
                                        </a>
                                    </li>

                                    <!-- Submenu Client -->
                                    <li>
                                        <a href="{{ route('client.index') }}"
                                            class="side-menu {{ request()->routeIs('client.*') ? 'side-menu--active' : '' }}">
                                            <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                                            <div class="side-menu__title"> Client </div>
                                        </a>
                                    </li>

                                    <!-- Submenu Konfigurasi -->
                                    <li>
                                        <a href="{{ route('konfigurasi.index') }}"
                                            class="side-menu {{ request()->routeIs('konfigurasi.*') ? 'side-menu--active' : '' }}">
                                            <div class="side-menu__icon"> <i data-lucide="settings"></i> </div>
                                            <div class="side-menu__title"> Konfigurasi </div>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="javascript:;"
                                    class="side-menu {{ request()->is('rekening*', 'keuangan/kas*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="dollar-sign"></i> </div>
                                    <div class="side-menu__title">
                                        Keuangan
                                        <div
                                            class="side-menu__sub-icon {{ request()->is('rekening*', 'keuangan/kas*') ? 'transform rotate-180' : '' }}">
                                            <i data-lucide="chevron-down"></i>
                                        </div>
                                    </div>
                                </a>
                                <ul
                                    class="{{ request()->is('rekening*', 'keuangan/kas*') ? 'side-menu__sub-open' : '' }}">
                                    <li>
                                        <a href="{{ route('rekening.index') }}"
                                            class="side-menu {{ request()->is('rekening*') ? 'side-menu--active' : '' }}">
                                            <div class="side-menu__icon"> <i data-lucide="credit-card"></i> </div>
                                            <div class="side-menu__title"> Rekening </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('kas.index') }}"
                                            class="side-menu {{ request()->is('keuangan/kas*') ? 'side-menu--active' : '' }}">
                                            <div class="side-menu__icon"> <i data-lucide="calendar"></i> </div>
                                            <div class="side-menu__title"> Kas Harian</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="javascript:;"
                                    class="side-menu {{ request()->is('laporan*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="file-text"></i> </div>
                                    <div class="side-menu__title">
                                        Laporan
                                        <div
                                            class="side-menu__sub-icon {{ request()->is('laporan*') ? 'transform rotate-180' : '' }}">
                                            <i data-lucide="chevron-down"></i>
                                        </div>
                                    </div>
                                </a>
                                <ul class="{{ request()->is('laporan*') ? 'side-menu__sub-open' : '' }}">
                                    <li>
                                        <a href="{{ route('laporan.penjualan') }}"
                                            class="side-menu {{ request()->is('laporan/penjualan*') ? 'side-menu--active' : '' }}">
                                            <div class="side-menu__icon"> <i data-lucide="clipboard"></i> </div>
                                            <div class="side-menu__title"> Laporan Penjualan </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('laporan.pembelian') }}"
                                            class="side-menu {{ request()->is('laporan/pembelian*') ? 'side-menu--active' : '' }}">
                                            <div class="side-menu__icon"> <i data-lucide="clipboard"></i> </div>
                                            <div class="side-menu__title"> Laporan Pembelian</div>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="side-nav__devider my-6"></li>

                            <li>
                                <a href="{{ route('pembelian.index') }}"
                                    class="side-menu {{ request()->routeIs('pembelian.*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="shopping-bag"></i> </div>
                                    <div class="side-menu__title"> Pembelian </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('penjualan.index') }}"
                                    class="side-menu {{ request()->routeIs('penjualan.*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="shopping-cart"></i> </div>
                                    <div class="side-menu__title"> Penjualan </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pengambilan.index') }}"
                                    class="side-menu {{ request()->routeIs('pengambilan.*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="wrench"></i> </div>
                                    <div class="side-menu__title"> Pengambilan Bahan </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('hasil-produksi.index') }}"
                                    class="side-menu {{ request()->routeIs('hasil-produksi.*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="clipboard"></i> </div>
                                    <div class="side-menu__title"> Hasil Produksi </div>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('produksi.index') }}"
                                    class="side-menu {{ request()->routeIs('produksi.*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="wrench"></i> </div>
                                    <div class="side-menu__title"> Produksi </div>
                                </a>
                            </li> --}}

                        </ul>
                    </nav>
