<!DOCTYPE html>
<html>
<head>
    <title>Laporan Hasil Produksi</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: sans-serif; font-size: 9px; color: #333; line-height: 1.3; }
        
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header h3 { margin: 2px 0; font-size: 12px; }
        .header p { margin: 2px 0; font-size: 10px; }

        table { width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed; }
        th, td { border: 1px solid #777; padding: 5px; vertical-align: middle; word-wrap: break-word; }
        th { background: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 8.5px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .summary-box { width: 300px; margin-top: 20px; float: right; }
        .summary-box h4 { margin-bottom: 5px; font-size: 10px; text-transform: uppercase; border-bottom: 1px solid #000; padding-bottom: 2px; }
        .summary-box table td { padding: 4px 5px; border: 1px solid #ccc; }
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
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="30%">Kode</th>
                <th width="40%">Produk</th>
                <th width="10%" class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($data as $row)
                @php $rowCount = $row->details->count(); @endphp
                @foreach($row->details as $index => $det)
                <tr>
                    @if($index === 0)
                        <td width="5%" rowspan="{{ $rowCount }}" class="text-center">{{ $no++ }}</td>
                        <td width="15%" rowspan="{{ $rowCount }}" class="text-center">
                            {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('j F Y') }}
                        </td>
                        <td width="30%" rowspan="{{ $rowCount }}" class="text-center font-bold">
                            #{{ $row->kode_produksi }}
                        </td>
                    @endif

                    <td width="40%">
                        {{ $det->produk ? ($det->produk->trashed() ? $det->produk->nama . ' (Dihapus)' : $det->produk->nama) : 'Produk Tidak Ditemukan' }}
                    </td>
                    <td width="10%" class="text-center">
                        {{ number_format($det->qty, 0, ',', '.') }} {{ $det->produk->satuan ?? '' }}
                    </td>
                </tr>
                @endforeach
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data hasil produksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="clear: both;"></div>

    <div class="summary-box">
        <h4>Ringkasan Total Produksi</h4>
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th class="text-center" width="100">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary as $nama => $info)
                <tr>
                    <td>{{ $nama }}</td>
                    <td class="text-right font-bold">
                        {{ number_format($info['qty'], 0, ',', '.') }} {{ $info['satuan'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px; text-align: left; font-size: 8px; color: #666; position: fixed; bottom: 0;">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>