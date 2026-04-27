<!DOCTYPE html>
<html>
<head>
    <title>Invoice Pembelian #{{ $pembelian->id }}</title>
    <style>
        @page {
            margin: 0cm; /* Reset margin untuk background full */
        }
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 11px; 
            color: #333; 
            margin: 1.5cm; /* Margin konten utama */
        }
        
        /* Watermark Logo Background */
        .watermark {
            position: fixed;
            top: 25%;
            left: 10%;
            width: 80%;
            height: 50%;
            z-index: -1000;
            opacity: 0.1; /* Transparansi logo di background */
            text-align: center;
        }
        .watermark img {
            width: 400px; /* Sesuaikan ukuran logo background */
            filter: grayscale(100%);
        }

        .header-table { width: 100%; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .company-logo { width: 80px; height: auto; }
        .company-name { font-size: 18px; font-weight: bold; color: #1e40af; text-transform: uppercase; }
        .invoice-label { font-size: 22px; font-weight: bold; color: #444; text-align: right; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-box { border: 1px solid #ddd; padding: 10px; border-radius: 5px; }
        
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .main-table th { background: #1e40af; color: white; padding: 10px; border: 1px solid #1e40af; text-align: left; }
        .main-table td { padding: 10px; border: 1px solid #ddd; background: rgba(255, 255, 255, 0.7); }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .grand-total-row { background: #f0f4ff !important; font-size: 13px; font-weight: bold; }
        .footer-sign { margin-top: 50px; width: 100%; }
        .footer-sign td { width: 50%; text-align: center; }
        
        .status-badge {
            padding: 5px 10px;
            border: 1px solid #000;
            text-transform: uppercase;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>

    @if($konfigurasi && $konfigurasi->logo)
    <div class="watermark">
        <img src="{{ public_path('storage/'.$konfigurasi->logo) }}">
    </div>
    @endif

    <table class="header-table">
        <tr>
            <td style="width: 100px;">
                @if($konfigurasi && $konfigurasi->logo)
                    <img src="{{ public_path('storage/'.$konfigurasi->logo) }}" class="company-logo">
                @endif
            </td>
            <td>
                <span class="company-name">{{ $konfigurasi->nama_perusahaan ?? 'IKN COCO COIR' }}</span><br>
                {{ $konfigurasi->alamat ?? 'Alamat belum diatur' }}<br>
                Email: {{ $konfigurasi->email ?? '-' }} | Telp: {{ $konfigurasi->telepon ?? '-' }}
            </td>
            <td class="invoice-label">
                INVOICE<br>
                <span style="font-size: 12px; color: #666;">#PB-{{ str_pad($pembelian->id, 5, '0', STR_PAD_LEFT) }}</span>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td style="width: 50%; padding-right: 10px;">
                <div class="info-box">
                    <small class="font-bold">DITERIMA DARI (SUPPLIER):</small><br>
                    <span class="font-bold" style="font-size: 13px; color: #1e40af;">{{ $pembelian->supplier->nama }}</span><br>
                    {{ $pembelian->supplier->alamat ?? 'Alamat tidak tersedia' }}<br>
                    Telp: {{ $pembelian->supplier->telp ?? '-' }}
                </div>
            </td>
            <td style="width: 50%; padding-left: 10px; vertical-align: top;">
                <table style="width: 100%;">
                    <tr>
                        <td class="font-bold">Tanggal Transaksi</td>
                        <td>: {{ date('d F Y', strtotime($pembelian->tanggal)) }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Status Pembayaran</td>
                        <td>: <span class="status-badge">{{ $pembelian->status_pembayaran }}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">NO</th>
                <th>DESKRIPSI BAHAN</th>
                <th class="text-right">HARGA SATUAN</th>
                <th class="text-center">QTY</th>
                <th class="text-right">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembelian->detail as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <span class="font-bold">{{ $item->bahan->nama }}</span><br>
                    <small>Satuan: {{ $item->bahan->satuan }}</small>
                </td>
                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total-row">
                <td colspan="4" class="text-right">TOTAL AKHIR PEMBELIAN</td>
                <td class="text-right" style="color: #1e40af;">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @if($pembelian->keterangan)
    <div style="margin-top: 15px;">
        <small class="font-bold">Catatan:</small><br>
        <div style="font-style: italic; color: #666;">"{{ $pembelian->keterangan }}"</div>
    </div>
    @endif

    <table class="footer-sign">
        <tr>
            <td>
                Admin {{ $konfigurasi->nama_perusahaan ?? 'IKN' }},<br><br><br><br><br>
                ( ____________________ )
            </td>
            <td>
                Supplier,<br><br><br><br><br>
                ( <strong>{{ $pembelian->supplier->nama }}</strong> )
            </td>
        </tr>
    </table>

    <div style="position: fixed; bottom: 1cm; left: 1.5cm; right: 1.5cm; border-top: 1px solid #ddd; padding-top: 5px;">
        <small style="color: #999;">Invoice ini dihasilkan secara otomatis oleh Sistem IKN COCO COIR pada {{ date('d/m/Y H:i') }}</small>
    </div>

</body>
</html>