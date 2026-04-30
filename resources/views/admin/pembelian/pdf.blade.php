<!DOCTYPE html>
<html>
<head>
    <title>Invoice Pembelian - {{ $pembelian->id }}</title>
    <style>
        @page { 
            size: 210mm 148mm; 
            margin: 10mm; 
        }
        
        body { 
            font-family: 'Courier', monospace; 
            font-size: 9pt; 
            margin: 0; 
            padding: 0;
            color: #000;
            line-height: 1.1;
        }

        #watermark {
            position: fixed;
            top: 20%;
            left: 25%;
            width: 250px;
            opacity: 0.06;
            z-index: -1000;
        }

        .header-table { width: 100%; border-bottom: 2px double #000; margin-bottom: 8px; padding-bottom: 5px; }
        .logo-top { width: 50px; height: auto; }
        .company-name { font-size: 12pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .company-info { font-size: 7pt; }
        .document-title { font-size: 16pt; font-weight: bold; text-align: right; }
        
        .info-table { width: 100%; margin-bottom: 8px; }
        .info-table td { vertical-align: top; font-size: 8pt; }
        .label-box { font-weight: bold; text-decoration: underline; margin-bottom: 3px; display: block; }
        
        .nota-info-table { width: 100%; border-spacing: 0; }
        .nota-info-table td { padding: 1px 0; }

        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .main-table th { 
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px; 
            text-align: left;
            font-size: 8pt;
        }
        .main-table td { padding: 4px; font-size: 8pt; vertical-align: top; border-bottom: 1px dotted #ccc; }

        .summary-container { float: right; width: 45%; margin-top: 5px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 2px 5px; font-size: 9pt; }
        .border-total { border-top: 1px solid #000; border-bottom: 1px solid #000; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
    </style>
</head>
<body>
    

    <table class="header-table">
        <tr>
            <td width="10%">
                @if($konfigurasi && $konfigurasi->logo)
                <img src="{{ base_path('uploads/logo/' . basename($konfigurasi->logo)) }}" class="logo-top">
                @endif
            </td>
            <td width="50%">
                <h1 class="company-name">{{ $konfigurasi->nama_perusahaan ?? 'NAMA PERUSAHAAN' }}</h1>
                <div class="company-info">
                    {{ $konfigurasi->alamat ?? '-' }}<br>
                    Telp: {{ $konfigurasi->telepon ?? '-' }}
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
                <th width="45%">NAMA BAHAN</th>
                <th width="15%" class="text-center">QTY</th>
                <th width="15%" class="text-right">HARGA</th>
                <th width="20%" class="text-right">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembelian->detail as $index => $item)
            <tr>
                <td width="5%" class="text-center">{{ $index + 1 }}</td>
                <td width="45%" class="uppercase">
                    {{ $item->bahan->nama }}<br>
                    <small style="font-size: 7pt;">Satuan: {{ $item->bahan->satuan }}</small>
                </td>
                <td width="15%" class="text-">{{ $item->qty + 0 }}</td>
                <td width="15%" class="text-">{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td width="20%" class="text-">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-container">
        <table class="summary-table">
            <tr>
                <td class="bold text-right">TOTAL PEMBELIAN:</td>
                <td class="bold text- border-total" style="width: 50%;">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($pembelian->keterangan)
    <div style="margin-top: 10px; font-size: 8pt;">
        <span class="bold">Keterangan:</span><br>
        <i>{{ $pembelian->keterangan }}</i>
    </div>
    @endif

    <div style="margin-top: 20px; font-size: 7pt; font-style: italic; border-top: 1px solid #eee; padding-top: 5px;">
        * Dokumen ini merupakan bukti sah pembelian bahan baku.<br>
        * Dicetak secara otomatis oleh sistem pada {{ date('d/m/Y H:i') }}.
    </div>
</body>
</html>