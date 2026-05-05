<!DOCTYPE html>
<html>
<head>
    <title>Invoice Pengganti - {{ $penjualan->id }}</title>
    <style>
        /* Masukkan semua CSS yang Anda berikan di prompt tadi di sini */
        @page { size: 210mm 148mm; margin: 8mm 10mm; }
        body { font-family: 'Courier', monospace; font-size: 9pt; color: #000; line-height: 1.2; }
        .header-table { width: 100%; border-bottom: 2px double #000; margin-bottom: 10px; padding-bottom: 5px; }
        .company-name { font-size: 12pt; font-weight: bold; text-transform: uppercase; }
        .document-title { font-size: 14pt; font-weight: bold; text-align: right; color: #c00; }
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table th { border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px; }
        .main-table td { padding: 5px; border-bottom: 1px dotted #ccc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .footer-sign { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .footer-sign td { text-align: center; width: 33.3%; }
        .sign-space { height: 40px; }
        .label-resend { 
            background: #000; color: #fff; padding: 2px 5px; font-size: 8pt; font-weight: bold; 
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="60%">
                <h1 class="company-name">{{ $company->nama_cv ?? 'NAMA PERUSAHAAN' }}</h1>
                <div style="font-size: 7pt;">{{ $company->alamat ?? '-' }}</div>
            </td>
            <td class="document-title">
                INVOICE PENGGANTI<br>
                <span class="label-resend">HARGA RP. 0 (REPLACEMENT)</span>
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-bottom: 10px;">
        <tr>
            <td width="55%">
                <span class="bold" style="text-decoration: underline;">KEPADA YTH:</span><br>
                {{ $penjualan->client->nama }}<br>
                {{ $penjualan->client->alamat }}
            </td>
            <td width="45%">
                No. Nota : {{ $penjualan->invoice->nomor ?? '-' }}<br>
                Tanggal  : {{ date('d/m/Y', strtotime($penjualan->tanggal)) }}<br>
                Keterangan: <span style="font-size: 8pt;">{{ $penjualan->keterangan }}</span>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="50%">NAMA BARANG PENGGANTI</th>
                <th width="15%">QTY</th>
                <th width="15%" class="text-right">HARGA</th>
                <th width="15%" class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penjualan->detail as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="bold uppercase">{{ $item->produk->nama }}</td>
                    <td class="text-center">{{ $item->qty + 0 }} {{ $item->produk->satuan }}</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right bold">TOTAL TAGIHAN :</td>
                <td class="text-right bold" style="border-top: 1px solid #000; border-bottom: 1px solid #000;">Rp 0</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 10px; font-size: 8pt; font-style: italic;">
        * Dokumen ini merupakan bukti pengiriman barang pengganti atas transaksi return.<br>
        * Nilai transaksi Rp 0 karena telah dibayarkan pada nota sebelumnya.
    </div>

    <table class="footer-sign">
        <tr>
            <td>Hormat Kami,<br><div class="sign-space"></div>( {{ auth()->user()->name }} )</td>
            <td>Sopir,<br><div class="sign-space"></div>( ................. )</td>
            <td>Penerima,<br><div class="sign-space"></div>( ................. )</td>
        </tr>
    </table>
</body>
</html>