<!DOCTYPE html>
<html>

<head>
    <title>{{ $type == 'sj' ? 'Surat Jalan' : 'Invoice' }} - {{ $penjualan->id }}</title>
    <style>
        @page {
            size: {{ $type == 'sj' ? '148mm 210mm' : '210mm 148mm' }};
            margin: 8mm 10mm;
        }

        body {
            font-family: 'Courier', monospace;
            font-size: 9pt;
            margin: 0;
            padding: 0;
            color: #000;
            line-height: 1.2;
        }

        #watermark {
            position: fixed;
            top: 20%;
            left: 25%;
            width: 250px;
            opacity: 0.06;
            z-index: -1000;
        }

        /* HEADER SECTION */
        .header-table {
            width: 100%;
            border-bottom: 2px double #000;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        .logo-top {
            width: 55px;
            height: auto;
        }

        .company-name {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .company-info {
            font-size: 7pt;
            line-height: 1.1;
        }

        .document-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: right;
            vertical-align: middle;
        }

        /* INFO SECTION (YTH & NOTA) */
        .info-container {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .info-container td {
            vertical-align: top;
            padding: 0;
        }

        .label-yth {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
            display: block;
            font-size: 8.5pt;
        }

        /* Sub-table untuk merapikan titik dua */
        .sub-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sub-info-table td {
            font-size: 8.5pt;
            padding: 1px 0;
        }

        /* TABLE UTAMA */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .main-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 4px;
            text-align: left;
            font-size: 8.5pt;
        }

        .main-table td {
            padding: 5px 4px;
            font-size: 8.5pt;
            vertical-align: top;
            border-bottom: 1px dotted #ccc;
        }

        /* SUMMARY */
        .summary-wrapper {
            width: 100%;
            margin-top: 5px;
        }

        .summary-table {
            float: right;
            width: 45%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 3px 5px;
            font-size: 9pt;
        }

        .border-total {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        /* RETURN SECTION */
        .return-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border: 1px dashed #000;
        }

        .return-header-title {
            background-color: #f9f9f9;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 8pt;
            border-bottom: 1px dashed #000;
            color: #c00;
        }

        .return-table th {
            font-size: 8pt;
            padding: 3px 4px;
            border-bottom: 1px solid #eee;
            text-align: left;
            color: #555;
        }

        .return-table td {
            font-size: 8pt;
            padding: 3px 4px;
            color: #444;
        }

        /* FOOTER SIGN */
        .footer-sign {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .footer-sign td {
            text-align: center;
            width: 33.3%;
            font-size: 8.5pt;
            vertical-align: top;
        }

        .sign-space {
            height: 45px;
        }

        /* UTILS */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>

<body>


    <!-- Header Logo & Title -->
    <table class="header-table">
        <tr>
            <td width="10%">
                @if ($company && $company->logo)
                    <img src="{{ base_path('uploads/logo/' . basename($company->logo)) }}" class="logo-top">
                @endif
            </td>
            <td width="55%">
                <h1 class="company-name">{{ $company->nama_cv ?? 'NAMA PERUSAHAAN' }}</h1>
                <div class="company-info">
                    {{ $company->alamat ?? '-' }}<br>
                    Telp: {{ $company->telepon ?? '-' }} {{ $company->email ? ' | Email: ' . $company->email : '' }}<br>
                    {{ $company->website }}

                </div>
            </td>
            <td class="document-title">
                {{ $type == 'sj' ? 'SURAT JALAN' : 'INVOICE' }}
            </td>
        </tr>
    </table>

    <!-- Info Pelanggan & Nota -->
    <table class="info-container">
        <tr>
            <!-- Kolom Kiri: Pelanggan -->
            <td width="55%">
                <span class="label-yth">KEPADA YTH:</span>
                <table class="sub-info-table">
                    <tr>
                        <td width="22%">Penerima</td>
                        <td width="3%">:</td>
                        <td class="bold uppercase">{{ $penjualan->client->nama ?? 'PELANGGAN UMUM' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $penjualan->client->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Telepon</td>
                        <td>:</td>
                        <td>{{ $penjualan->client->telp ?? '-' }}</td>
                    </tr>
                </table>
            </td>

            <!-- Kolom Kanan: Detail Nota -->
            <td width="45%">
                <div style="margin-left: 20px;">
                    <span class="label-yth" style="text-decoration: none; color: transparent;">.</span>
                    <table class="sub-info-table">
                        <tr>
                            <td width="35%">No. Nota</td>
                            <td width="5%">:</td>
                            <td class="bold">{{ $type == 'sj' ? $penjualan->suratJalan->nomor ?? '-' : $penjualan->invoice->nomor ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td>{{ date('d/m/Y', strtotime($penjualan->tanggal)) }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:</td>
                            <td class="uppercase bold" style="font-size: 7.5pt;">
                                {{ $penjualan->status == 'return' ? 'TERJADI RETURN' : ($type == 'sj' ? 'PENGIRIMAN' : 'LUNAS') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Barang Utama -->
    <table class="main-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">NO</th>
                <th width="{{ $type == 'invoice' ? '45%' : '75%' }}">NAMA BARANG</th>
                <th width="15%" class="text-center">QTY AWAL</th>
                @if ($type == 'invoice')
                    <th width="15%" class="text-right">HARGA</th>
                    <th width="20%" class="text-right">TOTAL</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php $totalOriginal = 0; @endphp
            @foreach ($penjualan->detail as $index => $item)
                @php
                    $qtyAwal = $item->qty;
                    $subtotalAwal = $qtyAwal * $item->harga;
                    $totalOriginal += $subtotalAwal;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="uppercase">{{ $item->produk->nama }}</td>
                    <td class="text-">{{ $qtyAwal + 0 }} {{ $item->produk->satuan }}</td>
                    @if ($type == 'invoice')
                        <td class="text-">{{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="text-">{{ number_format($subtotalAwal, 0, ',', '.') }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary Invoice -->
    @if ($type == 'invoice')
        <div class="summary-wrapper clearfix">
            <table class="summary-table">
                <tr>
                    <td class="bold text-right">TOTAL TAGIHAN:</td>
                    <td class="bold text- border-total" style="width: 50%;">
                        Rp {{ number_format($totalOriginal, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <div style="clear: both;"></div>

    <!-- Section Return -->
    @php $hasReturn = $penjualan->detail->sum('qty_return') > 0; @endphp
    @if ($hasReturn)
        <table class="return-table">
            <thead>
                <tr>
                    <th colspan="{{ $type == 'invoice' ? '5' : '3' }}" class="return-header-title">
                        INFORMASI PENGEMBALIAN BARANG (RETURN)
                    </th>
                </tr>
                <tr>
                    <th width="5%" class="text-center">NO</th>
                    <th width="{{ $type == 'invoice' ? '45%' : '75%' }}">NAMA BARANG</th>
                    <th width="15%" class="text-center">QTY RTN</th>
                    @if($type == 'invoice')
                        <th width="15%" class="text-right">HARGA</th>
                        <th width="20%" class="text-right">SUBTOTAL RTN</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @php $noReturn = 1; @endphp
                @foreach ($penjualan->detail as $item)
                    @if ($item->qty_return > 0)
                        <tr>
                            <td class="text-center">{{ $noReturn++ }}</td>
                            <td class="uppercase">{{ $item->produk->nama }}</td>
                            <td class="text-">{{ $item->qty_return + 0 }} {{ $item->produk->satuan }}</td>
                            @if($type == 'invoice')
                                <td class="text-">{{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-">{{ number_format($item->qty_return * $item->harga, 0, ',', '.') }}</td>
                            @endif
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <div style="font-size: 7pt; margin-top: 3px; color: #666; font-style: italic;">
            * Saldo return akan dikompensasikan pada transaksi berikutnya.
        </div>
    @endif

    <!-- Tanda Tangan -->
    <table class="footer-sign">
        <tr>
            <td>
                ( {{ auth()->check() ? auth()->user()->name : '.................' }} )
                <div class="sign-space"></div>
                (.................)
            </td>
            <td>
                {{ $type == 'sj' ? 'Sopir/Logistik,' : '' }}
                @if ($type == 'sj')
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

    <!-- Footer Note -->
    <div style="margin-top: 15px; font-size: 7pt; font-style: italic; border-top: 1px solid #000; padding-top: 5px;">
        * Cetakan komputer, sah tanpa tanda tangan basah jika ada stempel resmi.<br>
        * {{ $type == 'sj' ? 'Harap periksa barang saat diterima.' : 'Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada perjanjian.' }}
    </div>
</body>

</html>