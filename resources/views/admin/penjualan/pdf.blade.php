<!DOCTYPE html>
<html>
<head>
    <title>{{ $type == 'sj' ? 'Surat Jalan' : 'Invoice' }} - {{ $penjualan->id }}</title>
    <style>
        @page { 
            size: 210mm 148mm; 
            margin: 10mm; 
        }
        
        body { 
            font-family: 'Courier', monospace; 
            font-size: 10pt; 
            margin: 0; 
            padding: 0;
            color: #000;
            line-height: 1.2;
        }

        #watermark {
            position: fixed;
            top: 25%;
            left: 30%;
            width: 200px;
            opacity: 0.08;
            z-index: -1000;
        }

        .header-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 10px; padding-bottom: 5px; }
        .logo-top { width: 60px; height: auto; }
        .company-name { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .company-info { font-size: 8pt; }
        .document-title { font-size: 18pt; font-weight: bold; text-align: right; }
        
        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { vertical-align: top; font-size: 9pt; }
        .label-yth { font-weight: bold; text-decoration: underline; margin-bottom: 5px; display: block; }
        
        /* CSS untuk memajukan info nota ke kanan */
        .nota-info-table { 
            width: 100%; 
            border-spacing: 0;
        }
        .nota-info-table td {
            padding: 1px 0;
        }

        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .main-table th { 
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px; 
            text-align: left;
            font-size: 9pt;
        }
        .main-table td { padding: 5px; font-size: 9pt; vertical-align: top; border-bottom: 1px dotted #ccc; }

        .summary-container { float: right; width: 40%; margin-top: 5px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 3px 5px; }
        .border-total { border-top: 1px solid #000; border-bottom: 1px solid #000; }

        .footer-sign { margin-top: 20px; width: 100%; clear: both; }
        .footer-sign td { text-align: center; width: 33%; font-size: 9pt; vertical-align: top; }
        .sign-space { height: 50px; }
        
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
            <td width="55%">
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
                        <td width="25%">Penerima</td>
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
                <div style="float: right; width: 90%;">
                    <table class="nota-info-table">
                        <tr>
                            <td width="45%">No. Nota</td>
                            <td>: <span class="bold">{{ $type == 'sj' ? $penjualan->suratJalan->nomor : $penjualan->invoice->nomor }}</span></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ date('d/m/Y', strtotime($penjualan->tanggal)) }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: {{ $type == 'sj' ? 'PENGIRIMAN' : 'LUNAS' }}</td>
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
                <th width="50%">NAMA BARANG / DESKRIPSI</th>
                <th width="15%" class="text-center">QTY</th>
                @if($type == 'invoice')
                <th width="15%" class="text-right">HARGA</th>
                <th width="15%" class="text-right">TOTAL</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->detail as $index => $item)
            <tr>
                <td width="5%" class="text-center">{{ $index + 1 }}</td>
                <td width="50%" class="uppercase">{{ $item->produk->nama }}</td>
                <td width="10%" class="text-">{{ $item->qty + 0 }} {{ $item->produk->satuan }}</td>
                @if($type == 'invoice')
                <td width="15%" class="text-">{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td width="15%" class="text-">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($type == 'invoice')
    <div class="summary-container">
        <table class="summary-table">
            <tr>
                <td class="bold">JUMLAH TOTAL</td>
                <td class="bold text-left  border-total">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    @endif

    <div style="clear: both;"></div>



    {{-- <div style="margin-top: 10px; font-size: 8pt; font-style: italic;">
        * Barang yang sudah dibeli tidak dapat ditukar/dikembalikan tanpa perjanjian.
    </div> --}}
</body>
</html>