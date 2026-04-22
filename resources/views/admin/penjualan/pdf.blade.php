<!DOCTYPE html>
<html>
<head>
    <title>{{ $type == 'sj' ? 'Surat Jalan' : 'Invoice' }} - {{ $penjualan->id }}</title>
    <style>
        body { font-family: 'Courier', sans-serif; font-size: 12px; margin: 0; padding: 0; }
        .header { width: 100%; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; text-decoration: underline; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; }
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .main-table th { border-bottom: 1px solid #000; border-top: 1px solid #000; padding: 5px; text-align: left; }
        .main-table td { padding: 5px; border-bottom: 0.5px dotted #ccc; }
        .total-section { float: right; width: 250px; text-align: right; }
        .footer { margin-top: 50px; width: 100%; }
        .footer td { text-align: center; width: 33%; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td class="title">{{ $type == 'sj' ? 'SURAT JALAN' : 'INVOICE PENJUALAN' }}</td>
            <td class="text-right">
                <span class="bold">IKN COCO COIR</span><br>
                Jumantono, Karanganyar
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td style="width: 60%;">
                <strong>Kepada Yth:</strong><br>
                {{ $penjualan->client->nama ?? 'Pembeli Umum' }}<br>
                {{ $penjualan->client->alamat ?? '-' }}<br>
                Telp: {{ $penjualan->client->telp ?? '-' }}
            </td>
            <td class="text-">
                No. Doc : {{ $type == 'sj' ? $penjualan->suratJalan->nomor : $penjualan->invoice->nomor }}<br>
                Tanggal : {{ date('d/m/Y', strtotime($penjualan->tanggal)) }}<br>
                Status  : {{ $type == 'sj' ? 'DIKIRIM' : 'LUNAS' }}
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 40px;">NO</th>
                <th>DESKRIPSI BARANG</th>
                <th class="text-center" style="width: 80px;">QTY</th>
                @if($type == 'invoice')
                <th class="text-right">HARGA</th>
                <th class="text-right">SUBTOTAL</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->detail as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->produk->nama }}</td>
                <td class="text-center">{{ $item->qty }}</td>
                @if($type == 'invoice')
                <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($type == 'invoice')
    <div class="total-section">
        <table style="width: 100%;">
            <tr>
                <td class="bold">TOTAL AKHIR:</td>
                <td class="bold">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    @endif
{{-- 
    <table class="footer">
        <tr>
            <td>
                Tanda Terima,<br><br><br><br>
                (....................)
            </td>
            <td></td>
            <td>
                Hormat Kami,<br><br><br><br>
                (....................)
            </td>
        </tr>
    </table> --}}
</body>
</html>