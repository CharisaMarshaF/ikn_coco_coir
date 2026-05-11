<!DOCTYPE html>
<html>
<head>
    <title>Invoice Pembelian - {{ $pembelian->id }}</title>
    <style>
        /* Pengaturan Ukuran Kertas A4 Portrait */
        @page { 
            size: A4 portrait; 
            margin: 15mm; 
        }
        
        body { 
            font-family: 'Courier', monospace; 
            font-size: 10pt; 
            margin: 0; 
            padding: 0;
            color: #000;
            line-height: 1.2;
        }

        .header-table { width: 100%; border-bottom: 2px double #000; margin-bottom: 15px; padding-bottom: 10px; }
        .logo-top { width: 60px; height: auto; }
        .company-name { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .company-info { font-size: 8pt; }
        .document-title { font-size: 18pt; font-weight: bold; text-align: right; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; font-size: 9pt; }
        .label-box { font-weight: bold; text-decoration: underline; margin-bottom: 5px; display: block; }
        
        .nota-info-table { width: 100%; border-spacing: 0; }
        .nota-info-table td { padding: 2px 0; }

        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .main-table th { 
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 8px 5px; 
            font-size: 9pt;
        }
        .main-table td { 
            padding: 8px 5px; 
            font-size: 9pt; 
            vertical-align: top; 
            border-bottom: 1px dotted #ccc; 
        }

        .summary-container { float: right; width: 45%; margin-top: 10px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 4px 5px; font-size: 10pt; }
        .border-total { border-top: 1px solid #000; border-bottom: 1px solid #000; }
        
        /* Area Tanda Tangan Tetap Style Minimalis */
        .signature-section { width: 100%; margin-top: 50px; }
        .sig-box { float: left; width: 30%; text-align: center; font-size: 9pt; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
    </style>
</head>
<body>
    
    <table class="header-table">
        <tr>
            <td width="12%">
                @if($konfigurasi && $konfigurasi->logo)
                <img src="{{ base_path('uploads/logo/' . basename($konfigurasi->logo)) }}" class="logo-top">
                @endif
            </td>
            <td width="48%">
                <h1 class="company-name">{{ $konfigurasi->nama_cv ?? 'NAMA PERUSAHAAN' }}</h1>
                <div class="company-info">
                    {{ $konfigurasi->alamat ?? '-' }}<br>
                    Telp: {{ $konfigurasi->telepon ?? '-' }} {{ $konfigurasi->email ? ' | Email: ' . $konfigurasi->email : '' }}<br>
                    {{ $konfigurasi->website }}
                </div>
            </td>
            <td class="document-title">
                INVOICE PEMBELIAN
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="55%">
                <span class="label-box">DITERIMA DARI (SUPPLIER):</span>
                <table width="100%" style="border-spacing: 0;">
                    <tr>
                        <td width="30%">Nama</td>
                        <td>: <span class="bold uppercase">{{ $pembelian->supplier->nama }}</span></td>
                    </tr>
                    <tr>
                        <td valign="top">Alamat</td>
                        <td>: {{ $pembelian->supplier->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Telepon</td>
                        <td>: {{ $pembelian->supplier->telp ?? '-' }}</td>
                    </tr>
                </table>
            </td>

            <td width="45%">
                <div style="float: right; width: 95%;">
                    <table class="nota-info-table">
                        <tr>
                            <td width="45%">No. Transaksi</td>
                            <td>: <span class="bold">#PB-{{ str_pad($pembelian->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ date('d/m/Y', strtotime($pembelian->tanggal)) }}</td>
                        </tr>
                        <tr>
                            <td>Status Bayar</td>
                            <td>: <span class="uppercase bold">{{ $pembelian->status_pembayaran }}</span></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">NO</th>
                <th width="45%" class="text-left">NAMA BAHAN</th>
                <th width="15%" class="text-center">QTY</th>
                <th width="15%" class="text-right">HARGA</th>
                <th width="20%" class="text-right">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembelian->detail as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left uppercase">
                    {{ $item->bahan->nama }}
                </td>
                <td class="text-center">{{ $item->qty + 0 }} {{ $item->bahan->satuan }}</td>
                <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-container">
        <table class="summary-table">
            <tr>
                <td class="bold text-right">TOTAL PEMBELIAN:</td>
                <td class="bold text-right border-total" style="width: 55%;">
                    Rp {{ number_format($pembelian->total, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($pembelian->keterangan)
    <div style="margin-top: 15px; font-size: 9pt;">
        <span class="bold">Keterangan:</span><br>
        <i>{{ $pembelian->keterangan }}</i>
    </div>
    @endif

    <div class="signature-section">
        <div class="sig-box">
            Diterima Oleh,<br><br><br><br><br>
            ( .................... )
        </div>
        <div class="sig-box" style="float: right;">
            Hormat Kami,<br><br><br><br><br>
            ( {{ $pembelian->supplier->nama }} )
        </div>
    </div>

    <div style="clear: both;"></div>

    <div style="margin-top: 50px; font-size: 8pt; font-style: italic; border-top: 1px solid #eee; padding-top: 10px;">
        * Dokumen ini merupakan bukti sah pembelian bahan baku.<br>
        * Dicetak secara otomatis oleh sistem pada {{ date('d/m/Y H:i') }}.
    </div>
</body>
</html>