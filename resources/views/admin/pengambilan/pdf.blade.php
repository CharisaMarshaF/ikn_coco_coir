<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengambilan Bahan</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .title { font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .subtitle { font-size: 10pt; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 10px; vertical-align: top; }
        th { background: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 9pt; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Gaya untuk list bahan di dalam satu kotak transaksi */
        .bahan-list { margin: 0; padding: 0; list-style: none; }
        .bahan-item { border-bottom: 1px solid #eee; padding: 2px 0; font-size: 9pt; }
        .bahan-item:last-child { border-bottom: none; }
        
        .summary-box { margin-top: 30px; width: 50%; float: right; }
        .summary-title { font-weight: bold; background: #eee; text-align: center; border: 1px solid #999; padding: 5px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Laporan Pengambilan Bahan Baku</div>
        <div class="subtitle">
            @if($dari && $sampai)
                Periode: {{ date('d/m/Y', strtotime($dari)) }} - {{ date('d/m/Y', strtotime($sampai)) }}
            @else
                Periode: Semua Data
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">NO</th>
                <th width="80">TANGGAL</th>
                <th>DAFTAR BAHAN YANG DIAMBIL</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            {{-- GROUPING DATA BERDASARKAN ID PENGAMBILAN --}}
            @foreach($data->groupBy('pengambilan_id') as $id_pengambilan => $details)
            @php $master = $details->first()->pengambilan; @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ date('d/m/Y', strtotime($master->tanggal)) }}</td>
                <td>
                    <div class="bahan-list">
                        @foreach($details as $detail)
                        <div class="bahan-item">
                            • {{ $detail->bahan->nama ?? 'Bahan Dihapus' }} 
                            <strong>({{ (float)$detail->qty }} {{ $detail->bahan->satuan ?? '' }})</strong>
                        </div>
                        @endforeach
                    </div>
                </td>
                <td class="subtitle italic">{{ $master->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clear"></div>

    {{-- REKAPITULASI (TOTAL SEMUA) --}}
    <div class="summary-box">
        <div class="summary-title">TOTAL AKUMULASI PENGGUNAAN</div>
        <table>
            <thead>
                <tr>
                    <th>Nama Bahan</th>
                    <th class="text-right">Total Keluar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary as $s)
                <tr>
                    <td>{{ $s['nama'] }}</td>
                    <td class="text-right"><strong>{{ (float)$s['total_qty'] }} {{ $s['satuan'] }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>