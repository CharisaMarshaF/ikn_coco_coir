<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Histori Transaksi - {{ $supplier->nama }}</title>
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
            vertical-align: top; 
            word-wrap: break-word; 
        }

        /* Helpers */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* Row Highlights */
        .bg-light { background-color: #f9fbfd; }
        
        .row-total-footer {
            background-color: #D9E9FF;
            font-weight: bold;
            font-size: 10px;
        }

        /* Footer Stamp */
        .footer-stamp {
            margin-top: 15px;
            font-size: 8px;
            color: #999;
            font-style: italic;
        }

        .unit-text {
            font-size: 8px;
            color: #7f8c8d;
            margin-left: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $konfigurasi->nama_cv ?? 'IKN COCO COIR' }}</h2>
        <h3>LAPORAN HISTORI TRANSAKSI SUPPLIER</h3>
        <p>
            Supplier: <strong>{{ $supplier->nama }}</strong> | 
            Periode: <strong>{{ \Carbon\Carbon::parse($start)->translatedFormat('j F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($end)->translatedFormat('j F Y') }}</strong>
        </p>
    </div>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <thead>
        <tr>
            <th width="4%">No</th>
            <th width="16%">Tanggal</th>
            <th width="14%">No. Invoice</th>
            <th width="24%">Bahan Baku</th>
            <th width="12%">Qty</th>
            <th width="15%">Harga Satuan</th>
            <th width="15%">Total Nilai</th>
        </tr>
    </thead>
<tbody>
    @forelse($pembelian as $key => $row)
        @php 
            $rowCount = $row->detail->count(); 
            $rowClass = ($key % 2 == 0) ? '' : 'bg-light';
        @endphp
        @foreach($row->detail as $index => $item)
        <tr class="{{ $rowClass }}">
            {{-- Kolom yang di-rowspan: SEKARANG MENGGUNAKAN vertical-align: top --}}
            @if($index === 0)
                <td rowspan="{{ $rowCount }}" class="text-center font-bold" style="vertical-align: top; padding-top: 8px;">
                    {{ $key + 1 }}
                </td>
                <td rowspan="{{ $rowCount }}" class="text-center" style="vertical-align: top; padding-top: 8px;">
                    {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('j F Y') }}
                </td>
                <td rowspan="{{ $rowCount }}" class="text-center font-bold" style="color: #2c3e50; vertical-align: top; padding-top: 8px;">
                    #PB-{{ str_pad($row->id, 5, '0', STR_PAD_LEFT) }}
                </td>
            @endif

            {{-- Kolom Detail Produk --}}
            <td>{{ $item->bahan->nama ?? 'Bahan Tidak Ditemukan' }}</td>
            <td class="text-right">
                <span class="font-bold">{{ number_format($item->qty, 2, ',', '.') }}</span>
                <span class="unit-text">{{ $item->bahan->satuan ?? '' }}</span>
            </td>
            <td class="text-right">
                 {{ number_format($item->harga, 0, ',', '.') }}
            </td>

            {{-- Kolom Total per Invoice: SEKARANG MENGGUNAKAN vertical-align: top --}}
            @if($index === 0)
                <td rowspan="{{ $rowCount }}" class="text-right font-bold" style="vertical-align: top; padding-top: 8px; background-color: #fcfcfc;">
                     {{ number_format($row->total, 0, ',', '.') }}
                </td>
            @endif
        </tr>
        @endforeach
    @empty
        <tr>
            <td colspan="7" class="text-center" style="padding: 30px; color: #7f8c8d;">
                <i>Tidak ada data transaksi pada periode ini.</i>
            </td>
        </tr>
    @endforelse
</tbody>
    <tfoot>
        <tr style="background-color: #D9E9FF; border-top: 2px solid #2c3e50;">
            <td colspan="6" class="text-right font-bold" style="padding: 10px; text-transform: uppercase;">
                Total Akumulasi Belanja
            </td>
            <td class="text-right font-bold" style="padding: 10px; font-size: 10px; color: #2c3e50;">
                Rp {{ number_format($total_transaksi, 0, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>
    <div class="footer-stamp">
        Dicetak otomatis oleh Sistem pada: {{ now()->translatedFormat('j F Y') }}
    </div>
</body>
</html>