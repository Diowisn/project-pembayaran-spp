<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Pembayaran Infaq Gedung</title>
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
        .detail-table th, .detail-table td {
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
            {{-- <img src="{{ asset ('img/amanah31.png') }}" alt="Logo Sekolah" class="img"> --}}
            <h2>Assakiinah - SPP</h2>
            <p>Bendungan, Kedawung, Sragen</p>
            <p>082111050200</p>
        </div>
        <div class="header-right">
            Bukti Pembayaran SPP
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td width="25%">NISN</td>
            <td width="25%">: {{ $angsuran->siswa->nisn }}</td>
            <td width="25%">Tanggal Pembayaran</td>
            <td width="25%">: {{ $angsuran->tgl_bayar->format('d F Y') }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>: {{ $angsuran->siswa->nama }}</td>
            <td>Paket Infaq</td>
            <td>: {{ $angsuran->infaqGedung->paket ?? '-' }}</td>
        </tr>
        <tr>
            <td>Angsuran Ke</td>
            <td>: {{ $angsuran->angsuran_ke }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <p class="rincian-title">Dengan rincian pembayaran sebagai berikut:</p>

    <table class="detail-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th>Pembayaran</th>
                <th width="25%">Jumlah Bayar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Infaq Gedung - Paket {{ $angsuran->infaqGedung->paket ?? '-' }}</td>
                <td>Rp. {{ number_format($angsuran->jumlah_bayar,0,',','.') }}</td>
            </tr>
        </tbody>
    </table>

    <p class="total">Total Pembayaran : Rp. {{ number_format($angsuran->jumlah_bayar,0,',','.') }}</p>

    <div class="footer clearfix">
        <div class="left">
            <p>{{ \Carbon\Carbon::parse($angsuran->created_at)->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <div class="right">
            <p>({{ $angsuran->petugas->name ?? 'Administrator' }})</p>
        </div>
    </div>

</body>
</html>
