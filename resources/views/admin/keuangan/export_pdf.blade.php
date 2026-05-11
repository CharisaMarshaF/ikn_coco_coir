<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kas - {{ $namaRekening }}</title>
    <style>
        @page { margin: 1cm; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 8.5pt; 
            color: #333; 
            line-height: 1.4; 
        }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { border: 1px solid #777; padding: 8px 12px; }
        .info-label { background-color: #ffffff; font-weight: bold; width: 25%; }

        .data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .data-table th { 
            background-color: #D9E9FF; border: 1px solid #777; 
            padding: 8px 4px; text-align: center; text-transform: uppercase;
            font-weight: bold; font-size: 8pt;
        }
        .data-table td { border: 1px solid #777; padding: 5px 4px; word-wrap: break-word; vertical-align: top; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-footer { background-color: #eee; }
        .text-masuk { color: green; font-weight: bold; }
        .text-keluar { color: red; font-weight: bold; }
        
        /* Style untuk detail item */
        .row-detail td { background-color: #fcfcfc; color: #555; }

        /* Header Section */
        .header { 
            text-align: center; 
            margin-bottom: 2px; 
            padding-bottom: 8px;
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
        }
        .header p { 
            margin: 2px 0; 
            font-size: 10px;
            color: #666;
        }

    </style>
</head>
<body>  
    <div class="header">
        <h2>{{ $konfigurasi->nama_cv ?? 'PT INTER KARANGANYAR NUSANTARA' }}</h2>
        <h3>LAPORAN MUTASI KAS HARIAN</h3>
    </div>
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Rekening</td>
            <td>{{ $namaRekening }}</td>
        </tr>
        <tr>
            <td class="info-label">Periode</td>
            <td>
                {{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('j F Y') }} sampai {{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('j F Y') }}
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="col-no">NO</th>
                <th width="15%" class="col-tgl">TANGGAL</th>
                <th width="30%" class="col-ket">KETERANGAN / DETAIL</th>
                <th width="15%" class="col-detail-amt">NOMINAL</th>
                @foreach($kategoriTerlibat as $kat)
                    <th width="10%" class="col-kat">{{ strtoupper($kat->nama) }}</th>
                @endforeach
                <th width="15%" class="col-saldo">SALDO</th>
            </tr>
        </thead>
        <tbody>
            <!-- Saldo Awal -->
            <tr>
                <td width="5%" class="text-center">1</td>
                <td width="15%" class="text-center">{{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('d F Y') }}</td>
                <td width="30%" class="font-bold" colspan="2">SALDO AWAL</td>
                @foreach($kategoriTerlibat as $kat)
                    <td width="10%" class="text-right">-</td>
                @endforeach
                <td width="15%" class="text-right font-bold">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>

            @php 
                $runningSaldo = $saldoAwal; 
                $no = 2;
                $totalPerKat = [];
                foreach($kategoriTerlibat as $kat) { $totalPerKat[$kat->id] = 0; }
            @endphp

            @foreach($data as $kas)
                @php 
                    $isMasuk = $kas->jenis == 'masuk';
                    $nominal = (int)$kas->total_nominal;
                    $runningSaldo = $isMasuk ? $runningSaldo + $nominal : $runningSaldo - $nominal;
                    
                    if(isset($totalPerKat[$kas->kategori_kas_id])) {
                        $totalPerKat[$kas->kategori_kas_id] += ($isMasuk ? $nominal : -$nominal);
                    }

                    $hasDetails = ($kas->kategori == 'operasional' && $kas->details->count() > 0);
                    $rowCount = $hasDetails ? $kas->details->count() + 1 : 1;
                @endphp

                <!-- Baris Utama Transaksi -->
                <tr>
                    <td width="5%" class="text-center" rowspan="{{ $rowCount }}">{{ $no++ }}</td>
                    <td width="15%" class="text-center" rowspan="{{ $rowCount }}">
                        {{ \Carbon\Carbon::parse($kas->tanggal)->translatedFormat('d F Y') }}
                    </td>
                    <td width="30%" class="{{ $hasDetails ? 'font-bold' : '' }}" style="border-bottom: {{ $hasDetails ? 'none' : '1px solid #777' }}">
                        {{ $kas->keterangan ?? 'Tidak ada keterangan' }} 
                    </td>
                    <td width="15%" class="text-right" style="border-bottom: {{ $hasDetails ? 'none' : '1px solid #777' }}">
                        {{-- Kosong di baris utama jika ada detail agar tidak double --}}
                        {{ $hasDetails ? '' : number_format($nominal, 0, ',', '.') }}
                    </td>

                    @foreach($kategoriTerlibat as $kat)
                        <td width="10%" class="text-right" rowspan="{{ $rowCount }}">
                            @if($kas->kategori_kas_id == $kat->id)
                                <span class="{{ $isMasuk ? 'text-masuk' : 'text-keluar' }}">
                                    {{ $isMasuk ? '+' : '-' }}{{ number_format($nominal, 0, ',', '.') }}
                                </span>
                            @else
                                <span style="color: #ccc;">-</span>
                            @endif
                        </td>
                    @endforeach

                    <td width="15%" class="text-right font-bold" rowspan="{{ $rowCount }}">
                        {{ number_format($runningSaldo, 0, ',', '.') }}
                    </td>
                </tr>

                <!-- Baris Detail Operasional -->
                @if($hasDetails)
                    @foreach($kas->details as $index => $detail)
                    <tr class="row-detail">
                        <td width="30%" style="border-top: none; border-bottom: {{ ($index == $kas->details->count() - 1) ? '1px solid #777' : 'none' }}; padding-left: 15px;">
• {{ $detail->nama_item }} ({{ (int)$detail->jumlah }}x)                        </td>
                        <td width="15%" class="text-right" style="border-top: none; border-bottom: {{ ($index == $kas->details->count() - 1) ? '1px solid #777' : 'none' }};">
                            {{-- Mengambil kolom subtotal dari model KasDetail --}}
                            {{ number_format($detail->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-footer font-bold">
                <td width="50%" colspan="4" class="text-right">TOTAL MUTASI / SALDO AKHIR</td>
                @foreach($kategoriTerlibat as $kat)
                    <td width="10%" class="text-right">
                        {{ number_format($totalPerKat[$kat->id], 0, ',', '.') }}
                    </td>
                @endforeach
                <td width="15%" class="text-right" style="background-color:#D9E9FF;">
                    {{ number_format($runningSaldo, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 15px; font-size: 8pt; text-align: right;">
        Dicetak pada: {{ now()->translatedFormat('d F Y ') }}
    </div>

</body>
</html>