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

        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }

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
        
        .row-detail td { color: #555; font-size: 8pt; }

        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 16px; color: #2c3e50; }
        .header h3 { margin: 4px 0; font-size: 12px; color: #555; }
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
                <th width="5%">NO</th>
                <th width="15%">TANGGAL</th>
                <th width="30%">KETERANGAN / DETAIL</th>
                <th width="15%">NOMINAL</th>
                @foreach($kategoriTerlibat as $kat)
                    <th width="10%">{{ strtoupper($kat->nama) }}</th>
                @endforeach
                <th width="15%">SALDO</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $runningSaldo = $saldoAwal; 
                $no = 1;
                $totalPerKat = [];
                foreach($kategoriTerlibat as $kat) { $totalPerKat[$kat->id] = 0; }
            @endphp

            {{-- Baris Saldo Awal --}}
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('d M Y') }}</td>
                <td class="font-bold" colspan="2">SALDO AWAL</td>
                @foreach($kategoriTerlibat as $kat)
                    <td class="text-right">-</td>
                @endforeach
                <td class="text-right font-bold">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>

            @foreach($data as $kas)
                @php 
                    $isMasuk = $kas->jenis == 'masuk';
                    $nominal = (int)$kas->total_nominal;
                    $runningSaldo = $isMasuk ? $runningSaldo + $nominal : $runningSaldo - $nominal;
                    
                    if(isset($totalPerKat[$kas->kategori_kas_id])) {
                        $totalPerKat[$kas->kategori_kas_id] += ($isMasuk ? $nominal : -$nominal);
                    }

                    $hasDetails = ($kas->kategori == 'operasional' && $kas->details->count() > 0);
                    $ketKosong = empty($kas->keterangan);
                @endphp

                {{-- Baris Utama Transaksi --}}
                <tr>
                    <td class="text-center" style="{{ $hasDetails ? 'border-bottom: none;' : '' }}">{{ $no++ }}</td>
                    <td class="text-center" style="{{ $hasDetails ? 'border-bottom: none;' : '' }}">
                        {{ \Carbon\Carbon::parse($kas->tanggal)->translatedFormat('d M Y') }}
                    </td>
                    
                    @if($hasDetails && $ketKosong)
                        {{-- JIKA KETERANGAN KOSONG: Langsung tampilkan detail pertama di baris ini --}}
                        @php $firstDetail = $kas->details->first(); @endphp
                        <td style="border-bottom: none; padding-left: 15px;">
                            &bull; {{ $firstDetail->nama_item }} ({{ (int)$firstDetail->jumlah }}x)
                        </td>
                        <td class="text-right" style="border-bottom: none;">
                            {{ number_format($firstDetail->subtotal, 0, ',', '.') }}
                        </td>
                    @else
                        {{-- JIKA ADA KETERANGAN: Tampilkan keterangan seperti biasa --}}
                        <td class="{{ $hasDetails ? 'font-bold' : '' }}" style="{{ $hasDetails ? 'border-bottom: none;' : '' }}">
                            {{ $kas->keterangan ?? 'Tidak ada keterangan' }} 
                        </td>
                        <td class="text-right" style="{{ $hasDetails ? 'border-bottom: none;' : '' }}">
                            {{ $hasDetails ? '' : number_format($nominal, 0, ',', '.') }}
                        </td>
                    @endif

                    @foreach($kategoriTerlibat as $kat)
                        <td class="text-right" style="{{ $hasDetails ? 'border-bottom: none;' : '' }}">
                            @if($kas->kategori_kas_id == $kat->id)
                                <span class="{{ $isMasuk ? 'text-masuk' : 'text-keluar' }}">
                                    {{ $isMasuk ? '+' : '-' }}{{ number_format($nominal, 0, ',', '.') }}
                                </span>
                            @else
                                <span style="color: #ccc;">-</span>
                            @endif
                        </td>
                    @endforeach

                    <td class="text-right font-bold" style="{{ $hasDetails ? 'border-bottom: none;' : '' }}">
                        {{ number_format($runningSaldo, 0, ',', '.') }}
                    </td>
                </tr>

                {{-- Baris Detail Tambahan --}}
                @if($hasDetails)
                    @foreach($kas->details as $index => $detail)
                        {{-- Jika keterangan kosong, lewati index 0 karena sudah tampil di baris utama --}}
                        @if($ketKosong && $index === 0) @continue @endif

                        @php $isLast = ($index == $kas->details->count() - 1); @endphp
                        <tr class="row-detail">
                            <td style="border-top: none; border-bottom: {{ $isLast ? '' : 'none' }}"></td>
                            <td style="border-top: none; border-bottom: {{ $isLast ? '' : 'none' }}"></td>
                            <td style="border-top: none; border-bottom: {{ $isLast ? '' : 'none' }}; padding-left: 15px;">
                                &bull; {{ $detail->nama_item }} ({{ (int)$detail->jumlah }}x)
                            </td>
                            <td class="text-right" style="border-top: none; border-bottom: {{ $isLast ? '' : 'none' }}">
                                {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </td>
                            @foreach($kategoriTerlibat as $kat)
                                <td style="border-top: none; border-bottom: {{ $isLast ? '' : 'none' }}"></td>
                            @endforeach
                            <td style="border-top: none; border-bottom: {{ $isLast ? '' : 'none' }}"></td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-footer font-bold">
                <td colspan="4" class="text-right">TOTAL MUTASI / SALDO AKHIR</td>
                @foreach($kategoriTerlibat as $kat)
                    <td class="text-right">
                        {{ number_format($totalPerKat[$kat->id], 0, ',', '.') }}
                    </td>
                @endforeach
                <td class="text-right" style="background-color:#D9E9FF;">
                    {{ number_format($runningSaldo, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 15px; font-size: 8pt; text-align: right;">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>