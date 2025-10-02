<!doctype html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Tunggakan SPP - {{ $filter_text }}</title>
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

        .ml-2 {
            margin-left: 2rem;
        }

        .ml-min-5 {
            margin-left: -5px;
        }

        .text-uppercase {
            text-transform: uppercase;
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
            font-weight: bold;
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

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .summary-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
        }

        .bg-warning {
            background-color: #fff3cd !important;
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
    <!-- /header -->

    <hr class="border">

    <!-- content -->
    <div class="size2 text-center mb-1">LAPORAN TUNGGAKAN PEMBAYARAN SPP</div>

    <!-- Info Filter -->
    <div class="text-center mb-1">
        <strong>{{ $filter_text }}</strong><br>
        <small>Tanggal Cetak: {{ $tanggal_cetak }}</small>
    </div>

    <!-- Summary -->
    <div class="bg-warning p-3 mb-3" style="border: 1px solid #ffeaa7;">
        <strong>SUMMARY TUNGGAKAN</strong><br>
        Total Siswa dengan Tunggakan: <strong>{{ $total_siswa }} siswa</strong><br>
        Total Nominal Tunggakan: <strong>Rp {{ number_format($total_tunggakan, 0, ',', '.') }}</strong>
    </div>

    <!-- Tabel Detail Tunggakan -->
    <table class="detail-table">
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Bulan Tunggakan</th>
                <th>Jumlah Bulan</th>
                <th>Total Tunggakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswa_belum_bayar as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item['siswa']->nisn }}</td>
                    <td>{{ $item['siswa']->nama }}</td>
                    <td>{{ $item['siswa']->kelas->nama_kelas }}</td>
                    <td>
                        @foreach ($item['tunggakan'] as $tunggakan)
                            {{ $tunggakan['nama_bulan'] }}@if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </td>
                    <td>{{ $item['jumlah_bulan_tunggakan'] }} bulan</td>
                    <td>Rp {{ number_format($item['total_tunggakan'], 0, ',', '.') }}</td>
                </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada siswa dengan tunggakan SPP</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if (count($siswa_belum_bayar) > 0)
            <!-- Ringkasan per Kelas -->
            <table class="summary-table mt-4">
                <thead>
                    <tr>
                        <th colspan="4">RINGKASAN TUNGGAKAN PER KELAS</th>
                    </tr>
                    <tr>
                        <th>Kelas</th>
                        <th>Jumlah Siswa</th>
                        <th>Total Tunggakan</th>
                        <th>Rata-rata per Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedByKelas = collect($siswa_belum_bayar)->groupBy(function ($item) {
                            return $item['siswa']->kelas->nama_kelas;
                        });
                    @endphp
                    @foreach ($groupedByKelas as $kelasName => $items)
                        @php
                            $jumlahSiswa = count($items);
                            $totalKelas = collect($items)->sum('total_tunggakan');
                            $rataRata = $jumlahSiswa > 0 ? $totalKelas / $jumlahSiswa : 0;
                        @endphp
                        <tr>
                            <td>{{ $kelasName }}</td>
                            <td class="text-right">{{ $jumlahSiswa }} siswa</td>
                            <td class="text-right">Rp {{ number_format($totalKelas, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($rataRata, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Distribusi Tunggakan per Bulan -->
            <table class="summary-table mt-4">
                <thead>
                    <tr>
                        <th colspan="3">DISTRIBUSI TUNGGAKAN PER BULAN</th>
                    </tr>
                    <tr>
                        <th>Bulan</th>
                        <th>Jumlah Siswa</th>
                        <th>Total Tunggakan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $bulanTunggakan = [];
                        foreach ($siswa_belum_bayar as $item) {
                            foreach ($item['tunggakan'] as $tunggakan) {
                                $bulan = $tunggakan['nama_bulan'];
                                if (!isset($bulanTunggakan[$bulan])) {
                                    $bulanTunggakan[$bulan] = [
                                        'jumlah_siswa' => 0,
                                        'total' => 0,
                                    ];
                                }
                                $bulanTunggakan[$bulan]['jumlah_siswa']++;
                                $bulanTunggakan[$bulan]['total'] += $tunggakan['nominal_spp'];
                            }
                        }
                    @endphp
                    @foreach ($bulanTunggakan as $bulan => $data)
                        <tr>
                            <td>{{ $bulan }}</td>
                            <td class="text-right">{{ $data['jumlah_siswa'] }} siswa</td>
                            <td class="text-right">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- footer -->
        <div class="footer">
            <div style="float: right; text-align: center;">
                <p>{{ \Carbon\Carbon::parse($tanggal_surat)->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                <br><br>
                <p>({{ Auth::check() ? Auth::user()->name : $item->petugas->name ?? 'Administrator' }})</p>
            </div>
            <div style="clear: both;"></div>
        </div>
        <!-- /footer -->
</body>

</html>
