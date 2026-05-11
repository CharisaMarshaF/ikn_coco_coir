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
            font-size: 8px; 
            padding: 8px 4px;
            border: 1px solid #7f8c8d;
            vertical-align: middle;
        }

        td { 
            border: 1px solid #7f8c8d; 
            padding: 6px 4px; 
            vertical-align: middle;
            word-wrap: break-word; 
        }

        /* Helpers */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* Row Highlights */
        .bg-light { background-color: #f9fbfd; }
        
        /* Footer Stamp */
        .footer-stamp {
            margin-top: 15px;
            font-size: 9px;
            color: #000;
        }

        .total-row {
            background-color: #eee;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $konfigurasi->nama_cv ?? 'IKN COCO COIR' }}</h2>
        <h3>LAPORAN PENGAMBILAN BAHAN BAKU (RINGKASAN HARIAN)</h3>
        <p>Periode: 
            <strong>{{ \Carbon\Carbon::parse($dari)->translatedFormat('j F Y') }}</strong> 
            s/d 
            <strong>{{ \Carbon\Carbon::parse($sampai)->translatedFormat('j F Y') }}</strong>
        </p>
    </div>

    @php
        // 1. Ambil semua bahan unik untuk kolom (th)
        $daftarBahan = $data->pluck('bahan')->unique('id')->sortBy('nama');
        
        // 2. LOGIKA GROUP BY TANGGAL:
        // Kita kelompokkan data berdasarkan tanggal saja, lalu kita sum jumlahnya
        $reportData = $data->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->pengambilan->tanggal)->format('Y-m-d');
        });

        $grandTotal = [];
        foreach($daftarBahan as $b) {
            $grandTotal[$b->id] = 0;
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                @foreach($daftarBahan as $b)
                    <th width="10%">{{ $b->nama }} ({{ $b->satuan ?? 'Kg' }})</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($reportData as $tanggalStr => $items)
                <tr class="{{ $no % 2 == 0 ? 'bg-light' : '' }}">
                    <td width="5%" class="text-center">{{ $no++ }}</td>
                    <td width="15%" class="text-center">
                        {{ \Carbon\Carbon::parse($tanggalStr)->translatedFormat('j M Y') }}
                    </td>
                    
                    @foreach($daftarBahan as $b)
                        @php 
                            // Hitung total pengambilan bahan ini pada tanggal tersebut
                            $totalPerHari = $items->where('bahan_id', $b->id)->sum('qty');
                            $grandTotal[$b->id] += $totalPerHari;
                        @endphp
                        <td class="text-right">
                            {{ number_format($totalPerHari, 2, ',', '.') }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 2 + $daftarBahan->count() }}" class="text-center" style="padding: 20px;">
                        Tidak ada data pengambilan pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($reportData->isNotEmpty())
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-center font-bold">Total</td>
                @foreach($daftarBahan as $b)
                    <td class="text-right">{{ number_format($grandTotal[$b->id], 2, ',', '.') }}</td>
                @endforeach
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer-stamp">
        Dicetak otomatis oleh Sistem pada: {{ now()->translatedFormat('j F Y') }}
    </div>
</body>
</html>