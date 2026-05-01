<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembelian IKN COCO COIR</title>
    <style>
        /* Mengurangi font-size dan line-height agar teks lebih rapat */
        body { font-family: sans-serif; font-size: 10px; color: #333; line-height: 1.2; }
        
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; font-weight: bold; }
        .header span { font-size: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        /* Padding dikurangi drastis menjadi 3px 5px */
        th, td { border: 1px solid #999; padding: 3px 5px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .summary-container { margin-top: 10px; width: 100%; }
        .summary-box { float: right; width: 220px; }
        .summary-box table td { border: none; padding: 2px 0; }
        
        /* Baris total dibuat lebih padat */
        .total-row { font-size: 11px; font-weight: bold; color: #000; border-top: 1.5px solid #333 !important; }
        
        .footer { margin-top: 30px; text-align: right; font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>IKN COCO COIR</h2>
        <p>LAPORAN PEMBELIAN BAHAN BAKU</p>
        <span>Periode: <strong>{{ \Carbon\Carbon::parse($start_date)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($end_date)->translatedFormat('d F Y') }}</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="25px" class="text-center">No</th>
                <th width="90px">Tanggal</th>
                <th width="110px">Supplier</th>
                <th>Barang / Bahan Baku</th>
                <th width="90px" class="text-right">Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ $item->supplier->nama ?? 'Tanpa Supplier' }}</td>
                <td>
                    @foreach($item->detail as $detail)
                        • {{ $detail->bahan->nama }} ({{ $detail->jumlah }} {{ $detail->bahan->satuan }})<br>
                    @endforeach
                </td>
                <td class="text-right"><strong>{{ number_format($item->total, 0, ',', '.') }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data pembelian pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-container">
        <div class="summary-box">
            <table>
                <tr class="total-row">
                    <td>TOTAL PENGELUARAN</td>
                    <td class="text-right">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>