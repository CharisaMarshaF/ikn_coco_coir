<!DOCTYPE html>
<html>

<head>
    <title>{{ $type == 'sj' ? 'Surat Jalan' : 'Invoice' }} - {{ $penjualan->id }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm;
        }

        body {
            font-family: 'Courier', monospace;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            color: #000;
            line-height: 1.3;
        }

        /* HEADER SECTION */
        .header-table {
            width: 100%;
            border-bottom: 2px double #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .logo-top {
            width: 70px;
            height: auto;
        }

        .company-name {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .company-info {
            font-size: 8.5pt;
            line-height: 1.2;
        }

        .document-title {
            font-size: 20pt;
            font-weight: bold;
            text-align: right;
            vertical-align: middle;
        }

        /* INFO SECTION */
        .info-container {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }

        .info-container td {
            vertical-align: top;
            padding: 0;
        }

        .label-yth {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 6px;
            display: block;
            font-size: 10pt;
        }

        .sub-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sub-info-table td {
            font-size: 10pt;
            padding: 2px 0;
        }

        /* TABLE UTAMA */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .main-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 10px 5px;
            font-size: 10pt;
            background-color: #fcfcfc;
        }

        .main-table td {
            padding: 10px 5px;
            font-size: 10pt;
            vertical-align: top;
            border-bottom: 1px dotted #ccc;
        }

        /* SUMMARY */
        .summary-wrapper {
            width: 100%;
            margin-top: 10px;
        }

        .summary-table {
            float: right;
            width: 40%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 5px;
            font-size: 11pt;
        }

        .border-total {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        /* RETURN SECTION */
        .return-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            border: 1px dashed #000;
        }

        .return-header-title {
            background-color: #f9f9f9;
            padding: 8px;
            font-weight: bold;
            font-size: 9pt;
            border-bottom: 1px dashed #000;
            color: #c00;
        }

        .return-table th {
            font-size: 9pt;
            padding: 5px;
            border-bottom: 1px solid #eee;
        }

        .return-table td {
            font-size: 9pt;
            padding: 5px;
        }

        /* FOOTER SIGN */
        .footer-sign {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }

        .footer-sign td {
            text-align: center;
            width: 33.3%;
            font-size: 10pt;
            vertical-align: top;
        }

        .sign-space {
            height: 70px;
        }

        /* UTILS */
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td width="12%">
                @if ($company && $company->logo)
                    <img src="{{ base_path('uploads/logo/' . basename($company->logo)) }}" class="logo-top">
                @endif
            </td>
            <td width="48%">
                <h1 class="company-name">{{ $company->nama_cv ?? 'PT INTER KARANGANYAR NUSANTARA' }}</h1>
                <div class="company-info">
                    {{ $company->alamat ?? '-' }}<br>
                    Telp: {{ $company->telepon ?? '-' }} {{ $company?->email ? ' | Email: ' . $company->email : '' }}<br>
                    {{ $company->website ?? '-' }}
                </div>
            </td>
            <td class="document-title">
                {{ $type == 'sj' ? 'SURAT JALAN' : 'INVOICE' }}
            </td>
        </tr>
    </table>

    <table class="info-container">
        <tr>
            <td width="55%">
                <span class="label-yth">KEPADA YTH:</span>
                <table class="sub-info-table">
                    <tr>
                        <td width="25%">Penerima</td>
                        <td width="5%">:</td>
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

            <td width="45%">
                <div style="margin-left: 30px;">
                    <span class="label-yth" style="text-decoration: none; color: transparent;">.</span>
                    <table class="sub-info-table">
                        <tr>
                            <td width="40%">No. Nota</td>
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
                            <td class="uppercase bold">
                                {{ $penjualan->status == 'return' ? 'TERJADI RETURN' : ($type == 'sj' ? 'PENGIRIMAN' : 'LUNAS') }}
                            </td>
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
                <th width="{{ $type == 'invoice' ? '40%' : '80%' }}" class="text-left">NAMA BARANG</th>
                <th width="15%" class="text-center">QTY</th>
                @if ($type == 'invoice')
                    <th width="20%" class="text-right">HARGA</th>
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
                    <td class="text-left uppercase">{{ $item->produk->nama }}</td>
                    <td class="text-center">{{ $qtyAwal + 0 }} {{ $item->produk->satuan }}</td>
                    @if ($type == 'invoice')
                        <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($subtotalAwal, 0, ',', '.') }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($type == 'invoice')
        <div class="summary-wrapper clearfix">
            <table class="summary-table">
                <tr>
                    <td class="bold text-right">TOTAL TAGIHAN:</td>
                    <td class="bold text-right border-total">
                        Rp {{ number_format($totalOriginal, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <div style="clear: both;"></div>

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
                    <th width="{{ $type == 'invoice' ? '40%' : '80%' }}" class="text-left">NAMA BARANG</th>
                    <th width="15%" class="text-center">QTY RTN</th>
                    @if($type == 'invoice')
                        <th width="20%" class="text-right">HARGA</th>
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
                            <td class="text-left uppercase">{{ $item->produk->nama }}</td>
                            <td class="text-center">{{ $item->qty_return + 0 }} {{ $item->produk->satuan }}</td>
                            @if($type == 'invoice')
                                <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->qty_return * $item->harga, 0, ',', '.') }}</td>
                            @endif
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <div style="font-size: 8pt; margin-top: 5px; color: #666; font-style: italic;">
            * Saldo return akan dikompensasikan pada transaksi berikutnya.
        </div>
    @endif

    <table class="footer-sign">
        <tr>
            <td>
                Admin,
                <div class="sign-space"></div>
                ( {{ auth()->check() ? auth()->user()->name : '.................' }} )
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

    <div style="margin-top: 40px; font-size: 8.5pt; font-style: italic; border-top: 1px solid #000; padding-top: 10px;">
        * Cetakan komputer, sah tanpa tanda tangan basah jika ada stempel resmi.<br>
        * {{ $type == 'sj' ? 'Harap periksa barang saat diterima.' : 'Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada perjanjian.' }}
    </div>
</body>

</html>