<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0.5cm; }
        body { font-family: sans-serif; font-size: 8pt; color: #333; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #444; padding: 4px; word-wrap: break-word; }
        th { background-color: #FFC000; text-align: center; text-transform: uppercase; font-weight: bold; }
        .bg-gray { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .sub-header { background-color: #FFF2CC; font-size: 7pt; }
    </style>
</head>
<body>
    <h2 class="text-center">LAPORAN KAS HARIAN</h2>
    <p class="text-center">Periode: {{ $tgl_mulai }} s/d {{ $tgl_selesai }}</p>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="25">NO</th>
                <th rowspan="2" width="60">TANGGAL</th>
                <th rowspan="2">KETERANGAN / DETAIL ITEM</th>
                <th colspan="2">PENDAPATAN</th>
                <th colspan="2">PENGELUARAN OPS</th>
                <th colspan="1">INVESTASI/MODAL</th>
                <th rowspan="2" width="80">SALDO</th>
            </tr>
            <tr class="sub-header">
                <th width="70">TUNAI</th>
                <th width="70">NON-TUNAI</th>
                <th width="70">TUNAI</th>
                <th width="70">NON-TUNAI</th>
                <th width="70">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-gray" style="font-weight: bold;">
                <td class="text-center">1</td>
                <td class="text-center">{{ date('d/m/y', strtotime($tgl_mulai)) }}</td>
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
                {{-- Cek apakah dia punya detail item (Operasional) --}}
                @if($kas->kategori == 'operasional' && $kas->details->count() > 0)
                    @foreach($kas->details as $index => $detail)
                        @php 
                            $isMasuk = $kas->jenis == 'masuk';
                            $nominal = $detail->subtotal;
                            $runningSaldo = $isMasuk ? ($runningSaldo + $nominal) : ($runningSaldo - $nominal);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td class="text-center">{{ $index == 0 ? date('d/m/y', strtotime($kas->tanggal)) : '' }}</td>
                            <td>{{ $detail->nama_item }} ({{ $detail->jumlah }} x {{ number_format($detail->harga, 0, ',', '.') }})</td>
                            
                            {{-- Kolom Pendapatan --}}
                            <td class="text-right">{{ ($isMasuk && $kas->rekening->jenis != 'bank') ? number_format($nominal, 0, ',', '.') : '' }}</td>
                            <td class="text-right">{{ ($isMasuk && $kas->rekening->jenis == 'bank') ? number_format($nominal, 0, ',', '.') : '' }}</td>
                            
                            {{-- Kolom Pengeluaran Ops --}}
                            <td class="text-right">{{ (!$isMasuk && $kas->rekening->jenis != 'bank') ? number_format($nominal, 0, ',', '.') : '' }}</td>
                            <td class="text-right">{{ (!$isMasuk && $kas->rekening->jenis == 'bank') ? number_format($nominal, 0, ',', '.') : '' }}</td>
                            
                            {{-- Kolom Investasi --}}
                            <td class="text-right"></td>
                            
                            <td class="text-right"><b>{{ number_format($runningSaldo, 0, ',', '.') }}</b></td>
                        </tr>
                    @endforeach
                @else
                    {{-- Baris Tanpa Detail (Modal/Flat) --}}
                    @php 
                        $isMasuk = $kas->jenis == 'masuk';
                        $runningSaldo = $isMasuk ? ($runningSaldo + $kas->total_nominal) : ($runningSaldo - $kas->total_nominal);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ date('d/m/y', strtotime($kas->tanggal)) }}</td>
                        <td>{{ $kas->keterangan }}</td>
                        
                        {{-- Pendapatan --}}
                        <td class="text-right">{{ ($isMasuk && $kas->rekening->jenis != 'bank') ? number_format($kas->total_nominal, 0, ',', '.') : '' }}</td>
                        <td class="text-right">{{ ($isMasuk && $kas->rekening->jenis == 'bank') ? number_format($kas->total_nominal, 0, ',', '.') : '' }}</td>
                        
                        {{-- Pengeluaran Ops --}}
                        <td class="text-right">{{ (!$isMasuk && $kas->kategori == 'operasional' && $kas->rekening->jenis != 'bank') ? number_format($kas->total_nominal, 0, ',', '.') : '' }}</td>
                        <td class="text-right">{{ (!$isMasuk && $kas->kategori == 'operasional' && $kas->rekening->jenis == 'bank') ? number_format($kas->total_nominal, 0, ',', '.') : '' }}</td>

                        {{-- Investasi --}}
                        <td class="text-right">{{ (!$isMasuk && $kas->kategori == 'modal') ? number_format($kas->total_nominal, 0, ',', '.') : '' }}</td>
                        
                        <td class="text-right"><b>{{ number_format($runningSaldo, 0, ',', '.') }}</b></td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>