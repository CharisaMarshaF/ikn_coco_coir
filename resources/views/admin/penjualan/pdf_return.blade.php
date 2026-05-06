<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Return Penjualan {{ $konfigurasi->nama_cv ?? 'IKN COCO COIR' }}</title>
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
        .uppercase { text-transform: uppercase; }

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
    </style>
</head>
<body>
    @php
        \Carbon\Carbon::setLocale('id');
    @endphp

    <div class="header">
        <h2>{{ $konfigurasi->nama_cv ?? 'IKN COCO COIR' }}</h2>
        <h3>LAPORAN RETURN PENJUALAN</h3>
        <p>Periode: 
            <strong>{{ \Carbon\Carbon::parse($start)->translatedFormat('j F Y') }}</strong> 
            s/d 
            <strong>{{ \Carbon\Carbon::parse($end)->translatedFormat('j F Y') }}</strong>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                 <th width="14%">Tanggal</th>
                <th width="14%">No. Return</th>
                <th width="18%">Client</th>
                <th width="20%">Produk</th>
                <th width="10%">Qty</th>
                <th width="10%">Harga</th>
                <th width="10%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($data as $index => $row)
                @php 
                    $grandTotal += $row->subtotal; 
                    $rowClass = ($index % 2 == 0) ? '' : 'bg-light';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($row->returnHeader->tanggal)->translatedFormat('j F Y') }}
                    </td>
                    <td class="text-center font-bold" style="color: #2c3e50;">
                        {{ $row->returnHeader->nomor_return }}
                    </td>
                    
                    <td class="uppercase">
                        {{ $row->returnHeader->penjualan->client->nama ?? 'Umum' }}
                    </td>
                    <td class="uppercase">
                        {{ $row->produk->nama ?? 'Produk Tidak Ditemukan' }}
                    </td>
                    <td class="text-right">
                        {{ number_format($row->qty, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($row->harga, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-bold">
                        {{ number_format($row->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data return pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="row-total-footer">
                <td colspan="7" class="text-right" style="padding: 8px;">TOTAL RETURN</td>
                <td class="text-right" style="padding: 8px;">
                    {{ number_format($grandTotal, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-stamp">
        Dicetak otomatis oleh Sistem pada: {{ now()->translatedFormat('j F Y, H:i') }} WIB
    </div>
</body>
</html>