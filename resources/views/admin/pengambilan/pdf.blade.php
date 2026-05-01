<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengambilan Bahan</title>
    <style>
        /* Mengatur font dan line-height agar teks lebih rapat */
        body { font-family: sans-serif; font-size: 10px; color: #333; line-height: 1.2; }
        
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header h3 { margin: 2px 0; font-size: 12px; }
        .header p { margin: 2px 0; font-size: 10px; }

        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        /* Padding dikurangi menjadi 3px 5px untuk efisiensi ruang */
        th, td { border: 1px solid #999; padding: 3px 5px; text-align: left; vertical-align: top; }
        th { background: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        h4 { margin: 10px 0 5px 0; font-size: 11px; text-transform: uppercase; }

        /* Summary box dibuat lebih ringkas */
        .summary-box { width: 55%; margin-top: 15px; }
        .summary-box table td { padding: 2px 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $konfigurasi->nama_perusahaan ?? 'IKN COCO COIR' }}</h2>
        <h3>LAPORAN PENGAMBILAN BAHAN BAKU</h3>
        <p>
            Periode: 
            <strong>{{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }}</strong> 
            s/d 
            <strong>{{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}</strong>
        </p>
    </div>

    <h4>Rincian Transaksi</h4>
    <table>
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="100">Tanggal</th>
                <th>Nama Bahan</th>
                <th class="text-right" width="100">Qty Ambil</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $groupedData = $data->groupBy('pengambilan_id');
                $no = 1;
            @endphp

            @foreach($groupedData as $pengambilanId => $items)
                @foreach($items as $index => $row)
                <tr>
                    {{-- Rowspan untuk menggabungkan kolom No dan Tanggal per ID Pengambilan --}}
                    @if($index === 0)
                        <td class="text-center" rowspan="{{ $items->count() }}">{{ $no++ }}</td>
                        <td rowspan="{{ $items->count() }}">
                            {{ \Carbon\Carbon::parse($row->pengambilan->tanggal)->translatedFormat('d F Y') }}
                            <div style="font-size: 8px; color: #666; margin-top: 2px;">ID: #{{ $row->pengambilan_id }}</div>
                        </td>
                    @endif

                    <td>
                        {{ $row->bahan ? ($row->bahan->trashed() ? $row->bahan->nama . ' (Dihapus)' : $row->bahan->nama) : 'N/A' }}
                    </td>
                    <td class="text-right">
                        <strong>{{ number_format($row->qty, 2) }}</strong> {{ $row->bahan->satuan ?? '-' }}
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <h4>Total Akumulasi Bahan</h4>
        <table>
            <thead>
                <tr>
                    <th>Nama Bahan</th>
                    <th class="text-right" width="100">Total Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary as $item)
                <tr>
                    <td>{{ $item['nama'] }}</td>
                    <td class="text-right">
                        <strong>{{ number_format($item['total_qty'], 2) }}</strong> {{ $item['satuan'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px; text-align: right; font-size: 9px; color: #666;">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>