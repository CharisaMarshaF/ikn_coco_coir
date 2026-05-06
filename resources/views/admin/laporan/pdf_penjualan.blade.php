<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan IKN COCO COIR</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: sans-serif; font-size: 9px; color: #333; line-height: 1.3; }
        
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 16px; }
        .header h3 { margin: 2px 0; font-size: 13px; }
        .header p { margin: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed; }
        th, td { border: 1px solid #777; padding: 4px 5px; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 8.5px; text-align: center; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .summary-container { margin-top: 15px; width: 100%; }
        .summary-box { float: right; width: 250px; }
        .summary-box table { border: none; }
        .summary-box table td { border: none; padding: 3px 0; font-size: 11px; }
        .total-row { border-top: 1.5px solid #000 !important; font-weight: bold; }

        .item-row { border-bottom: 1px solid #eee; padding: 2px 0; }
        .item-row:last-child { border-bottom: none; }
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
                <th width="5%">No</th>
                <th width="15%">Tanggal</th> {{-- Lebar sedikit ditambah agar teks bulan tidak terpotong --}}
                <th width="15%">No. Nota</th>
                <th width="20%">Client</th>
                <th width="30%">Produk</th>
                <th  width="15%">Qty</th>
                <th width="15%">Harga</th>
                <th width="20%">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $row)
                @php $rowCount = $row->detail->count(); @endphp
                @foreach($row->detail as $index => $item)
                <tr>
                    @if($index === 0)
                        <td width="5%" rowspan="{{ $rowCount }}" class="text-center">{{ $key + 1 }}</td>
                        <td rowspan="{{ $rowCount }}" class="text-center">
                            {{-- UBAHAN DI SINI: format j F Y menghasilkan 1 May 2026 --}}
                            {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('j F Y') }}
                        </td>
                        <td rowspan="{{ $rowCount }}" class="text-center">
                            #PJ-{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        <td rowspan="{{ $rowCount }}">
                            {{ $row->client->nama ?? 'Umum' }}
                        </td>
                    @endif

                    <td>{{ $item->produk->nama ?? 'Produk Tidak Ditemukan' }}</td>
                    <td  width="5%" class="text-center">{{ number_format($item->qty, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>

                    @if($index === 0)
                        <td rowspan="{{ $rowCount }}" class="text-right font-bold">
                            {{ number_format($row->total, 0, ',', '.') }}
                        </td>
                    @endif
                </tr>
                @endforeach
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data penjualan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-container">
        <div class="summary-box">
            <table>
                <tr class="total-row">
                    <td width="120px">TOTAL OMZET</td>
                    <td class="text-right">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
    </div>
    
</body> 
</html>