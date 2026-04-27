<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembelian IKN COCO COIR</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f9f9f9; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 9px; text-transform: uppercase; }
        
        .summary-container { margin-top: 30px; width: 100%; }
        .summary-box { float: right; width: 250px; }
        .summary-box table td { border: none; padding: 3px 0; }
        .total-row { font-size: 13px; font-weight: bold; color: #000; border-top: 1px solid #333 !important; }
        
        .footer { margin-top: 50px; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>IKN COCO COIR</h2>
        <p>LAPORAN PEMBELIAN BAHAN BAKU</p>
        <span>Periode: <strong>{{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30px" class="text-center">No</th>
                <th width="80px">Tanggal</th>
                <th width="100px">No. Faktur</th>
                <th>Supplier</th>
                <th>Status Bayar</th>
                <th class="text-right">Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                <td>{{ $item->no_faktur ?? $item->id }}</td>
                <td>{{ $item->supplier->nama ?? 'Tanpa Supplier' }}</td>
                <td>{{ strtoupper($item->status_pembayaran) }}</td>
                <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-container">
        <div class="summary-box">
            <table>
                <tr>
                    <td>Total Transaksi</td>
                    <td class="text-right">{{ $summary['count_transaksi'] }}</td>
                </tr>
                
                <tr class="total-row">
                    <td>TOTAL PENGELUARAN</td>
                    <td class="text-right">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>