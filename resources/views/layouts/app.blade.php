@include('layouts.header')
    <!-- END: Head -->
    <body class="py-5 md:py-0 bg-black/[0.15] dark:bg-transparent">
        @include('layouts.navbar')


            <!-- BEGIN: Content -->
            <div class="content">
                @include('layouts.topbar')

                @yield('content')         
                    </div>    
                </div>
            </div>
            <!-- END: Content -->
        </div>
        @include('layouts.js')
    </body>
</html>