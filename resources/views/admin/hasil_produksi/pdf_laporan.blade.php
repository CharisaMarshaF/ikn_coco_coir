<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Produksi</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 2px; padding-bottom: 8px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 16px; color: #2c3e50; }
        .header h3 { margin: 4px 0; font-size: 11px; color: #555; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { background-color: #D9E9FF; color: #2c3e50; font-weight: bold; text-transform: uppercase; font-size: 8px; padding: 8px 4px; border: 1px solid #7f8c8d; }
        td { border: 1px solid #7f8c8d; padding: 6px 4px; vertical-align: middle; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-light { background-color: #f9fbfd; }
        .footer-row { background-color: #eee; font-weight: bold; }
        .footer-stamp { margin-top: 15px; font-size: 8px; color: #999; font-style: italic; }
        .info-box { margin-bottom: 10px; font-size: 10px; }
          .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { border: 1px solid #777; padding: 8px 12px; }
        .info-label { background-color: #ffffff; font-weight: bold; width: 25%; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $konfigurasi->nama_cv ?? 'IKN COCO COIR' }}</h2>
        <h3>LAPORAN HASIL PRODUKSI</h3>

    </div>
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Produk</td>
            <td>{{ $produk->nama }}</td>
        </tr>
        <tr>
            <td class="info-label">Periode</td>
            <td>
                {{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('j F Y') }} sampai {{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('j F Y') }}
            </td>
        </tr>
    </table>

    

    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="20%">Tanggal</th>
                <th width="23%">Pola Bulat</th>
                <th width="23%">Pola Setengah Jadi</th>
                <th width="24%">Produk Jadi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($report as $row)
            <tr class="{{ $no % 2 == 0 ? 'bg-light' : '' }}">
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('j M Y') }}</td>
                <td class="text-right">{{ number_format($row['bulat'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row['setengah_jadi'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row['jadi'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 20px;">Tidak ada data produksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($report) > 0)
        <tfoot>
            <tr class="footer-row">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-right">{{ number_format($totalBulat, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalSetengah, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalJadi, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer-stamp">
        Dicetak pada: {{ now()->translatedFormat('j F Y, H:i') }} WIB
    </div>
</body>
</html>