<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan IKN COCO COIR</title>
    <style>
        /* Mengurangi line-height agar teks lebih rapat */
        body { font-family: sans-serif; font-size: 10px; color: #333; line-height: 1.2; }
        
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 16px; }
        .header h3 { margin: 2px 0; font-size: 13px; }
        .header p { margin: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        /* Padding dikurangi dari 8px menjadi 3px 5px */
        th, td { border: 1px solid #999; padding: 3px 5px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Summary box dibuat lebih rapat */
        .summary-box { float: right; width: 220px; margin-top: 10px; }
        .summary-box table { border: none; margin-top: 0; }
        .summary-box table td { border: none; padding: 2px 0; font-size: 11px; }
        
        .font-bold { font-weight: bold; border-top: 1.5px solid #333 !important; }
        
        /* Mengurangi jarak daftar produk */
        .product-detail { margin: 0; padding: 0; list-style: none; }
    </style>
</head>
<body>
    <div class="header">
        <h2>IKN COCO COIR</h2>
        <h3>LAPORAN PENJUALAN</h3>
        <p>Periode: 
            <strong>{{ \Carbon\Carbon::parse($start_date)->translatedFormat('d F Y') }}</strong> 
            s/d 
            <strong>{{ \Carbon\Carbon::parse($end_date)->translatedFormat('d F Y') }}</strong>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="25px" class="text-center">No</th>
                <th width="90px">Tanggal</th>
                <th width="80px">No. Nota</th>
                <th width="100px">Client</th>
                <th>Rincian Barang</th>
                <th width="90px" class="text-right">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $row)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</td>
                <td>#PJ-{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $row->client->nama ?? 'Umum' }}</td>
                <td>
                    @foreach($row->detail as $item)
                        • {{ $item->produk->nama ?? 'Produk Tidak Ditemukan' }} 
                        ({{ $item->qty }} x {{ number_format($item->harga, 0, ',', '.') }})<br>
                    @endforeach
                </td>
                <td class="text-right"><strong>{{ number_format($row->total, 0, ',', '.') }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data penjualan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table>
            <tr class="font-bold">
                <td width="100px">TOTAL OMZET</td>
                <td class="text-right">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>
    
    <div style="margin-top: 30px; text-align: right; font-size: 9px; color: #666;">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body> 
</html>