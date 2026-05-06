<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Histori Transaksi - {{ $client->nama }}</title>
    <style>
        @page { margin: 1cm; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9px; 
            color: #333; 
            line-height: 1.4; 
            margin: 0;
            padding: 0;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            padding-bottom: 8px;
            border-bottom: 2px solid #2c3e50;
        }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 16px; color: #2c3e50; }
        .header h3 { margin: 4px 0; font-size: 12px; color: #555; letter-spacing: 1px; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; background-color: #fff; }
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
        td { border: 1px solid #7f8c8d; padding: 6px 6px; vertical-align: top; word-wrap: break-word; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-light { background-color: #f9fbfd; }
        .row-total-footer { background-color: #D9E9FF; font-weight: bold; font-size: 10px; }
        .footer-stamp { margin-top: 15px; font-size: 8px; color: #999; font-style: italic; }
        .unit-text { font-size: 8px; color: #7f8c8d; margin-left: 2px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $konfigurasi->nama_cv ?? 'IKN COCO COIR' }}</h2>
        <h3>LAPORAN HISTORI TRANSAKSI CLIENT</h3>
        <p>Client: <strong>{{ $client->nama }}</strong> | Periode: 
            <strong>{{ \Carbon\Carbon::parse($start_date)->translatedFormat('j F Y') }}</strong> 
            s/d 
            <strong>{{ \Carbon\Carbon::parse($end_date)->translatedFormat('j F Y') }}</strong>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="14%">Tanggal</th>
                <th width="14%">No. Nota</th>
                <th width="20%">Produk</th>
                <th width="10%">Qty</th>
                <th width="12%">Harga</th>
                <th width="12%">Subtotal</th>
                <th width="14%">Total Nota</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $row)
                @php 
                    $rowCount = $row->detail->count(); 
                    $rowClass = ($key % 2 == 0) ? '' : 'bg-light';
                @endphp
                @foreach($row->detail as $index => $item)
                <tr class="{{ $rowClass }}">
                    @if($index === 0)
                        <td rowspan="{{ $rowCount }}" class="text-center font-bold">{{ $key + 1 }}</td>
                        <td rowspan="{{ $rowCount }}" class="text-center">
                            {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('j F Y') }}
                        </td>
                        <td rowspan="{{ $rowCount }}" class="text-center font-bold" style="color: #2c3e50;">
                             #PJ-{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                    @endif

                    <td>{{ $item->produk->nama ?? 'N/A' }}</td>
                    <td class="text-right">
                        {{ number_format($item->qty, 0, ',', '.') }}
                        <span class="unit-text">{{ $item->produk->satuan ?? '' }}</span>
                    </td>
                    <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>

                    @if($index === 0)
                        <td rowspan="{{ $rowCount }}" class="text-right font-bold">
                            {{ number_format($row->total, 0, ',', '.') }}
                        </td>
                    @endif
                </tr>
                @endforeach
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada histori transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="row-total-footer">
                <td colspan="7" class="text-right" style="padding: 8px;">TOTAL AKUMULASI BELANJA CLIENT</td>
                <td class="text-right" style="padding: 8px;">
                     {{ number_format($total_omzet, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-stamp">
        Dicetak otomatis oleh Sistem pada: {{ now()->translatedFormat('j F Y') }} WIB
    </div>
</body>
</html>