<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { 
            font-family: sans-serif; 
            font-size: 10px; /* Ukuran font sedikit diperkecil */
            line-height: 1.1; /* Padding antar baris dirapatkan */
            color: #333;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { 
            margin-bottom: 10px; 
            border-bottom: 2px solid #000; 
            padding-bottom: 5px; 
        }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table, th, td { border: 1px solid #333; }
        th { 
            background-color: #f2f2f2; 
            padding: 4px 2px; /* Padding header dirapatkan */
            font-size: 10px;
        }
        td { 
            padding: 3px 4px; /* Padding sel dirapatkan */
            vertical-align: middle; 
        }
        .footer { margin-top: 20px; }
        .uppercase { text-transform: uppercase; }
    </style>
</head>
<body>
    @php
        // Set locale ke Bahasa Indonesia
        \Carbon\Carbon::setLocale('id');
    @endphp

    <div class="header text-center">
        <h2 style="margin: 0; font-size: 16px;">LAPORAN RETURN PENJUALAN</h2>
        <p style="margin: 3px 0;">
            Periode: {{ \Carbon\Carbon::parse($start)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($end)->translatedFormat('d F Y') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="14%">No. Return</th>
                <th width="14%">Tanggal</th>
                <th width="14%">Client</th>
                <th width="20%">Produk</th>
                <th width="6%">Qty</th>
                <th width="13%">Harga Satuan</th>
                <th width="15%">Subtotal </th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($data as $index => $row)
                @php 
                    $grandTotal += $row->subtotal; 
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->returnHeader->nomor_return }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($row->returnHeader->tanggal)->translatedFormat('d F Y') }}
                    </td>
                    <td class="uppercase">{{ $row->returnHeader->penjualan->client->nama }}</td>
                    <td class="uppercase">{{ $row->produk->nama }}</td>
                    <td class="text-center">{{ number_format($row->qty, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->harga, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data return pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td colspan="7" class="text-right">TOTAL </td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    
</body>
</html>