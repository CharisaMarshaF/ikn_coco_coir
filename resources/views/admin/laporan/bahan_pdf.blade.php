<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok - {{ $bahan->nama }}</title>
    <style>
        /* Pengaturan font dan ukuran agar rapat & profesional */
        body { 
            font-family: sans-serif; 
            font-size: 10px; 
            color: #333; 
            line-height: 1.2; 
            margin: 0; 
            padding: 0; 
        }
        
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #000; 
            padding-bottom: 5px; 
        }
        
        .header h2 { 
            margin: 0; 
            font-size: 16px; 
            text-transform: uppercase; 
        }
        
        .header p { 
            margin: 2px 0; 
            font-size: 11px; 
            font-weight: bold; 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 5px; 
        }
        
        /* Padding dipersempit (3px 5px) agar space tidak terlalu jauh */
        th, td { 
            border: 1px solid #999; 
            padding: 3px 5px; 
            vertical-align: middle;
        }
        
        th { 
            background-color: #f2f2f2; 
            text-transform: uppercase; 
            font-size: 9px; 
            text-align: center; 
            font-weight: bold;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Baris Saldo Awal & Akhir */
        .bg-gray { 
            background-color: #f9f9f9; 
            font-weight: bold; 
        }
        
        .footer { 
            margin-top: 20px; 
            font-size: 9px; 
        }

        .success { color: #2d7a2d; }
        .danger { color: #b91c1c; }
    </style>
</head>
<body>
    <div class="header">
        <h2>IKN COCO COIR</h2>
        <p>LAPORAN STOK BAHAN BAKU</p>
        <span style="font-size: 9px;">
            Bahan: <strong>{{ $bahan->nama }}</strong> | 
            Periode: <strong>{{ $start_date->translatedFormat('d F Y') }}</strong> s/d <strong>{{ $end_date->translatedFormat('d F Y') }}</strong>
        </span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30px">No</th>
                <th width="110px">Tanggal</th>
                <th>Pembelian (Masuk)</th>
                <th>Produksi (Keluar)</th>
                <th width="100px">Saldo Stok</th>
            </tr>
        </thead>
        <tbody>
            <!-- Baris Saldo Awal -->
            <tr class="bg-gray">
                <td class="text-center">-</td>
                <td class="text-center">SALDO AWAL</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($stokAwal, 0, ',', '.') }}</td>
            </tr>

            @php $no = 1; @endphp
            @foreach($mutasi as $m)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                {{-- Format Tanggal: 20 April 2026 --}}
                <td class="text-center">
                    {{ \Carbon\Carbon::parse($m['tanggal'])->translatedFormat('d F Y') }}
                </td>
                <td class="text-right success">
                    {{ $m['masuk'] > 0 ? '+'.number_format($m['masuk'], 0, ',', '.') : '-' }}
                </td>
                <td class="text-right danger">
                    {{ $m['keluar'] > 0 ? '-'.number_format($m['keluar'], 0, ',', '.') : '-' }}
                </td>
                <td class="text-right" style="font-weight: bold;">
                    {{ number_format($m['stok_akhir'], 0, ',', '.') }}
                </td>
            </tr>
            @endforeach

            <!-- Baris Saldo Akhir -->
            <tr class="bg-gray" style="border-top: 1.5px solid #000;">
                <td colspan="4" class="text-right">SALDO AKHIR PER {{ $end_date->translatedFormat('d F Y') }}</td>
                <td class="text-right">{{ number_format($stokAkhir, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <strong>Ringkasan :</strong>
        <table style="width: 300px; margin-top: 5px; border: none;">
            <tr>
                <td style="border: none; padding: 1px;">Total Penggunaan (Keluar)</td>
                <td style="border: none; padding: 1px;">:</td>
                <td style="border: none; padding: 1px;"><strong>{{ number_format($totalDiambil, 0, ',', '.') }} {{ $bahan->satuan }}</strong></td>
            </tr>
            <tr>
                <td style="border: none; padding: 1px;">Status Stok Akhir</td>
                <td style="border: none; padding: 1px;">:</td>
                <td style="border: none; padding: 1px;"><strong>{{ number_format($stokAkhir, 0, ',', '.') }} {{ $bahan->satuan }}</strong></td>
            </tr>
        </table>
        
        <div style="text-align: right; margin-top: 10px; color: #666;">
            Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
        </div>
    </div>
</body>
</html>