<!DOCTYPE html>
<html>
<head>
    <title>{{ $type == 'sj' ? 'Surat Jalan' : 'Invoice' }} - {{ $penjualan->id }}</title>
    <style>
        @page { 
            /* Otomatis deteksi orientasi berdasarkan tipe */
            size: {{ $type == 'sj' ? '148mm 210mm' : '210mm 148mm' }}; 
            margin: 10mm; 
        }
        
        body { 
            font-family: 'Courier', monospace; 
            font-size: 9pt; 
            margin: 0; 
            padding: 0;
            color: #000;
            line-height: 1.1;
        }

        #watermark {
            position: fixed;
            top: 20%;
            left: 25%;
            width: 250px;
            opacity: 0.06;
            z-index: -1000;
        }

        .header-table { width: 100%; border-bottom: 2px double #000; margin-bottom: 8px; padding-bottom: 5px; }
        .logo-top { width: 50px; height: auto; }
        .company-name { font-size: 12pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .company-info { font-size: 7pt; }
        .document-title { font-size: 16pt; font-weight: bold; text-align: right; }
        
        .info-table { width: 100%; margin-bottom: 8px; }
        .info-table td { vertical-align: top; font-size: 8pt; }
        .label-yth { font-weight: bold; text-decoration: underline; margin-bottom: 3px; display: block; }
        
        .nota-info-table { width: 100%; border-spacing: 0; }
        .nota-info-table td { padding: 1px 0; }

        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .main-table th { 
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px; 
            text-align: left;
            font-size: 8pt;
        }
        .main-table td { padding: 4px; font-size: 8pt; vertical-align: top; border-bottom: 1px dotted #ccc; }

        .summary-container { float: right; width: 45%; margin-top: 5px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 2px 5px; font-size: 9pt; }
        .border-total { border-top: 1px solid #000; border-bottom: 1px solid #000; }

        /* Tanda Tangan Section */
        .footer-sign { margin-top: 15px; width: 100%; }
        .footer-sign td { text-align: center; width: 33.3%; font-size: 8pt; vertical-align: top; }
        .sign-space { height: 45px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
    </style>
</head>
<body>
    @if($company && $company->logo)
    <div id="watermark">
        <img src="{{ public_path('storage/' . $company->logo) }}" style="width: 100%;">
    </div>
    @endif

    <table class="header-table">
        <tr>
            <td width="10%">
                @if($company && $company->logo)
                <img src="{{ public_path('storage/' . $company->logo) }}" class="logo-top">
                @endif
            </td>
            <td width="50%">
                <h1 class="company-name">{{ $company->nama_cv ?? 'NAMA PERUSAHAAN' }}</h1>
                <div class="company-info">
                    {{ $company->alamat ?? '-' }}<br>
                    Telp: {{ $company->telepon ?? '-' }}
                </div>
            </td>
            <td class="document-title">
                {{ $type == 'sj' ? 'SURAT JALAN' : 'INVOICE' }}
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="55%">
                <span class="label-yth">KEPADA YTH:</span>
                <table width="100%" style="border-spacing: 0;">
                    <tr>
                        <td width="30%">Penerima</td>
                        <td>: <span class="bold uppercase">{{ $penjualan->client->nama ?? 'PELANGGAN UMUM' }}</span></td>
                    </tr>
                    <tr>
                        <td valign="top">Alamat</td>
                        <td>: {{ $penjualan->client->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Telepon</td>
                        <td>: {{ $penjualan->client->telp ?? '-' }}</td>
                    </tr>
                </table>
            </td>

            <td width="45%">
                <div style="float: right; width: 95%;">
                    <table class="nota-info-table">
                        <tr>
                            <td width="40%">No. Nota</td>
                            <td>: <span class="bold">{{ $type == 'sj' ? ($penjualan->suratJalan->nomor ?? '-') : ($penjualan->invoice->nomor ?? '-') }}</span></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ date('d/m/Y', strtotime($penjualan->tanggal)) }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: <span class="uppercase">{{ $type == 'sj' ? 'Pengiriman' : 'Lunas' }}</span></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">NO</th>
                <th width="{{ $type == 'invoice' ? '45%' : '75%' }}">NAMA BARANG</th>
                <th width="15%" class="text-center">QTY</th>
                @if($type == 'invoice')
                <th width="15%" class="text-right">HARGA</th>
                <th width="20%" class="text-right">TOTAL</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->detail as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="uppercase">{{ $item->produk->nama }}</td>
                <td class="text-">{{ $item->qty + 0 }} {{ $item->produk->satuan }}</td>
                @if($type == 'invoice')
                <td class="text-">{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($type == 'invoice')
    <div class="summary-container">
        <table class="summary-table">
            <tr>
                <td class="bold text-right">TOTAL AKHIR:</td>
                <td class="bold text- border-total" style="width: 50%;">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    @endif

    <div style="clear: both;"></div>

    <table class="footer-sign">
        <tr>
            <td>
                (Pengirim/Admin)
                <div class="sign-space"></div>
                ( ................. )
            </td>
            <td>
                {{ $type == 'sj' ? 'Sopir/Logistik,' : '' }}
                @if($type == 'sj')
                <div class="sign-space"></div>
                ( ................. )
                @endif
            </td>
            <td>
                Penerima/Customer,
                <div class="sign-space"></div>
                ( ................. )
            </td>
        </tr>
    </table>

    <div style="margin-top: 15px; font-size: 7pt; font-style: italic; border-top: 1px solid #eee; padding-top: 5px;">
        * Cetakan komputer, sah tanpa tanda tangan basah jika sudah ada stempel resmi.<br>
        * {{ $type == 'sj' ? 'Harap periksa barang saat diterima.' : 'Barang yang sudah dibeli tidak dapat dikembalikan.' }}
    </div>
</body>
</html>