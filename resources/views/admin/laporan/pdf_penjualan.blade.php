<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan IKN COCO COIR</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-size: 10px; }
        .text-right { text-align: right; }
        .summary-box { float: right; width: 250px; margin-top: 20px; }
        .summary-box table td { border: none; padding: 4px 0; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>IKN COCO COIR</h2>
        <h3>LAPORAN PENJUALAN</h3>
        <p>Periode: {{ $start_date }} s/d {{ $end_date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Nota</th>
                <th>Client</th>
                <th>Status</th>
                <th class="text-right">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $row)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
                <td>#PJ-{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $row->client->nama ?? 'Umum' }}</td>
                <td>{{ strtoupper($row->status) }}</td>
                <td class="text-right">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <table>
            <tr>
                <td>Total Transaksi</td>
                <td class="text-right">{{ $summary['count_transaksi'] }}</td>
            </tr>
            <tr>
                <td>Transaksi Cancel</td>
                <td class="text-right">Rp {{ number_format($summary['total_cancel'], 0, ',', '.') }}</td>
            </tr>
            <tr class="font-bold">
                <td>TOTAL OMZET BERSIH</td>
                <td class="text-right">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>