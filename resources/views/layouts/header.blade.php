<!DOCTYPE html>
<html lang="en" class="light">
    <!-- BEGIN: Head -->
    <head>
        <meta charset="utf-8">
        <link href="dist/images/logo.svg" rel="shortcut icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Tinker admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities.">
        <meta name="keywords" content="admin template, Tinker Admin Template, dashboard template, flat admin template, responsive admin template, web app">
        <meta name="author" content="LEFT4CODE">
        <title>Dashboard - IKN COCO COIR</title>
        <!-- BEGIN: CSS Assets-->
        <link rel="stylesheet" href="{{ asset('template/dist/css/app.css') }}" />
<style>
    /* Sembunyikan tabel di awal */
    #example1 {
        display: none;
        opacity: 0;
    }

    /* Definisi Animasi */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Class untuk memicu animasi */
    .table-show {
        display: table !important; /* Paksa jadi table kembali */
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    /* Percantik sedikit input pencarian agar senada dengan Midone */
    .dataTables_filter input {
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.4rem 0.8rem;
    }
    
    .dataTables_filter input:focus {
        border-color: #1e40af;
        box-shadow: 0 0 0 2px rgba(30, 64, 175, 0.1);
        outline: none;
    }
</style>
    </head>