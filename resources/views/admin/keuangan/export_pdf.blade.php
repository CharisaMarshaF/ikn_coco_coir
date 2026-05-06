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
            table-layout: fixed; /* WAJIB biar width kepakai */
        }
        
        .data-table th { 
            background-color: #D9E9FF; 
            border: 1px solid #777; 
            padding: 8px 4px; 
            text-align: center; 
            text-transform: uppercase;
            font-weight: bold;
            color: #333;
        }
        
        .data-table td { 
            border: 1px solid #777; 
            padding: 6px 4px; 
            word-wrap: break-word; 
            vertical-align: middle; 
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .row-item:nth-child(even) {
            background-color: #fafafa;
        }

        .bg-footer {
            background-color: #eee;
        }

        /* ===== FIX UTAMA KOLOM ===== */

        .col-no { 
            width: 20px !important;
        }

        .col-tgl { width: 80px; }
        .col-ket { width: auto; }
        .col-masuk { width: 90px; }
        .col-keluar { width: 90px; }
        .col-saldo { width: 100px; }

        /* Paksa kolom NO kecil */
        .data-table th.col-no,
        .data-table td.col-no {
            padding: 2px !important;
            font-size: 8pt;
            text-align: center;
            white-space: nowrap;
        }

    </style>
</head>
<body>

    <!-- Header -->
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

    <!-- Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="15%" class="col-tgl">TANGGAL</th>
                <th width="30%" class="col-ket">KETERANGAN</th>
                <th width="15%" class="col-masuk">PEMASUKAN</th>
                <th width="15%" class="col-keluar">PENGELUARAN</th>
                <th width="20%" class="col-saldo">SALDO</th>
            </tr>
        </thead>
        <tbody>

            <!-- Saldo Awal -->
            <tr>
                <td width="5%" class="text-center">1</td>
                <td class="text-center">
                    {{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('j F Y') }}
                </td>
                <td class="font-bold">SALDO AWAL</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right font-bold">
                    {{ number_format($saldoAwal, 0, ',', '.') }}
                </td>
            </tr>

            @php 
                $runningSaldo = $saldoAwal; 
                $no = 2;
                $totalMasuk = 0;
                $totalKeluar = 0;
            @endphp

            @foreach($data as $kas)

                @if($kas->kategori == 'operasional' && $kas->details->count() > 0)

                    @foreach($kas->details as $detail)

                        @php 
                            $isMasuk = $kas->jenis == 'masuk';
                            $nominal = (int)$detail->subtotal;

                            $runningSaldo = $isMasuk 
                                ? $runningSaldo + $nominal 
                                : $runningSaldo - $nominal;

                            if($isMasuk) $totalMasuk += $nominal;
                            else $totalKeluar += $nominal;
                        @endphp

                        <tr class="row-item">
                            <td width="5%" class="text-center">{{ $no++ }}</td>

                            <td width="15%" class="text-center">
                                {{ \Carbon\Carbon::parse($kas->tanggal)->translatedFormat('j F Y') }}
                            </td>

                            <td width="30%">
                                {{ $detail->nama_item }}
                                <span style="font-size: 8pt; color: #666;">
                                    ({{ (int)$detail->jumlah }}x)
                                </span>
                            </td>

                            <td width="15%" class="text-right">
                                {{ $isMasuk ? number_format($nominal, 0, ',', '.') : '-' }}
                            </td>

                            <td width="15%" class="text-right">
                                {{ !$isMasuk ? number_format($nominal, 0, ',', '.') : '-' }}
                            </td>

                            <td width="20%" class="text-right font-bold">
                                {{ number_format($runningSaldo, 0, ',', '.') }}
                            </td>
                        </tr>

                    @endforeach

                @else

                    @php 
                        $isMasuk = $kas->jenis == 'masuk';
                        $nominal = (int)$kas->total_nominal;

                        $runningSaldo = $isMasuk 
                            ? $runningSaldo + $nominal 
                            : $runningSaldo - $nominal;

                        if($isMasuk) $totalMasuk += $nominal;
                        else $totalKeluar += $nominal;
                    @endphp

                    <tr class="row-item">
                        <td class="col-no">{{ $no++ }}</td>

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

        <tfoot>
            <tr class="bg-footer font-bold">
                <td colspan="3" class="text-right">TOTAL SALDO</td>

                <td class="text-right">
                    {{ number_format($totalMasuk, 0, ',', '.') }}
                </td>

                <td class="text-right">
                    {{ number_format($totalKeluar, 0, ',', '.') }}
                </td>

                <td class="text-right" style="background-color:#D9E9FF;">
                    {{ number_format($runningSaldo, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>


</body>
</html>