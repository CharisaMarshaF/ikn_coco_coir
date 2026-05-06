<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Mutasi Stok - {{ $bahan->nama }}</title>
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
            padding: 6px; 
            vertical-align: top; 
            word-wrap: break-word; 
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .success { color: #15803d; font-weight: bold; }
        .danger { color: #b91c1c; font-weight: bold; }
        .bg-light { background-color: #f9fbfd; }
        .row-saldo { background-color: #f0f4f8; font-weight: bold; color: #2c3e50; }
        
        h4 { margin: 15px 0 5px 0; font-size: 10px; color: #2c3e50; text-transform: uppercase; }
        .footer-stamp { margin-top: 15px; font-size: 8px; color: #999; font-style: italic; }
        .table-summary { width: 55%; margin-top: 10px; }
        .table-summary td { padding: 5px 8px; border: 1px solid #eee; }
        
        .small-text { font-size: 7.5px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $konfigurasi->nama_cv ?? 'IKN COCO COIR' }}</h2>
        <h3>LAPORAN STOK BAHAN BAKU</h3>
        <p>
            Bahan: <strong>{{ strtoupper($bahan->nama) }}</strong> | 
            Periode: <strong>{{ $start_date->translatedFormat('j F Y') }}</strong> s/d <strong>{{ $end_date->translatedFormat('j F Y') }}</strong>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="18%"> Tanggal</th>
                <th width="35%">Keterangan Transaksi</th>
                <th width="13%">Masuk</th>
                <th width="13%">Keluar</th>
                <th width="16%" class="text-right">Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            <!-- Baris Stok Awal -->
            <tr class="row-saldo">
                <td class="text-center">-</td>
                <td class="text-center">{{ $start_date->translatedFormat('j F Y') }}</td>
                <td>STOK AWAL PERIODE</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-right">
                    {{ $formatNumber($stokAwal) }} <span class="small-text">{{ $bahan->satuan }}</span>
                </td>
            </tr>

            @php $no = 1; @endphp
            @forelse($mutasi as $m)
                <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-light' : '' }}">
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">
                        {{ $m['tanggal']->translatedFormat('j F Y') }}<br>
                    </td>
                    <td>
                        {{ $m['keterangan'] }}
                    </td>
                    <td class="text-center success">
                        {{ $m['masuk'] > 0 ? '+ ' . $formatNumber($m['masuk']) : '-' }}
                    </td>
                    <td class="text-center danger">
                        {{ $m['keluar'] > 0 ? '- ' . $formatNumber($m['keluar']) : '-' }}
                    </td>
                    <td class="text-right font-bold">
                        {{ $formatNumber($m['stok_akhir']) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #999;">
                        Tidak ada aktivitas mutasi pada periode ini.
                    </td>
                </tr>
            @endforelse

            <!-- Baris Stok Akhir -->
            <tr class="row-saldo">
                <td colspan="5" class="text-right">STOK AKHIR PER {{ $end_date->translatedFormat('j F Y') }}</td>
                <td class="text-right">
                    {{ $formatNumber($stokAkhir) }} <span class="small-text">{{ $bahan->satuan }}</span>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="summary-section">
        <h4>Ringkasan Aktivitas</h4>
        <table class="table-summary">
            <tr>
                <td class="bg-light">Total Stok Awal</td>
                <td class="text-right font-bold">{{ $formatNumber($stokAwal) }} {{ $bahan->satuan }}</td>
            </tr>
            <tr>
                <td class="bg-light">Total Pemasukan (Pembelian/Lainnya)</td>
                <td class="text-right success">+ {{ $formatNumber($totalMasuk) }} {{ $bahan->satuan }}</td>
            </tr>
            <tr>
                <td class="bg-light">Total Pengeluaran (Produksi/Lainnya)</td>
                <td class="text-right danger">- {{ $formatNumber($totalKeluar) }} {{ $bahan->satuan }}</td>
            </tr>
            <tr class="row-saldo">
                <td style="background-color: #D9E9FF;">Kalkulasi Stok Akhir</td>
                <td class="text-right" style="background-color: #D9E9FF;">
                    {{ $formatNumber($stokAkhir) }} {{ $bahan->satuan }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-stamp">
        Laporan ini dicetak secara otomatis oleh sistem pada {{ now()->translatedFormat('j F Y') }}
    </div>
</body>
</html>