<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Tabungan</title>
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

        .debit {
            color: #28a745; /* Green for debit */
        }

        .kredit {
            color: #dc3545; /* Red for credit */
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
    </style>
</head>

<body>

    <div class="header">
        <div class="header-left">
            <img src="data:image/png;base64,{{ $logoData }}" style="width: 200px; height: auto; display: block; margin-bottom: 10px;" alt="Logo Sekolah">
            <p style="font-size: 12px">Jl. Raya Ngawi-Solo KM 33 Dadung RT. 1/11, Sambirejo, Mantingan, Ngawi</p>
            <p style="margin-top: 6px; font-size: 12px; line-height: 1.6;">
                <span style="margin-right: 3px;">
                    <img src="data:image/png;base64,{{ $websiteData }}" alt="Website"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;"> www.assakiinah.com
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
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:4px;"> +62 851 6258 6667
                </span>
            </p>
            <br>
            <br>
        </div>
        <div class="header-right">
            Laporan Transaksi Tabungan
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td width="25%">NISN</td>
            <td width="25%">: {{ $siswa->nisn }}</td>
            <td width="25%">Periode</td>
            <td width="25%">: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->isoFormat('D MMMM Y') : 'Awal' }} 
                s/d {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->isoFormat('D MMMM Y') : now()->isoFormat('D MMMM Y') }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>: {{ $siswa->nama }}</td>
            <td>Kelas</td>
            <td>: {{ $siswa->kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td>Saldo Akhir</td>
            <td>: Rp {{ number_format($saldo, 0, ',', '.') }}</td>
            <td>Total Transaksi</td>
            <td>: {{ $tabungan->count() }}</td>
        </tr>
    </table>

    <p class="rincian-title">Daftar Transaksi Tabungan:</p>

    <table class="detail-table">
        <thead>
            <tr>
                <th width="15%" style="text-align: center">Tanggal</th>
                <th width="15%" style="text-align: center">Jenis</th>
                <th width="15%" style="text-align: center">Debit</th>
                <th width="15%" style="text-align: center">Kredit</th>
                <th width="20%" style="text-align: center">Saldo</th>
                <th width="20%" style="text-align: center">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tabungan as $transaksi)
            <tr>
                <td style="text-align: center">{{ $transaksi->created_at->isoFormat('D MMM Y') }}</td>
                <td style="text-align: center">{{ $transaksi->debit > 0 ? 'Setoran' : 'Penarikan' }}</td>
                <td style="text-align: center" class="debit">{{ $transaksi->debit > 0 ? 'Rp '.number_format($transaksi->debit, 0, ',', '.') : '-' }}</td>
                <td style="text-align: center" class="kredit">{{ $transaksi->kredit > 0 ? 'Rp '.number_format($transaksi->kredit, 0, ',', '.') : '-' }}</td>
                <td style="text-align: center">Rp {{ number_format($transaksi->saldo, 0, ',', '.') }}</td>
                <td style="text-align: center">{{ $transaksi->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer clearfix">
        <div class="left">
            {{--  --}}
        </div>
        <div class="right">
            <p>{{ now()->isoFormat('D MMMM Y') }}</p>
            <img src="data:image/png;base64,{{ $barcodeData }}" style="width: 100px; height: auto; display: block; margin-bottom: 5px;" alt="Barcode">
            <p>({{ Auth::check() ? Auth::user()->name : 'Administrator' }})</p>
        </div>
    </div>

</body>

</html>