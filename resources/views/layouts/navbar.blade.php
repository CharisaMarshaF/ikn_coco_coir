                <!-- BEGIN: Mobile Menu -->
        <div class="mobile-menu md:hidden">
            <div class="mobile-menu-bar">
                <a href="" class="flex mr-auto">
                    <img alt="Midone - HTML Admin Template" class="w-6" src="{{ asset('template/dist/images/logo.svg') }}">
                </a>
                <a href="javascript:;" class="mobile-menu-toggler"> <i data-lucide="bar-chart-2" class="w-8 h-8 text-white transform -rotate-90"></i> </a>
            </div>
            <div class="scrollable">
                <a href="javascript:;" class="mobile-menu-toggler"> <i data-lucide="x-circle" class="w-8 h-8 text-white transform -rotate-90"></i> </a>
                <ul class="scrollable__content py-2">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="menu {{ request()->is('admin/dashboard') ? 'menu--active' : '' }}">
                            <div class="menu__icon"> <i data-lucide="inbox"></i> </div>
                            <div class="menu__title"> Dashboard </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" class="menu">
                            <div class="menu__icon"> <i data-lucide="database"></i> </div>
                            <div class="menu__title"> Master Data <i data-lucide="chevron-down" class="menu__sub-icon "></i> </div>
                        </a>
                        <ul class="">
                            <li>
                                <a href="{{ route('users.index') }}" class="menu {{ request()->is('users*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="menu__title"> User </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('supplier.index') }}" class="menu {{ request()->is('supplier*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="menu__title"> Suplier </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bahan-baku.index') }}" class="menu {{ request()->is('bahan-baku*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="menu__title"> Bahan Baku </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('produk.index') }}" class="menu {{ request()->is('produk*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="menu__title"> Produk </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('client.index') }}" class="menu {{ request()->is('client*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="menu__title"> Client </div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:;" class="menu">
                            <div class="menu__icon"> <i data-lucide="dollar-sign"></i> </div>
                            <div class="menu__title"> Keuangan <i data-lucide="chevron-down" class="menu__sub-icon "></i> </div>
                        </a>
                        <ul class="">
                            <li>
                                <a href="{{ route('rekening.index') }}" class="menu {{ request()->is('rekening*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="menu__title"> Rekening </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kas.index') }}" class="menu {{ request()->is('kas/keuangan*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="menu__title"> Kas Harian </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('keuangan.index') }}" class="menu {{ request()->is('keuangan*') ? 'menu--active' : '' }}">
                                    <div class="menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="menu__title"> Keuangan</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('pembelian.index') }}" class="menu {{ request()->is('pembelian*') ? 'menu--active' : '' }}">
                            <div class="menu__icon"> <i data-lucide="shopping-bag"></i> </div>
                            <div class="menu__title"> Pembelian </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('produksi.index') }}" class="menu {{ request()->is('produksi*') ? 'menu--active' : '' }}">
                            <div class="menu__icon"> <i data-lucide="wrench"></i> </div>
                            <div class="menu__title"> Produksi </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('penjualan.index') }}" class="menu {{ request()->is('penjualan*') ? 'menu--active' : '' }}">
                            <div class="menu__icon"> <i data-lucide="shopping-cart"></i> </div>
                            <div class="menu__title"> Penjualan </div>
                        </a>
                    </li>
                    <li class="side-nav__devider my-6"></li>

                </ul>
            </div>
        </div>
        <!-- END: Mobile Menu -->
        <div class="flex mt-[4.7rem] md:mt-0 overflow-hidden">
            <!-- BEGIN: Side Menu -->
            <nav class="side-nav">
                <a href="" class="intro-x flex items-center pl-5 pt-4 mt-3">
                    <img alt="Midone - HTML Admin Template" class="w-6" src="{{ asset('template/dist/images/logo.svg') }}">
                    <span class="hidden xl:block text-white text-lg ml-3"> IKN COCO COIR </span> 
                </a>
                <div class="side-nav__devider my-6"></div>
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="side-menu {{ request()->is('admin/dashboard') ? 'side-menu--active' : '' }}">
                            <div class="side-menu__icon"> <i data-lucide="home"></i> </div>
                            <div class="side-menu__title"> Dashboard </div>
                        </a>
                    </li>
                     <li>
                        <a href="javascript:;" class="side-menu">
                            <div class="side-menu__icon"> <i data-lucide="database"></i> </div>
                            <div class="side-menu__title">
                                Master Data 
                                <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
                            </div>
                        </a>
                        <ul class="">
                            <li>
                                <a href="{{ route('users.index') }}" class="side-menu {{ request()->is('users*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="side-menu__title"> User </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('supplier.index') }}" class="side-menu {{ request()->is('supplier*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="side-menu__title"> Supplier </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bahan-baku.index') }}" class="side-menu {{ request()->is('bahan-baku*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="side-menu__title"> Bahan Baku </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('produk.index') }}" class="side-menu {{ request()->is('produk*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="side-menu__title"> Produk </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('client.index') }}" class="side-menu {{ request()->is('client*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="side-menu__title"> Client </div>
                                </a>
                            </li>
                        </ul>
                    </li>
                     <li>
                        <a href="javascript:;" class="side-menu">
                            <div class="side-menu__icon"> <i data-lucide="dollar-sign"></i> </div>
                            <div class="side-menu__title">
                                Keuangan 
                                <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
                            </div>
                        </a>
                        <ul class="">
                            <li>
                                <a href="{{ route('rekening.index') }}" class="side-menu {{ request()->is('rekening*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="side-menu__title"> Rekening </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kas.index') }}" class="side-menu {{ request()->is('keuangan/kas*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="side-menu__title"> Kas Harian</div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('keuangan.index') }}" class="side-menu {{ request()->is('keuangan*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                                    <div class="side-menu__title"> Keuangan </div>
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                    {{-- <li>
                        <a href="{{ route('supplier.index') }}" class="side-menu {{ request()->is('supplier*') ? 'side-menu--active' : '' }}">
                            <div class="side-menu__icon"> <i data-lucide="truck"></i> </div>
                            <div class="side-menu__title"> Supplier </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bahan-baku.index') }}" class="side-menu {{ request()->is('bahan-baku*') ? 'side-menu--active' : '' }}">
                            <div class="side-menu__icon"> <i data-lucide="square"></i> </div>
                            <div class="side-menu__title"> Bahan Baku </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('produk.index') }}" class="side-menu {{ request()->is('produk*') ? 'side-menu--active' : '' }}">
                            <div class="side-menu__icon"> <i data-lucide="package"></i> </div>
                            <div class="side-menu__title"> Produk </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('client.index') }}" class="side-menu {{ request()->is('client*') ? 'side-menu--active' : '' }}">
                            <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                            <div class="side-menu__title"> Client </div>
                        </a>
                    </li> --}}
                    {{-- <li>
                        <a href="{{ route('rekening.index') }}" class="side-menu {{ request()->is('rekening*') ? 'side-menu--active' : '' }}">
                            <div class="side-menu__icon"> <i data-lucide="credit-card"></i> </div>
                            <div class="side-menu__title"> Rekening </div>
                        </a> --}}
                    <li class="side-nav__devider my-6"></li>
                    <li>
                        <a href="{{ route('pembelian.index') }}" class="side-menu {{ request()->is('pembelian*') ? 'side-menu--active' : '' }}">
                            <div class="side-menu__icon"> <i data-lucide="shopping-bag"></i> </div>
                            <div class="side-menu__title"> Pembelian </div>
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('produksi.index') }}" class="side-menu {{ request()->is('produksi*') ? 'side-menu--active' : '' }}">
                            <div class="side-menu__icon"> <i data-lucide="wrench"></i> </div>
                            <div class="side-menu__title"> Produksi </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('penjualan.index') }}" class="side-menu {{ request()->is('penjualan*') ? 'side-menu--active' : '' }}">
                            <div class="side-menu__icon"> <i data-lucide="shopping-cart"></i> </div>
                            <div class="side-menu__title"> Penjualan </div>
                        </a>
                    </li>
                        {{-- <li>
                            <a href="{{ route('kas.index') }}" class="side-menu {{ request()->is('keuangan/kas*') ? 'side-menu--active' : '' }}">
                                <div class="side-menu__icon"> <i data-lucide="dollar-sign"></i> </div>
                                <div class="side-menu__title"> Kas Harian </div>
                            </a>
                        </li>
                        <li>
                                <a href="{{ route('keuangan.index') }}" class="side-menu {{ request()->is('keuangan*') ? 'side-menu--active' : '' }}">
                                    <div class="side-menu__icon"> <i data-lucide="pie-chart"></i> </div>
                                    <div class="side-menu__title"> Keuangan </div>
                                </a>
                            </li> --}}
                    
                </ul>
            </nav>

            