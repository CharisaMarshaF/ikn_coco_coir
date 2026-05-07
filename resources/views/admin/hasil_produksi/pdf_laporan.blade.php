<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Produksi {{ $konfigurasi->nama_cv }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 8px; border-bottom: 2px solid #2c3e50; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 16px; color: #2c3e50; }
        .header h3 { margin: 4px 0; font-size: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #D9E9FF; color: #2c3e50; font-weight: bold; text-transform: uppercase; font-size: 8px; padding: 8px 4px; border: 1px solid #7f8c8d; }
        td { border: 1px solid #7f8c8d; padding: 6px; vertical-align: top; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-light { background-color: #f9fbfd; }
        .unit-text { font-size: 8px; color: #7f8c8d; margin-left: 2px; }
        .footer-stamp { margin-top: 15px; font-size: 8px; color: #999; font-style: italic; }
        h4 { margin: 15px 0 5px 0; font-size: 10px; color: #2c3e50; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $konfigurasi->nama_cv ?? 'IKN COCO COIR' }}</h2>
        <h3>LAPORAN HASIL PRODUKSI</h3>
        <p>Periode: 
            <strong>{{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('j F Y') }}</strong> s/d 
            <strong>{{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('j F Y') }}</strong>
        </p>
    </div>

<!-- Bagian Tabel Utama -->
<table>
    <thead>
        <tr>
            <th width="4%">No</th>
            <th width="18%">Tanggal</th>
            <th width="20%">Kode</th>
            <th width="28%">Produk</th>
            <th width="16%">Pola</th>
            <th width="15%">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @forelse($data as $row)
            @php 
                $rowCount = $row->details->count(); 
                $rowClass = ($no % 2 == 0) ? 'bg-light' : '';
            @endphp
            @foreach($row->details as $index => $det)
            <tr class="{{ $rowClass }}">
                @if($index === 0)
                    {{-- Style ditambahkan vertical-align: top --}}
                    <td rowspan="{{ $rowCount }}" class="text-center font-bold" style="vertical-align: top;">{{ $no++ }}</td>
                    <td rowspan="{{ $rowCount }}" class="text-center" style="vertical-align: top;">
                        {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('j F Y') }}
                    </td>
                    <td rowspan="{{ $rowCount }}" class="text-center font-bold" style="vertical-align: top;">
                         #{{ $row->kode_produksi }}
                    </td>
                @endif

                <td>
                    {{ $det->produk ? ($det->produk->trashed() ? $det->produk->nama . ' (Dihapus)' : $det->produk->nama) : 'N/A' }}
                </td>
                <td class="text-center">
                    {{-- Logika Label Pola --}}
                    @if(strtolower($det->produk->jenis ?? '') == 'jadi' || $det->kategori_pola === 'Jadi')
                        Produk Jadi
                    @else
                        {{ str_replace('_', ' ', $det->kategori_pola) }}
                    @endif
                </td>
                <td class="text-right">
                    <span class="font-bold">{{ number_format($det->qty, 0, ',', '.') }}</span>
                    <span class="unit-text">{{ $det->produk->satuan ?? '' }}</span>
                </td>
            </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data hasil produksi pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>

    <div style="width: 60%;">
        <h4>Total Akumulasi Hasil Produksi</h4>
        <table>
            <thead>
                <tr>
                    <th width="50%">Nama Produk</th>
                    <th width="20%">Pola</th>
                    <th width="30%">Total Qty</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    // Logika Pengurutan Akumulasi: Bulat, Setengah Jadi, Jadi
                    $sortedSummary = collect($summary)->sortBy(function($item) {
                        $pola = $item['pola'];
                        if ($pola == 'Bulat') return 1;
                        if ($pola == 'Setengah_jadi') return 2;
                        return 3; // Untuk 'Jadi'
                    });
                    $i = 0; 
                @endphp
                @foreach($sortedSummary as $item)
                <tr class="{{ $i % 2 != 0 ? 'bg-light' : '' }}">
                    <td>{{ $item['nama'] }}</td>
                    <td class="text-center">
                        @if(strtolower($item['jenis'] ?? '') == 'jadi' || $item['pola'] === 'Jadi')
                            Produk Jadi
                        @else
                            {{ str_replace('_', ' ', $item['pola']) }}
                        @endif
                    </td>
                    <td class="text-right">
                        <span class="font-bold">{{ number_format($item['qty'], 0, ',', '.') }}</span>
                        <span class="unit-text">{{ $item['satuan'] }}</span>
                    </td>
                </tr>
                @php $i++; @endphp
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer-stamp">
        Dicetak otomatis pada: {{ now()->translatedFormat('j F Y') }}
    </div>
</body>
</html>