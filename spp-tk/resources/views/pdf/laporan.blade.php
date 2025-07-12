<!doctype html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Laporan pembayaran SPP</title>

</head>

<body>

    <style>
        .page-break {
            page-break-after: always;
        }

        .text-center {
            text-align: center;
        }

        .text-header {
            font-size: 1.1rem;
        }

        .size2 {
            font-size: 1.4rem;
        }

        .border-bottom {
            border-bottom: 1px black solid;
        }

        .border {
            border: 2px block solid;
        }

        .border-top {
            border-top: 1px black solid;
        }

        .float-right {
            float: right;
        }

        .mt-4 {
            margin-top: 4px;
        }

        .mx-1 {
            margin: 1rem 0 1rem 0;
        }

        .mr-1 {
            margin-right: 1rem;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        ml-2 {
            margin-left: 2rem;
        }

        .ml-min-5 {
            margin-left: -5px;
        }

        .text-uppercase {
            font: uppercase;
        }

        .d1 {
            font-size: 2rem;
        }

        .img {
            position: absolute;
        }

        .link {
            font-style: underline;
        }

        .text-desc {
            font-size: 14px;
        }

        .text-bold {
            font-style: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .table-center {
            margin-left: auto;
            margin-right: auto;
        }

        .mb-1 {
            margin-bottom: 1rem;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12px;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .detail-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>

    <!-- header -->
    <div class="text-center">
        <img src="{{ public_path('img/logo-amanah.png') }}" class="img" alt="logo.png" width="90">
        <div style="margin-left:6rem;">
            <span class="text-header text-bold text-danger">
                <br>
                <span class="size2">PAUD PESANTREN ANAK SHOLEH ASSAKIINAH</span> <br>
            </span>
            <span class="text-desc">Jl. Raya Ngawi-Solo KM 33 Dadung RT. 1/11, Sambirejo, Mantingan, Ngawi<br>
                <img src="{{ public_path('img/icons/website.png') }}" width="16"
                    style="vertical-align: middle; margin-top: 10px"> <span>www.assakiinah.com </span>
                <img src="{{ public_path('img/icons/instagram.png') }}" width="16"
                    style="vertical-align: middle; margin-top: 10px"> <span>pas_asskiinah </span>
                <img src="{{ public_path('img/icons/facebook.png') }}" width="16"
                    style="vertical-align: middle; margin-top: 10px"> <span>Pas Assakiinah </span>
                <img src="{{ public_path('img/icons/youtube.png') }}" width="16"
                    style="vertical-align: middle; margin-top: 10px"> <span>Assakiinah TV </span>
                <img src="{{ public_path('img/icons/whatsapp.png') }}" width="16"
                    style="vertical-align: middle; margin-top: 10px"> <span>+62 851 6258 6667</span>
            </span>
            <br>
        </div>
    </div>
    <div>
        <!-- /header -->

        <hr class="border">

        <!-- content -->

        <div class="size2 text-center mb-1">LAPORAN PEMBAYARAN SPP</div>

        <table class="detail-table">
            <thead>
                <tr>
                    <th>Petugas</th>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>SPP Bulan</th>
                    <th>SPP Nominal</th>
                    <th>Konsumsi Nominal</th>
                    <th>Fullday Nominal</th>
                    <th>Nominal Bayar</th>
                    <th>Tanggal Bayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pembayaran as $val)
                    <tr>
                        <td>{{ $val->petugas->name }}</td>
                        <td>{{ $val->siswa->nama }}</td>
                        <td>{{ $val->siswa->kelas->nama_kelas }}</td>
                        <td>{{ $val->bulan }}</td>
                        <td>Rp. {{ number_format($val->siswa->spp->nominal_spp, 0, ',', '.') }}</td>
                        <td>Rp. {{ number_format($val->siswa->spp->nominal_konsumsi, 0, ',', '.') }}</td>
                        <td>Rp. {{ number_format($val->siswa->spp->nominal_fullday, 0, ',', '.') }}</td>
                        <td>Rp. {{ number_format($val->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>{{ $val->created_at->format('d M, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- /content -->

        <!-- footer -->
        <div>Pembuat : {{ auth()->user()->name }}</div>
        <!-- /footer -->
</body>

</html>
