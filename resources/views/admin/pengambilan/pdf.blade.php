<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pengambilan Bahan Baku</title>
    <style>
        @page { 
            margin: 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9px; 
            color: #333; 
            line-height: 1.4; 
            margin: 0;
            padding: 0;
        }

        /* Header Section */
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            padding-bottom: 8px;
            border-bottom: 2px solid #2c3e50;
        }
        .header h2 { 
            margin: 0; 
            text-transform: uppercase; 
            font-size: 16px; 
            color: #2c3e50;
        }
        .header h3 { 
            margin: 4px 0; 
            font-size: 12px; 
            color: #555;
            letter-spacing: 1px;
        }
        .header p { 
            margin: 2px 0; 
            font-size: 10px;
            color: #666;
        }

        /* Table Styling */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed;
            background-color: #fff;
            margin-bottom: 20px;
        }
        
        th { 
            background-color: #D9E9FF;
            color: #2c3e50;
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 8.5px; 
            padding: 8px 4px;
            border: 1px solid #7f8c8d;
            vertical-align: middle;
        }

        td { 
            border: 1px solid #7f8c8d; 
            padding: 6px 6px; 
            vertical-align: top; /* Align atas sesuai permintaan */
            word-wrap: break-word; 
        }

        /* Helpers */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* Row Highlights */
        .bg-light { background-color: #f9fbfd; }
        
        h4 { 
            margin: 15px 0 5px 0; 
            font-size: 10px; 
            color: #2c3e50; 
            text-transform: uppercase;
        }

        .unit-text {
            font-size: 8px;
            color: #7f8c8d;
            margin-left: 2px;
        }

        /* Footer Stamp */
        .footer-stamp {
            margin-top: 15px;
            font-size: 8px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $konfigurasi->nama_cv ?? 'IKN COCO COIR' }}</h2>
        <h3>LAPORAN PENGAMBILAN BAHAN BAKU</h3>
        <p>Periode: 
            <strong>{{ \Carbon\Carbon::parse($dari)->translatedFormat('j F Y') }}</strong> 
            s/d 
            <strong>{{ \Carbon\Carbon::parse($sampai)->translatedFormat('j F Y') }}</strong>
        </p>
    </div>
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="16%">Tanggal</th>
                <th width="50%">Nama Bahan</th>
                <th width="30%">Qty Ambil</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $groupedData = $data->groupBy('pengambilan_id');
                $no = 1;
            @endphp

            @foreach($groupedData as $pengambilanId => $items)
                @php $rowClass = ($no % 2 == 0) ? 'bg-light' : ''; @endphp
                @foreach($items as $index => $row)
                <tr class="{{ $rowClass }}">
                    @if($index === 0)
                        <td class="text-center font-bold" rowspan="{{ $items->count() }}">{{ $no++ }}</td>
                        <td class="text-center" rowspan="{{ $items->count() }}">
                            {{ \Carbon\Carbon::parse($row->pengambilan->tanggal)->translatedFormat('j F Y') }}
                        </td>
                    @endif

                    <td>
                        {{ $row->bahan ? ($row->bahan->trashed() ? $row->bahan->nama . ' (Dihapus)' : $row->bahan->nama) : 'N/A' }}
                    </td>
                    <td class="text-right">
                        <span class="font-bold">{{ number_format($row->qty, 2, ',', '.') }}</span>
                        <span class="unit-text">{{ $row->bahan->satuan ?? '-' }}</span>
                    </td>
                </tr>
                @endforeach
            @endforeach
            @if($data->isEmpty())
            <tr>
                <td colspan="4" class="text-center" style="padding: 20px;">Tidak ada data pengambilan pada periode ini.</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div style="width: 50%;">
        <h4>Total Akumulasi Bahan</h4>
        <table>
            <thead>
                <tr>
                    <th width="60%">Nama Bahan</th>
                    <th width="30%">Total Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary as $index => $item)
                <tr class="{{ $index % 2 != 0 ? 'bg-light' : '' }}">
                    <td>{{ $item['nama'] }}</td>
                    <td class="text-right">
                        <span class="font-bold">{{ number_format($item['total_qty'], 2, ',', '.') }}</span>
                        <span class="unit-text">{{ $item['satuan'] }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer-stamp">
        Dicetak otomatis oleh Sistem pada: {{ now()->translatedFormat('j F Y') }}
    </div>
</body>
</html>