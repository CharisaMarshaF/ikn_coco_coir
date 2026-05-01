<!DOCTYPE html>
<html>
<head>
    <title>Laporan Hasil Produksi</title>
    <style>
        /* Mengatur font dan line-height agar teks lebih rapat */
        body { font-family: sans-serif; font-size: 10px; color: #333; line-height: 1.2; }
        
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header h3 { margin: 2px 0; font-size: 12px; }
        .header p { margin: 2px 0; font-size: 10px; }

        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        /* Mengurangi padding dari 6px menjadi 3px 5px */
        th, td { border: 1px solid #999; padding: 3px 5px; text-align: left; vertical-align: top; }
        th { background: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Summary box dibuat lebih ringkas */
        .summary-box { width: 50%; margin-top: 15px; }
        .summary-box h4 { margin-bottom: 5px; font-size: 11px; }
        .summary-box table td { padding: 2px 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $konfigurasi->nama_perusahaan ?? 'IKN COCO COIR' }}</h2>
        <h3>LAPORAN HASIL PRODUKSI</h3>
        <p>
            Periode: 
            <strong>{{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('d F Y') }}</strong> 
            s/d 
            <strong>{{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('d F Y') }}</strong>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="25">No</th>
                <th width="100">Tanggal</th>
                <th width="90">Kode</th>
                <th>Daftar Produk (Qty)</th>
                <th width="90" class="text-right">Total Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</td>
                <td class="text-center">#{{ $row->kode_produksi }}</td>
                <td>
                    @foreach($row->details as $det)
                        • {{ $det->produk ? ($det->produk->trashed() ? $det->produk->nama . ' (Dihapus)' : $det->produk->nama) : 'N/A' }} 
                        ({{ number_format($det->qty, 2) }} {{ $det->produk->satuan ?? '' }})<br>
                    @endforeach
                </td>
                <td class="text-right">
                    <strong>{{ number_format($row->details->sum('qty'), 2) }}</strong>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data pada periode ini</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <h4>Ringkasan Per Produk</h4>
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th class="text-right" width="100">Total Produksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary as $nama => $info)
                <tr>
                    <td>{{ $nama }}</td>
                    <td class="text-right">
                        <strong>{{ number_format($info['qty'], 2) }}</strong> {{ $info['satuan'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>