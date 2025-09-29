<!doctype html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan pembayaran SPP - {{ $filter_text }}</title>
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
    <div class="size2 text-center mb-1">LAPORAN PEMBAYARAN SPP</div>

    <!-- Info Filter -->
    <div class="text-center mb-1">
        <strong>Filter: {{ $filter_text }}</strong><br>
        <small>Tanggal Cetak: {{ $tanggal_cetak }}</small>
    </div>

    <!-- Tabel Detail Pembayaran -->
    <table class="detail-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Petugas</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>SPP Bulan</th>
                <th>SPP Nominal</th>
                <th>Konsumsi Nominal</th>
                <th>Fullday Nominal</th>
                <th>Inklusi Nominal</th>
                <th>Nominal Bayar</th>
                <th>Tanggal Bayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pembayaran as $key => $val)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $val->petugas->name }}</td>
                    <td>{{ $val->siswa->nama }}</td>
                    <td>{{ $val->siswa->kelas->nama_kelas }}</td>
                    <td>{{ $val->bulan }} {{ $val->tahun }}</td>
                    <td>Rp {{ number_format($val->nominal_spp, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($val->nominal_konsumsi, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($val->nominal_fullday, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($val->nominal_inklusi, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($val->jumlah_bayar, 0, ',', '.') }}</td>
                    <td>{{ $val->tgl_bayar->format('d M, Y') }}</td>
                    <td>{{ $val->is_lunas ? 'Lunas' : 'Belum Lunas' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">Tidak ada data pembayaran</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tabel Ringkasan -->
    @if ($pembayaran->count() > 0)
        <table class="summary-table">
            <thead>
                <tr>
                    <th colspan="2">RINGKASAN LAPORAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Total Transaksi</th>
                    <td class="text-right">{{ $pembayaran->count() }} transaksi</td>
                </tr>
                <tr>
                    <th>Total Pembayaran SPP</th>
                    <td class="text-right">Rp {{ number_format($total_spp, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Total Pembayaran Konsumsi</th>
                    <td class="text-right">Rp {{ number_format($total_konsumsi, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Total Pembayaran Fullday</th>
                    <td class="text-right">Rp {{ number_format($total_fullday, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Total Pembayaran Inklusi</th>
                    <td class="text-right">Rp {{ number_format($total_inklusi, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th><strong>TOTAL KESELURUHAN</strong></th>
                    <td class="text-right"><strong>Rp {{ number_format($total_pembayaran, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- Tambahkan ini setelah tabel ringkasan utama -->
    @if ($jenis_laporan == 'per_kelas' && $pembayaran->count() > 0)
        <!-- Ringkasan Per Kelas -->
        <table class="summary-table mt-4">
            <thead>
                <tr>
                    <th colspan="4">RINGKASAN PER KELAS</th>
                </tr>
                <tr>
                    <th>Kelas</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Pembayaran</th>
                    <th>Rata-rata per Siswa</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedByKelas = $pembayaran->groupBy('siswa.kelas.nama_kelas');
                @endphp
                @foreach ($groupedByKelas as $kelasName => $transaksiKelas)
                    @php
                        $totalKelas = $transaksiKelas->sum('jumlah_bayar');
                        $jumlahSiswa = $transaksiKelas->unique('id_siswa')->count();
                        $rataRata = $jumlahSiswa > 0 ? $totalKelas / $jumlahSiswa : 0;
                    @endphp
                    <tr>
                        <td>{{ $kelasName }}</td>
                        <td class="text-right">{{ $transaksiKelas->count() }}</td>
                        <td class="text-right">Rp {{ number_format($totalKelas, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($rataRata, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($jenis_laporan == 'per_bulan' && $pembayaran->count() > 0)
        <!-- Ringkasan Per Bulan -->
        <table class="summary-table mt-4">
            <thead>
                <tr>
                    <th colspan="3">RINGKASAN PER BULAN</th>
                </tr>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Dapatkan bulan-bulan yang dipilih dari request (jika ada)
                    $bulanDipilih = request('bulan') ?: [];
                    $tahunDipilih = request('tahun') ?: date('Y');

                    // Group by bulan dan tahun, tapi hanya yang sesuai dengan filter
                    $groupedByBulan = $pembayaran->groupBy(function ($item) {
                        return $item->bulan . '-' . $item->tahun;
                    });

                    // Jika ada bulan yang dipilih, filter hanya bulan-bulan tersebut
                    if (!empty($bulanDipilih)) {
                        $groupedByBulan = $groupedByBulan->filter(function ($transaksi, $key) use (
                            $bulanDipilih,
                            $tahunDipilih,
                        ) {
                            [$bulan, $tahun] = explode('-', $key);
                            return in_array((int) $bulan, $bulanDipilih) && $tahun == $tahunDipilih;
                        });
                    }
                @endphp

                @if ($groupedByBulan->count() > 0)
                    @foreach ($groupedByBulan as $groupKey => $transaksiBulan)
                        @php
                            [$bulan, $tahun] = explode('-', $groupKey);
                        @endphp
                        <tr>
                            <td>{{ $getNamaBulan($bulan) }} {{ $tahun }}</td>
                            <td class="text-right">{{ $transaksiBulan->count() }}</td>
                            <td class="text-right">Rp
                                {{ number_format($transaksiBulan->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data untuk bulan yang dipilih</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    @if ($jenis_laporan == 'per_semester' && $pembayaran->count() > 0)
        <!-- Ringkasan Per Semester -->
        <table class="summary-table mt-4">
            <thead>
                <tr>
                    <th colspan="3">RINGKASAN PER BULAN DALAM SEMESTER</th>
                </tr>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Group by bulan untuk melihat distribusi per bulan dalam semester
                    $groupedByBulan = $pembayaran->groupBy(function ($item) {
                        return $item->bulan . '-' . $item->tahun;
                    });

                    // Urutkan berdasarkan bulan
                    $groupedByBulan = $groupedByBulan->sortBy(function ($item, $key) {
                        return $key;
                    });
                @endphp

                @foreach ($groupedByBulan as $groupKey => $transaksiBulan)
                    @php
                        [$bulan, $tahun] = explode('-', $groupKey);
                    @endphp
                    <tr>
                        <td>{{ $getNamaBulan($bulan) }} {{ $tahun }}</td>
                        <td class="text-right">{{ $transaksiBulan->count() }}</td>
                        <td class="text-right">Rp
                            {{ number_format($transaksiBulan->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <!-- Total Semester -->
                <tr style="background-color: #f8f9fa;">
                    <td><strong>Total Semester</strong></td>
                    <td class="text-right"><strong>{{ $pembayaran->count() }}</strong></td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($pembayaran->sum('jumlah_bayar'), 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Statistik Semester -->
        <table class="summary-table mt-3">
            <thead>
                <tr>
                    <th colspan="2">STATISTIK SEMESTER</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $jumlahSiswa = $pembayaran->unique('siswa.id')->count();
                    $rataTransaksiPerSiswa = $jumlahSiswa > 0 ? $pembayaran->count() / $jumlahSiswa : 0;
                    $rataPembayaranPerSiswa = $jumlahSiswa > 0 ? $pembayaran->sum('jumlah_bayar') / $jumlahSiswa : 0;
                @endphp
                <tr>
                    <th>Jumlah Siswa yang Membayar</th>
                    <td class="text-right">{{ $jumlahSiswa }} siswa</td>
                </tr>
                <tr>
                    <th>Rata-rata Transaksi per Siswa</th>
                    <td class="text-right">{{ number_format($rataTransaksiPerSiswa, 1) }} transaksi</td>
                </tr>
                <tr>
                    <th>Rata-rata Pembayaran per Siswa</th>
                    <td class="text-right">Rp {{ number_format($rataPembayaranPerSiswa, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    @if ($jenis_laporan == 'per_tahun' && $pembayaran->count() > 0)
        <!-- Ringkasan Per Bulan dalam Tahun -->
        <table class="summary-table mt-4">
            <thead>
                <tr>
                    <th colspan="4">RINGKASAN PER BULAN TAHUN
                        {{ request('tahun_laporan') ?? (request('tahun') ?? date('Y')) }}</th>
                </tr>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Pembayaran</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Group by bulan dengan cara yang lebih aman
                    $groupedByBulan = $pembayaran->groupBy(function ($item) {
                        return date('n', strtotime($item->tgl_bayar));
                    });

                    $totalTahun = $pembayaran->sum('jumlah_bayar');

                    // Buat array untuk semua bulan
                    $allMonths = [];
                    for ($i = 1; $i <= 12; $i++) {
                        $allMonths[$i] = [
                            'count' => 0,
                            'total' => 0,
                            'percentage' => 0,
                        ];
                    }

                    // Isi data dari grouped result
                    foreach ($groupedByBulan as $bulan => $transaksi) {
                        $bulan = (int) $bulan; // Pastikan integer
                        if (isset($allMonths[$bulan])) {
                            $allMonths[$bulan]['count'] = $transaksi->count();
                            $allMonths[$bulan]['total'] = $transaksi->sum('jumlah_bayar');
                            $allMonths[$bulan]['percentage'] =
                                $totalTahun > 0 ? ($transaksi->sum('jumlah_bayar') / $totalTahun) * 100 : 0;
                        }
                    }
                @endphp

                @foreach ($allMonths as $bulan => $data)
                    <tr>
                        <td>{{ $getNamaBulan($bulan) }}</td>
                        <td class="text-right">{{ $data['count'] }}</td>
                        <td class="text-right">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($data['percentage'], 1) }}%</td>
                    </tr>
                @endforeach

                <!-- Total Tahun -->
                <tr style="background-color: #e3f2fd;">
                    <td><strong>TOTAL TAHUN</strong></td>
                    <td class="text-right"><strong>{{ $pembayaran->count() }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalTahun, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>100%</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Ringkasan Per Semester -->
        <table class="summary-table mt-4">
            <thead>
                <tr>
                    <th colspan="4">RINGKASAN PER SEMESTER</th>
                </tr>
                <tr>
                    <th>Semester</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Pembayaran</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Hitung semester 1 (Jan-Jun) dan semester 2 (Jul-Des)
                    $semester1 = $pembayaran->filter(function ($item) {
                        $bulan = (int) date('n', strtotime($item->tgl_bayar));
                        return $bulan >= 1 && $bulan <= 6;
                    });

                    $semester2 = $pembayaran->filter(function ($item) {
                        $bulan = (int) date('n', strtotime($item->tgl_bayar));
                        return $bulan >= 7 && $bulan <= 12;
                    });

                    $totalSemester1 = $semester1->sum('jumlah_bayar');
                    $totalSemester2 = $semester2->sum('jumlah_bayar');
                @endphp

                <tr>
                    <td>Semester 1 (Jan-Jun)</td>
                    <td class="text-right">{{ $semester1->count() }}</td>
                    <td class="text-right">Rp {{ number_format($totalSemester1, 0, ',', '.') }}</td>
                    <td class="text-right">
                        {{ $totalTahun > 0 ? number_format(($totalSemester1 / $totalTahun) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Semester 2 (Jul-Des)</td>
                    <td class="text-right">{{ $semester2->count() }}</td>
                    <td class="text-right">Rp {{ number_format($totalSemester2, 0, ',', '.') }}</td>
                    <td class="text-right">
                        {{ $totalTahun > 0 ? number_format(($totalSemester2 / $totalTahun) * 100, 1) : 0 }}%</td>
                </tr>
            </tbody>
        </table>

        <!-- Statistik Tahunan -->
        <table class="summary-table mt-3">
            <thead>
                <tr>
                    <th colspan="2">STATISTIK TAHUNAN
                        {{ request('tahun_laporan') ?? (request('tahun') ?? date('Y')) }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $jumlahSiswa = $pembayaran->unique('id_siswa')->count();
                    $jumlahKelas = $pembayaran->unique('siswa.id_kelas')->count();
                    $rataTransaksiPerSiswa = $jumlahSiswa > 0 ? $pembayaran->count() / $jumlahSiswa : 0;
                    $rataPembayaranPerSiswa = $jumlahSiswa > 0 ? $totalTahun / $jumlahSiswa : 0;

                    // Bulan dengan transaksi tertinggi
                    $bulanTertinggi = null;
                    $bulanTerendah = null;

                    $monthsWithData = collect($allMonths)->where('total', '>', 0);
                    if ($monthsWithData->count() > 0) {
                        $bulanTertinggi = $monthsWithData->sortByDesc('total')->first();
                        $bulanTerendah = $monthsWithData->sortBy('total')->first();
                    }
                @endphp
                <tr>
                    <th>Total Siswa yang Membayar</th>
                    <td class="text-right">{{ $jumlahSiswa }} siswa</td>
                </tr>
                <tr>
                    <th>Total Kelas yang Aktif</th>
                    <td class="text-right">{{ $jumlahKelas }} kelas</td>
                </tr>
                <tr>
                    <th>Rata-rata Transaksi per Siswa</th>
                    <td class="text-right">{{ number_format($rataTransaksiPerSiswa, 1) }} transaksi</td>
                </tr>
                <tr>
                    <th>Rata-rata Pembayaran per Siswa</th>
                    <td class="text-right">Rp {{ number_format($rataPembayaranPerSiswa, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Bulan dengan Pembayaran Tertinggi</th>
                    <td class="text-right">
                        @if ($bulanTertinggi)
                            @php
                                $bulanKey = array_keys($allMonths, $bulanTertinggi)[0] ?? null;
                            @endphp
                            @if ($bulanKey)
                                {{ $getNamaBulan($bulanKey) }} (Rp
                                {{ number_format($bulanTertinggi['total'], 0, ',', '.') }})
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Bulan dengan Pembayaran Terendah</th>
                    <td class="text-right">
                        @if ($bulanTerendah)
                            @php
                                $bulanKey = array_keys($allMonths, $bulanTerendah)[0] ?? null;
                            @endphp
                            @if ($bulanKey)
                                {{ $getNamaBulan($bulanKey) }} (Rp
                                {{ number_format($bulanTerendah['total'], 0, ',', '.') }})
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    @if ($jenis_laporan == 'semua' && $pembayaran->count() > 0)
        <!-- Ringkasan Statistik Komprehensif -->
        <div class="page-break"></div>

        <!-- Statistik Per Tahun -->
        <table class="summary-table mt-4">
            <thead>
                <tr>
                    <th colspan="4">STATISTIK PER TAHUN ({{ $tahun_awal }} - {{ $tahun_akhir }})</th>
                </tr>
                <tr>
                    <th>Tahun</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Pembayaran</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statistik_tahunan as $tahun => $data)
                    <tr>
                        <td>{{ $tahun }}</td>
                        <td class="text-right">{{ number_format($data['transaksi']) }}</td>
                        <td class="text-right">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($data['percentage'], 1) }}%</td>
                    </tr>
                @endforeach

                <!-- Total Keseluruhan -->
                <tr style="background-color: #e3f2fd;">
                    <td><strong>TOTAL KESELURUHAN</strong></td>
                    <td class="text-right"><strong>{{ number_format($pembayaran->count()) }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($total_pembayaran, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>100%</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Statistik Per Kelas -->
        <table class="summary-table mt-4">
            <thead>
                <tr>
                    <th colspan="4">STATISTIK PER KELAS</th>
                </tr>
                <tr>
                    <th>Kelas</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Pembayaran</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statistik_kelas as $kelas => $data)
                    <tr>
                        <td>{{ $kelas }}</td>
                        <td class="text-right">{{ number_format($data['transaksi']) }}</td>
                        <td class="text-right">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($data['percentage'], 1) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Statistik Per Bulan (Rata-rata) -->
        <table class="summary-table mt-4">
            <thead>
                <tr>
                    <th colspan="4">STATISTIK RATA-RATA PER BULAN (Semua Tahun)</th>
                </tr>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Pembayaran</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statistik_bulanan as $bulan => $data)
                    <tr>
                        <td>{{ $getNamaBulan($bulan) }}</td>
                        <td class="text-right">{{ number_format($data['count']) }}</td>
                        <td class="text-right">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($data['percentage'], 1) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Analisis Trend -->
        <table class="summary-table mt-4">
            <thead>
                <tr>
                    <th colspan="2">ANALISIS TREND DAN PERFORMANSI</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Rentang Data</th>
                    <td class="text-right">{{ $tahun_awal }} - {{ $tahun_akhir }}
                        ({{ $tahun_akhir - $tahun_awal + 1 }} tahun)</td>
                </tr>
                <tr>
                    <th>Total Siswa Unik yang Pernah Membayar</th>
                    <td class="text-right">{{ number_format($jumlah_siswa_unik) }} siswa</td>
                </tr>
                <tr>
                    <th>Total Kelas Unik yang Aktif</th>
                    <td class="text-right">{{ number_format($jumlah_kelas_unik) }} kelas</td>
                </tr>
                <tr>
                    <th>Rata-rata Transaksi per Siswa</th>
                    <td class="text-right">{{ number_format($rata_transaksi_per_siswa, 1) }} transaksi</td>
                </tr>
                <tr>
                    <th>Rata-rata Pembayaran per Siswa</th>
                    <td class="text-right">Rp {{ number_format($rata_pembayaran_per_siswa, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Bulan dengan Performa Terbaik (Rata-rata)</th>
                    <td class="text-right">
                        @if ($bulan_tertinggi)
                            @php
                                $bulanKey = array_keys($statistik_bulanan, $bulan_tertinggi)[0] ?? null;
                            @endphp
                            @if ($bulanKey)
                                {{ $getNamaBulan($bulanKey) }} (Rp
                                {{ number_format($bulan_tertinggi['total'], 0, ',', '.') }})
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Bulan dengan Performa Terendah (Rata-rata)</th>
                    <td class="text-right">
                        @if ($bulan_terendah)
                            @php
                                $bulanKey = array_keys($statistik_bulanan, $bulan_terendah)[0] ?? null;
                            @endphp
                            @if ($bulanKey)
                                {{ $getNamaBulan($bulanKey) }} (Rp
                                {{ number_format($bulan_terendah['total'], 0, ',', '.') }})
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tahun dengan Performa Terbaik</th>
                    <td class="text-right">
                        @if ($tahun_tertinggi)
                            {{ $tahun_tertinggi->tahun }} (Rp
                            {{ number_format($tahun_tertinggi->total_pembayaran, 0, ',', '.') }})
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tahun dengan Performa Terendah</th>
                    <td class="text-right">
                        @if ($tahun_terendah)
                            {{ $tahun_terendah->tahun }} (Rp
                            {{ number_format($tahun_terendah->total_pembayaran, 0, ',', '.') }})
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Trend Pertumbuhan</th>
                    <td class="text-right">
                        @if (count($statistik_tahunan) >= 2)
                            @php
                                $tahunArray = array_keys($statistik_tahunan);
                                $tahunTerakhir = end($tahunArray);
                                $tahunPertama = reset($tahunArray);
                                $pertumbuhan =
                                    (($statistik_tahunan[$tahunTerakhir]['total'] -
                                        $statistik_tahunan[$tahunPertama]['total']) /
                                        $statistik_tahunan[$tahunPertama]['total']) *
                                    100;
                            @endphp
                            {{ number_format($pertumbuhan, 1) }}% ({{ $tahunPertama }} vs {{ $tahunTerakhir }})
                        @else
                            Data tidak cukup untuk analisis trend
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Distribusi Pembayaran -->
        <table class="summary-table mt-4">
            <thead>
                <tr>
                    <th colspan="3">DISTRIBUSI JENIS PEMBAYARAN</th>
                </tr>
                <tr>
                    <th>Jenis Pembayaran</th>
                    <th>Total Nominal</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>SPP</td>
                    <td class="text-right">Rp {{ number_format($total_spp, 0, ',', '.') }}</td>
                    <td class="text-right">
                        {{ $total_pembayaran > 0 ? number_format(($total_spp / $total_pembayaran) * 100, 1) : 0 }}%
                    </td>
                </tr>
                <tr>
                    <td>Konsumsi</td>
                    <td class="text-right">Rp {{ number_format($total_konsumsi, 0, ',', '.') }}</td>
                    <td class="text-right">
                        {{ $total_pembayaran > 0 ? number_format(($total_konsumsi / $total_pembayaran) * 100, 1) : 0 }}%
                    </td>
                </tr>
                <tr>
                    <td>Fullday</td>
                    <td class="text-right">Rp {{ number_format($total_fullday, 0, ',', '.') }}</td>
                    <td class="text-right">
                        {{ $total_pembayaran > 0 ? number_format(($total_fullday / $total_pembayaran) * 100, 1) : 0 }}%
                    </td>
                </tr>
                <tr>
                    <td>Inklusi</td>
                    <td class="text-right">Rp {{ number_format($total_inklusi, 0, ',', '.') }}</td>
                    <td class="text-right">
                        {{ $total_pembayaran > 0 ? number_format(($total_inklusi / $total_pembayaran) * 100, 1) : 0 }}%
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- footer -->
    <div class="footer">
        <div style="float: right; text-align: center;">
            Ngawi, {{ date('d F Y') }}<br>
            Pembuat Laporan,<br><br><br><br>
            <strong>{{ auth()->user()->name }}</strong>
        </div>
        <div style="clear: both;"></div>
    </div>
    <!-- /footer -->
</body>

</html>
