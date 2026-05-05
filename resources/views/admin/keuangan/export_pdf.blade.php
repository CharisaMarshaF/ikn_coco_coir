<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kas - {{ $namaRekening }}</title>
    <style>
        @page { 
            margin: 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9pt; 
            color: #333; 
            line-height: 1.4; 
        }

        /* Header Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-table td {
            border: 1px solid #777;
            padding: 8px 12px;
        }

        .info-label {
            background-color: #ffffff;
            font-weight: bold;
            width: 25%;
        }

        /* Main Data Table */
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
        }
        
        .data-table th { 
            background-color: #D9E9FF; 
            border: 1px solid #777; 
            padding: 10px 5px; 
            text-align: center; 
            text-transform: uppercase;
            font-weight: bold;
            color: #333;
        }
        
        .data-table td { 
            border: 1px solid #777; 
            padding: 8px 6px; 
            word-wrap: break-word; 
            vertical-align: middle; 
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .row-item:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>
<body>

    <!-- Tabel Header Informasi -->
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Rekening</td>
            <td>{{ $namaRekening }}</td>
        </tr>
        <tr>
            <td class="info-label">Periode</td>
            <td>
                {{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('j F Y') }} 
                sampai 
                {{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('j F Y') }}
            </td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="35">NO</th>
                <th width="100">TANGGAL</th>
                <th>KETERANGAN</th>
                <th width="110">PEMASUKAN</th>
                <th width="110">PENGELUARAN</th>
                <th width="120">SALDO</th>
            </tr>
        </thead>
        <tbody>
            {{-- Baris Saldo Awal --}}
            <tr>
                <td class="text-center">1</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('j F Y') }}</td>
                <td class="font-bold">SALDO AWAL</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>

            @php 
                $runningSaldo = $saldoAwal; 
                $no = 2;
            @endphp

            @foreach($data as $kas)
                @if($kas->kategori == 'operasional' && $kas->details->count() > 0)
                    @foreach($kas->details as $index => $detail)
                        @php 
                            $isMasuk = $kas->jenis == 'masuk';
                            $nominal = $detail->subtotal;
                            $runningSaldo = $isMasuk ? ($runningSaldo + $nominal) : ($runningSaldo - $nominal);
                        @endphp
                        <tr class="row-item">
                            <td class="text-center">{{ $no++ }}</td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($kas->tanggal)->translatedFormat('j F Y') }}
                            </td>
                            <td>
                                {{ $detail->nama_item }} 
                                <span style="font-size: 8pt; color: #666;">({{ $detail->jumlah }}x)</span>
                            </td>
                            <td class="text-right">
                                {{ $isMasuk ? number_format($nominal, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right">
                                {{ !$isMasuk ? number_format($nominal, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right font-bold">
                                {{ number_format($runningSaldo, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    @php 
                        $isMasuk = $kas->jenis == 'masuk';
                        $nominal = $kas->total_nominal;
                        $runningSaldo = $isMasuk ? ($runningSaldo + $nominal) : ($runningSaldo - $nominal);
                    @endphp
                    <tr class="row-item">
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($kas->tanggal)->translatedFormat('j F Y') }}
                        </td>
                        <td>{{ $kas->keterangan }}</td>
                        <td class="text-right">
                            {{ $isMasuk ? number_format($nominal, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-right">
                            {{ !$isMasuk ? number_format($nominal, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-right font-bold">
                            {{ number_format($runningSaldo, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

</body>
</html>