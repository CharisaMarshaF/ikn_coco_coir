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
        	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>

<script>
$(document).ready(function () {
    $('#example1').DataTable({
        "searching": true,
        "lengthChange": false,
        "language": {
            "search": "Pencarian "
        },
        "initComplete": function(settings, json) {
            // Tambahkan class animasi setelah inisialisasi selesai
            $('#example1').addClass('table-show');
        },
        "drawCallback": function() {
            // Pastikan icon Lucide tetap ter-render saat pindah halaman/pencarian
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Tambahkan sedikit efek transisi pada baris tabel saat ganti halaman
            $('.intro-x').css({
                'opacity': '0',
                'transform': 'translateX(-10px)'
            }).each(function(i) {
                $(this).delay(30 * i).animate({
                    'opacity': '1',
                    'transform': 'translateX(0)'
                }, 300);
            });
        }
    });
});
</script>

    </body>
</html>