<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Pembayaran</title>
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
            Bukti Pembayaran
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td width="25%">NISN</td>
            <td width="25%">: {{ $pembayaran->siswa->nisn }}</td>
            <td width="25%">Tanggal Pembayaran</td>
            <td width="25%">: {{ $pembayaran->tgl_bayar->format('d F Y') }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>: {{ $pembayaran->siswa->nama }}</td>
            <td>Tahun Ajaran</td>
            <td>: {{ $pembayaran->tahun }}/{{ $pembayaran->tahun+1 }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>: {{ $pembayaran->siswa->kelas->nama_kelas }}</td>
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
                <th width="25%">Total Tagihan</th>
                <th width="25%">Jumlah Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>SPP - T.A {{ $pembayaran->tahun }}/{{ $pembayaran->tahun+1 }} - ({{ ucfirst($pembayaran->bulan) }} {{ $pembayaran->tahun }})</td>
                <td>Rp. {{ number_format($pembayaran->nominal_spp,0,',','.') }}</td>
                <td>Rp. {{ number_format($pembayaran->nominal_spp,0,',','.') }}</td>
            </tr>
            @if($pembayaran->nominal_konsumsi > 0)
            <tr>
                <td>2</td>
                <td>Konsumsi - T.A {{ $pembayaran->tahun }}/{{ $pembayaran->tahun+1 }}</td>
                <td>Rp. {{ number_format($pembayaran->nominal_konsumsi,0,',','.') }}</td>
                <td>Rp. {{ number_format($pembayaran->nominal_konsumsi,0,',','.') }}</td>
            </tr>
            @endif
            @if($pembayaran->nominal_fullday > 0)
            <tr>
                <td>3</td>
                <td>Fullday - T.A {{ $pembayaran->tahun }}/{{ $pembayaran->tahun+1 }}</td>
                <td>Rp. {{ number_format($pembayaran->nominal_fullday,0,',','.') }}</td>
                <td>Rp. {{ number_format($pembayaran->nominal_fullday,0,',','.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <p class="total">Total Pembayaran : Rp. {{ number_format($pembayaran->jumlah_bayar,0,',','.') }}</p>
    <p class="total">Kembalian : Rp. {{ number_format($pembayaran->kembalian,0,',','.') }}</p>

    <div class="footer clearfix">
        <div class="left">
            <p>{{ \Carbon\Carbon::parse($pembayaran->created_at)->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <div class="right">
            <p>({{ $pembayaran->petugas->name ?? 'Administrator' }})</p>
        </div>
    </div>

</body>
</html>