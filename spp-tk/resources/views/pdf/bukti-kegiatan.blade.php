<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bukti Pembayaran Kegiatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 30px;
            max-width: 700px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .header-left {
            text-align: left;
        }

        .header-left img {
            width: 60px;
            height: auto;
            object-fit: contain;
            margin-bottom: 5px;
        }

        .header-left h2 {
            margin: 2px 0;
            font-size: 16px;
        }

        .header-left p {
            margin: 2px 0;
            font-size: 11px;
        }

        .header-right {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .info-table td {
            padding: 5px 0;
        }

        .rincian-title {
            text-align: center;
            font-weight: normal;
            margin: 10px 0;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .detail-table th {
            background-color: #f2f2f2;
        }

        .total {
            margin-top: 5px;
            text-align: right;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
        }

        .footer .left {
            float: left;
        }

        .footer .right {
            float: right;
            text-align: center;
        }

        .footer .right img {
            width: 100px;
            height: auto;
            margin: 0 auto 5px;
            display: block;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .status-lunas {
            color: green;
            font-weight: bold;
        }

        .status-belum {
            color: orange;
            font-weight: bold;
        }

        .info-note {
            margin-top: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 11px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-left">
            <img src="data:image/png;base64,{{ $logoData }}"
                style="width: 200px; height: auto; display: block; margin-bottom: 10px;" alt="Logo Sekolah">
            <p style="font-size: 12px">Jl. Raya Ngawi-Solo KM 33 Dadung RT. 1/11, Sambirejo, Mantingan, Ngawi</p>
            <p style="margin-top: 6px; font-size: 12px; line-height: 1.6;">
                <span style="margin-right: 3px;">
                    <img src="data:image/png;base64,{{ $websiteData }}" alt="Website"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;">
                    www.assakiinah.com
                </span>
                <span style="margin-right: 3px;">
                    <img src="data:image/png;base64,{{ $instagramData }}" alt="Instagram"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;"> pas_asskiinah
                </span>
                <span style="margin-right: 3px;">
                    <img src="data:image/png;base64,{{ $facebookData }}" alt="Facebook"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;"> Pas Assakiinah
                </span>
                <span style="margin-right: 3px;">
                    <img src="data:image/png;base64,{{ $youtubeData }}" alt="YouTube"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;"> Assakiinah TV
                </span>
                <span>
                    <img src="data:image/png;base64,{{ $whatsappData }}" alt="WhatsApp"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:4px;"> +62 851 6258
                    6667
                </span>
            </p>
            <br>
            <br>
        </div>
        <div class="header-right">
            Bukti Pembayaran Kegiatan
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td width="25%">Tanggal Pembayaran</td>
            <td width="25%">: {{ $pembayaran->tgl_bayar->format('d F Y') }}</td>
        </tr>
        <tr>
            <td>NISN</td>
            <td>: {{ $pembayaran->siswa->nisn }}</td>
            <td>Tahun Ajaran</td>
            <td>: {{ date('Y') }}/{{ date('Y') + 1 }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>: {{ $pembayaran->siswa->nama }}</td>
            <td>Status</td>
            <td>:
                @if ($pembayaran->is_lunas)
                    <span class="status-lunas">LUNAS</span>
                @else
                    <span class="status-belum">BELUM LUNAS</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>: {{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</td>
            <td>Angsuran Ke</td>
            <td>: {{ $pembayaran->angsuran_ke }}</td>
        </tr>
    </table>

    <p class="rincian-title">Dengan rincian pembayaran sebagai berikut:</p>

    <table class="detail-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th>Kegiatan</th>
                <th width="20%">Nominal Kegiatan</th>
                <th width="20%">Jumlah Bayar</th>
                <th width="20%">Kembalian</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $pembayaran->kegiatan->nama_kegiatan }}</td>
                <td>Rp. {{ number_format($pembayaran->kegiatan->nominal, 0, ',', '.') }}</td>
                <td>Rp. {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                <td>
                    @if ($pembayaran->kembalian > 0)
                        Rp. {{ number_format($pembayaran->kembalian, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    @if (!$pembayaran->is_lunas)
        <div class="info-note">
            * Pembayaran ini merupakan angsuran. Sisa tagihan: Rp
            {{ number_format($pembayaran->kegiatan->nominal - $pembayaran->jumlah_bayar, 0, ',', '.') }}
        </div>
    @endif

    <p class="total">Total Pembayaran : Rp. {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</p>
    <p class="total">Kembalian : Rp. {{ number_format($pembayaran->kembalian, 0, ',', '.') }}</p>

    <div class="footer clearfix">
        <div class="left">
        </div>
        <div class="right">
            <p>{{ \Carbon\Carbon::parse($pembayaran->created_at)->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            <img src="data:image/png;base64,{{ $barcodeData }}"
                style="width: 100px; height: auto; display: block; margin-bottom: 5px;" alt="Barcode">
            <p>({{ Auth::check() ? Auth::user()->name : $pembayaran->petugas->name ?? 'Administrator' }})</p>
        </div>
    </div>

</body>

</html>
