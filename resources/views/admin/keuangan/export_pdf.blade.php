<!DOCTYPE html>
<html>
<head>
    <style>
        /* Margin halaman diperkecil agar lebih luas */
        @page { margin: 0.8cm; }
        
        /* Font dan line-height dirapatkan */
        body { font-family: sans-serif; font-size: 8pt; color: #333; line-height: 1.1; }
        
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 10px; }
        
        /* Padding sel dikurangi agar lebih rapat (2px) */
        th, td { border: 1px solid #444; padding: 2px 4px; word-wrap: break-word; vertical-align: middle; }
        
        th { background-color: #FFC000; text-align: center; text-transform: uppercase; font-weight: bold; font-size: 7.5pt; }
        
        .bg-gray { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .sub-header { background-color: #FFF2CC; font-size: 7pt; }
        
        .header-title { margin: 0; padding: 0; text-transform: uppercase; }
        .header-periode { margin: 2px 0 10px 0; font-size: 9pt; }
    </style>
</head>
<body>
    <h2 class="text-center header-title">LAPORAN KAS HARIAN</h2>
    <p class="text-center header-periode">
        Periode: 
        <strong>{{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('j F Y') }}</strong> 
        s/d 
        <strong>{{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('j F Y') }}</strong>
    </p>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="25">NO</th>
                <th rowspan="2" width="75">TANGGAL</th>
                <th rowspan="2">KETERANGAN / DETAIL ITEM</th>
                <th colspan="2">PENDAPATAN</th>
                <th colspan="2">PENGELUARAN OPS</th>
                <th width="70">MODAL</th>
                <th rowspan="2" width="80">SALDO</th>
            </tr>
            <tr class="sub-header">
                <th width="70">TUNAI</th>
                <th width="70">NON-TUNAI</th>
                <th width="70">TUNAI</th>
                <th width="70">NON-TUNAI</th>
                <th width="70">INVESTASI</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-gray" style="font-weight: bold;">
                <td class="text-center">1</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('j M Y') }}</td>
                <td>SALDO AWAL</td>
                <td colspan="2" class="text-right">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
                <td colspan="3"></td>
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
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td class="text-center" style="font-size: 7pt;">
                                {{ $index == 0 ? \Carbon\Carbon::parse($kas->tanggal)->translatedFormat('j M Y') : '' }}
                            </td>
                            <td>{{ $detail->nama_item }} <span style="font-size: 7pt; color: #666;">({{ $detail->jumlah }}x)</span></td>
                            
                            <td class="text-right">{{ ($isMasuk && $kas->rekening->jenis != 'bank') ? number_format($nominal, 0, ',', '.') : '' }}</td>
                            <td class="text-right">{{ ($isMasuk && $kas->rekening->jenis == 'bank') ? number_format($nominal, 0, ',', '.') : '' }}</td>
                            <td class="text-right">{{ (!$isMasuk && $kas->rekening->jenis != 'bank') ? number_format($nominal, 0, ',', '.') : '' }}</td>
                            <td class="text-right">{{ (!$isMasuk && $kas->rekening->jenis == 'bank') ? number_format($nominal, 0, ',', '.') : '' }}</td>
                            <td class="text-right"></td>
                            <td class="text-right"><b>{{ number_format($runningSaldo, 0, ',', '.') }}</b></td>
                        </tr>
                    @endforeach
                @else
                    @php 
                        $isMasuk = $kas->jenis == 'masuk';
                        $runningSaldo = $isMasuk ? ($runningSaldo + $kas->total_nominal) : ($runningSaldo - $kas->total_nominal);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center" style="font-size: 7pt;">{{ \Carbon\Carbon::parse($kas->tanggal)->translatedFormat('j M Y') }}</td>
                        <td>{{ $kas->keterangan }}</td>
                        
                        <td class="text-right">{{ ($isMasuk && $kas->rekening->jenis != 'bank') ? number_format($kas->total_nominal, 0, ',', '.') : '' }}</td>
                        <td class="text-right">{{ ($isMasuk && $kas->rekening->jenis == 'bank') ? number_format($kas->total_nominal, 0, ',', '.') : '' }}</td>
                        <td class="text-right">{{ (!$isMasuk && $kas->kategori == 'operasional' && $kas->rekening->jenis != 'bank') ? number_format($kas->total_nominal, 0, ',', '.') : '' }}</td>
                        <td class="text-right">{{ (!$isMasuk && $kas->kategori == 'operasional' && $kas->rekening->jenis == 'bank') ? number_format($kas->total_nominal, 0, ',', '.') : '' }}</td>
                        <td class="text-right">{{ (!$isMasuk && $kas->kategori == 'modal') ? number_format($kas->total_nominal, 0, ',', '.') : '' }}</td>
                        <td class="text-right"><b>{{ number_format($runningSaldo, 0, ',', '.') }}</b></td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html> 