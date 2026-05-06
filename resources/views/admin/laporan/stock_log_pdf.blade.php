<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Mutasi Stok - {{ strtoupper($type) }}</title>
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

        /* Item Title Bar */
        .item-title { 
            background-color: #2c3e50; 
            color: #ffffff;
            padding: 6px 10px; 
            font-size: 10px; 
            font-weight: bold; 
            margin-top: 15px;
            border-radius: 2px 2px 0 0;
        }

        /* Table Styling */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed;
            background-color: #fff;
            margin-bottom: 15px;
        }
        
        th { 
            background-color: #D9E9FF;
            color: #2c3e50;
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 8px; 
            padding: 8px 4px;
            border: 1px solid #7f8c8d;
            vertical-align: middle;
        }

        td { 
            border: 1px solid #7f8c8d; 
            padding: 6px; 
            vertical-align: top; 
            word-wrap: break-word; 
        }

        /* Helpers */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .success { color: #15803d; font-weight: bold; }
        .danger { color: #b91c1c; font-weight: bold; }
        .bg-light { background-color: #f9fbfd; }
        
        .row-stok-awal {
            background-color: #f0f4f8;
            font-weight: bold;
            color: #2c3e50;
        }

        /* Footer Stamp */
        .footer-stamp {
            margin-top: 20px;
            font-size: 8px;
            color: #999;
            font-style: italic;
        }

        .signature-section {
            margin-top: 30px;
            float: right;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ strtoupper($konfigurasi->nama_cv ?? 'SISTEM INVENTORI') }}</h2>
        <h3>LAPORAN MUTASI {{ strtoupper(str_replace('_', ' ', $type)) }}</h3>
        <p>
            Periode: <strong>{{ $start_date->translatedFormat('j F Y') }}</strong> s/d <strong>{{ $end_date->translatedFormat('j F Y') }}</strong>
        </p>
    </div>

    @foreach($groupedData as $itemId => $data)
        <div class="item-title">
            ITEM: {{ strtoupper($data['nama']) }}
        </div>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="18%">Tanggal</th>
                    <th width="42%">Keterangan / Sumber</th>
                    <th width="13%">Masuk</th>
                    <th width="13%">Keluar</th>
                    <th width="14%" class="text-right">Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                <!-- Baris Stok Awal -->
                <tr class="row-stok-awal">
                    <td class="text-center">1</td>
                    <td class="text-center">{{ $start_date->translatedFormat('j F Y') }}</td>
                    <td>STOK AWAL (PERIODE SEBELUMNYA)</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-right">{{ $formatNumber($data['stok_awal']) }}</td>
                </tr>

                @php $currentStok = $data['stok_awal']; @endphp
                @foreach($data['logs'] as $index => $log)
                    @php 
                        $masuk = $log->jenis == 'masuk' ? (double)$log->jumlah : 0;
                        $keluar = $log->jenis == 'keluar' ? (double)$log->jumlah : 0;
                        $currentStok = $currentStok + $masuk - $keluar;
                        $rowClass = ($index % 2 == 0) ? '' : 'bg-light';
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="text-center">
                            {{ $index + 2 }}
                        </td>
                        <td class="text-center">  
                            {{ $log->created_at->translatedFormat('j F Y') }}<br>
                        </td>
                        <td>
                            <span class="font-bold">[{{ strtoupper($log->sumber) }}]</span><br>
                            <small style="color: #666;">{{ $log->keterangan ?? '-' }}</small>
                        </td>
                        <td class="text-center success">
                            {{ $masuk > 0 ? '+'.$formatNumber($masuk) : '-' }}
                        </td>
                        <td class="text-center danger">
                            {{ $keluar > 0 ? '-'.$formatNumber($keluar) : '-' }}
                        </td>
                        <td class="text-right font-bold">
                            {{ $formatNumber($currentStok) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="footer-stamp">
        Dicetak otomatis oleh Sistem pada: {{ now()->translatedFormat('j F Y') }} WIB
    </div>
</body>
</html>