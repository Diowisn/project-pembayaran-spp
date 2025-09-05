<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Riwayat Pembayaran SPP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
            max-width: 100%;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .header-left img {
            width: 80px;
            height: auto;
            margin-bottom: 5px;
        }

        .header-left p {
            margin: 2px 0;
            font-size: 11px;
        }

        .header-right {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }

        .detail-table th, .detail-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }

        .detail-table th {
            background: #f2f2f2;
        }

        .total {
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
        }

        .status-lunas { color: green; font-weight: bold; }
        .status-belum { color: red; font-weight: bold; }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            page-break-inside: avoid;
        }
        .footer .left { float: left; }
        .footer .right { float: right; text-align: center; }
        .footer img { width: 80px; margin: 5px auto; display: block; }
        .clearfix::after { content: ""; clear: both; display: table; }
        
        /* Untuk mencegah pemotongan baris saat cetak */
        tr { page-break-inside: avoid; }
        
        /* Media query khusus untuk cetak */
        @media print {
            body {
                padding: 0;
                margin: 0;
                font-size: 11px;
            }
            .header {
                margin-top: 0;
            }
            .detail-table {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            @if(isset($logoData) && !empty($logoData))
            <img src="data:image/png;base64,{{ $logoData }}" style="width: 200px; height: auto; display: block; margin-bottom: 10px;" alt="Logo Sekolah">
            @else
            <div style="width: 80px; height: 60px; background: #f0f0f0; display: block; margin-bottom: 10px; text-align: center; line-height: 60px;">LOGO</div>
            @endif
            
            <p style="font-size: 12px">Jl. Raya Ngawi-Solo KM 33 Dadung RT. 1/11, Sambirejo, Mantingan, Ngawi</p>
            <p style="margin-top: 6px; font-size: 12px; line-height: 1.6;">
                <span style="margin-right: 3px;">
                    @if(isset($websiteData) && !empty($websiteData))
                    <img src="data:image/png;base64,{{ $websiteData }}" alt="Website"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;">
                    @else
                    [Web]
                    @endif
                    www.assakiinah.com
                </span>
                <span style="margin-right: 3px;">
                    @if(isset($instagramData) && !empty($instagramData))
                    <img src="data:image/png;base64,{{ $instagramData }}" alt="Instagram"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;">
                    @else
                    [IG]
                    @endif
                    pas_asskiinah
                </span>
                <span style="margin-right: 3px;">
                    @if(isset($facebookData) && !empty($facebookData))
                    <img src="data:image/png;base64,{{ $facebookData }}" alt="Facebook"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;">
                    @else
                    [FB]
                    @endif
                    Pas Assakiinah
                </span>
                <span style="margin-right: 3px;">
                    @if(isset($youtubeData) && !empty($youtubeData))
                    <img src="data:image/png;base64,{{ $youtubeData }}" alt="YouTube"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;">
                    @else
                    [YT]
                    @endif
                    Assakiinah TV
                </span>
                <span>
                    @if(isset($whatsappData) && !empty($whatsappData))
                    <img src="data:image/png;base64,{{ $whatsappData }}" alt="WhatsApp"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:4px;">
                    @else
                    [WA]
                    @endif
                    +62 851 6258 6667
                </span>
            </p>
        </div>
        <br>
        <div class="header-right">
            Rekap Pembayaran SPP
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td width="25%">NISN</td>
            <td width="25%">: {{ $siswa->nisn ?? '-' }}</td>
            <td width="25%">Nama</td>
            <td width="25%">: {{ $siswa->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>: {{ $siswa->kelas->nama_kelas ?? '-' }}</td>
            <td>Total Riwayat</td>
            <td>: {{ count($riwayatPembayaran) }} Pembayaran</td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td colspan="3">: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</td>
        </tr>
    </table>

    <p style="text-align: center; margin: 10px 0;">Daftar Riwayat Pembayaran SPP:</p>

    <table class="detail-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Bulan/Tahun</th>
                <th width="10%">Pembayaran</th>
                <th width="13%">Total Tagihan</th>
                <th width="13%">Jumlah Bayar</th>
                <th width="13%">Kembalian</th>
                <th width="10%">Status</th>
                <th width="12%">Tanggal Bayar</th>
                <th width="12%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $counter = 1; 
                $totalBayar = 0; 
                $totalTagihan = 0; 
                $totalKembalian = 0; 
            @endphp

            @if(count($riwayatPembayaran) > 0)
                @foreach($riwayatPembayaran as $pembayaran)
                    @php
                        $tagihan = ($pembayaran->nominal_spp ?? 0) + 
                                  ($pembayaran->nominal_konsumsi ?? 0) + 
                                  ($pembayaran->nominal_fullday ?? 0) + 
                                  ($pembayaran->nominal_inklusi ?? 0);
                        $totalTagihan += $tagihan;
                        $totalBayar += $pembayaran->jumlah_bayar ?? 0;
                        $totalKembalian += $pembayaran->kembalian ?? 0;
                        
                        $tanggalBayar = $pembayaran->tgl_bayar ?? $pembayaran->created_at;
                    @endphp

                    <!-- Baris utama pembayaran -->
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>{{ ucfirst($pembayaran->bulan) }} {{ $pembayaran->tahun }}</td>
                        <td>SPP</td>
                        <td>Rp {{ number_format($pembayaran->nominal_spp ?? 0, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($pembayaran->kembalian ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @if($pembayaran->is_lunas)
                                <span class="status-lunas">LUNAS</span>
                            @else
                                <span class="status-belum">BELUM</span>
                            @endif
                        </td>
                        <td>{{ $tanggalBayar->format('d/m/Y') }}</td>
                        <td>{{ $pembayaran->petugas->name ?? 'Admin' }}</td>
                    </tr>

                    <!-- Detail komponen pembayaran -->
                    @if(($pembayaran->nominal_konsumsi ?? 0) > 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Konsumsi</td>
                        <td>Rp {{ number_format($pembayaran->nominal_konsumsi, 0, ',', '.') }}</td>
                        <td colspan="5"></td>
                    </tr>
                    @endif

                    @if(($pembayaran->nominal_fullday ?? 0) > 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Fullday</td>
                        <td>Rp {{ number_format($pembayaran->nominal_fullday, 0, ',', '.') }}</td>
                        <td colspan="5"></td>
                    </tr>
                    @endif

                    @if(($pembayaran->nominal_inklusi ?? 0) > 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Inklusi</td>
                        <td>Rp {{ number_format($pembayaran->nominal_inklusi, 0, ',', '.') }}</td>
                        <td colspan="5"></td>
                    </tr>
                    @endif

                @endforeach

                <!-- Total -->
                <tr style="background: #f2f2f2; font-weight: bold;">
                    <td colspan="3" style="text-align:center;">TOTAL</td>
                    <td>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($totalKembalian, 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
            @else
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada data pembayaran</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer clearfix">
        <div class="left">
            Dicetak pada: {{ date('d/m/Y H:i:s') }}
        </div>
        <div class="right">
            <p>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            @if(isset($barcodeData) && !empty($barcodeData))
            <img src="data:image/png;base64,{{ $barcodeData }}" alt="Barcode">
            @else
            <div style="width: 80px; height: 40px; background: #f0f0f0; text-align: center; line-height: 40px;">BARCODE</div>
            @endif
            <p>({{ Auth::check() ? Auth::user()->name : 'Administrator' }})</p>
        </div>
    </div>

</body>
</html>