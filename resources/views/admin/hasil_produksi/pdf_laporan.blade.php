<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Produksi {{ $konfigurasi->nama_cv }}</title>
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
        <h3>LAPORAN HASIL PRODUKSI</h3>
        <p>Periode: 
            <strong>{{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('j F Y') }}</strong> 
            s/d 
            <strong>{{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('j F Y') }}</strong>
        </p>
    </div>
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="16%">Tanggal</th>
                <th width="20%">Kode Produksi</th>
                <th width="40%">Produk</th>
                <th width="20%">Jumlah Hasil</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($data as $key => $row)
                @php 
                    $rowCount = $row->details->count(); 
                    $rowClass = ($no % 2 == 0) ? 'bg-light' : '';
                @endphp
                @foreach($row->details as $index => $det)
                <tr class="{{ $rowClass }}">
                    @if($index === 0)
                        <td rowspan="{{ $rowCount }}" class="text-center font-bold">{{ $no++ }}</td>
                        <td rowspan="{{ $rowCount }}" class="text-center">
                            {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('j F Y') }}
                        </td>
                        <td rowspan="{{ $rowCount }}" class="text-center font-bold" style="color: #2c3e50;">
                             #{{ $row->kode_produksi }}
                        </td>
                    @endif

                    <td>
                        {{ $det->produk ? ($det->produk->trashed() ? $det->produk->nama . ' (Dihapus)' : $det->produk->nama) : 'Produk Tidak Ditemukan' }}
                    </td>
                    <td class="text-right">
                        <span class="font-bold">{{ number_format($det->qty, 0, ',', '.') }}</span>
                        <span class="unit-text">{{ $det->produk->satuan ?? '' }}</span>
                    </td>
                </tr>
                @endforeach
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 20px;">Tidak ada data hasil produksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="width: 50%;">
        <h4>Total Akumulasi Hasil Produksi</h4>
        <table>
            <thead>
                <tr>
                    <th width="60%">Nama Produk</th>
                    <th width="30%">Total Qty</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 0; @endphp
                @foreach($summary as $nama => $info)
                <tr class="{{ $i % 2 != 0 ? 'bg-light' : '' }}">
                    <td>{{ $nama }}</td>
                    <td class="text-right">
                        <span class="font-bold">{{ number_format($info['qty'], 0, ',', '.') }}</span>
                        <span class="unit-text">{{ $info['satuan'] }}</span>
                    </td>
                </tr>
                @php $i++; @endphp
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer-stamp">
        Dicetak otomatis oleh Sistem pada: {{ now()->translatedFormat('j F Y, H:i') }} WIB
    </div>
</body>
</html>