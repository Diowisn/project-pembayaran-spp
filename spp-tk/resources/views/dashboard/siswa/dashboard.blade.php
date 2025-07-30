@extends('layouts.dashboard-siswa')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard Siswa</li>
@endsection

@section('content')
    <div class="row">
        <!-- Info Siswa -->
        {{-- <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Informasi Siswa</div>
                <div class="row">
                    <div class="col-md-3">
                        <p><strong>Nama:</strong> {{ $siswa->nama }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>NISN:</strong> {{ $siswa->nisn }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas }}</p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Tahun Ajaran:</strong> {{ $siswa->spp->tahun ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

        <!-- Kartu Rekening Virtual dan Tabungan -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Informasi Keuangan</div>
                    <div class="row">
                        <!-- Virtual Account -->
                        <div class="col-md-4">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">Virtual Account</h5>
                                            <p class="card-text mb-1"><strong>12345677890</strong></p>
                                            <p class="card-text">a.n. <strong>Anjar Novendra</strong></p>
                                        </div>
                                        <i class="mdi mdi-credit-card-multiple display-4"></i>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-white-50">Gunakan nomor ini untuk pembayaran via transfer
                                            bank</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Saldo Tagihan -->
                        <div class="col-md-4">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Saldo Tagihan</h5>
                                    @php
                                        $nominal_spp = $siswa->spp->nominal_spp ?? 0;
                                        $nominal_konsumsi = $siswa->spp->nominal_konsumsi ?? 0;
                                        $nominal_fullday = $siswa->spp->nominal_fullday ?? 0;
                                        $total_tagihan = $nominal_spp + $nominal_konsumsi + $nominal_fullday;
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="card-text mb-1">Total Tagihan Bulan Ini:</p>
                                            <h4 class="mb-0">Rp {{ number_format($total_tagihan, 0, ',', '.') }}</h4>
                                        </div>
                                        <div class=" mt-3">
                                            <p class="mb-1">SPP: Rp {{ number_format($nominal_spp, 0, ',', '.') }}</p>
                                            <p class="mb-1">Konsumsi: Rp
                                                {{ number_format($nominal_konsumsi, 0, ',', '.') }}</p>
                                            <p class="mb-1">Fullday: Rp
                                                {{ number_format($nominal_fullday, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <div class="">

                                    </div>
                                    {{-- <div class="mt-2">
                                        <a href="#" class="btn btn-light btn-sm">Detail Tagihan</a>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                        <!-- Infaq Gedung -->
                        <div class="col-md-4">
                            <div class="card bg-purple text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Infaq Gedung</h5>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="card-text mb-1">Total Tagihan:</p>
                                            <h4 class="mb-0">Rp {{ number_format($totalTagihanInfaq, 0, ',', '.') }}</h4>
                                        </div>
                                        <div>
                                            <p class="card-text mb-1">Sisa Pembayaran:</p>
                                            <h4 class="mb-0">Rp {{ number_format($sisaPembayaranInfaq, 0, ',', '.') }}
                                            </h4>
                                        </div>
                                    </div>
                                    {{-- <div class="progress mt-2">
                                    <div class="progress-bar bg-white" role="progressbar" style="width: {{ $persentaseInfaq }}%" 
                                        aria-valuenow="{{ $persentaseInfaq }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ round($persentaseInfaq, 2) }}%
                                    </div>
                                </div> --}}
                                    <div class="mt-2">
                                        <a href="{{ url('dashboard/siswa/infaq') }}" class="btn btn-light btn-sm">Detail
                                            Infaq</a>
                                        <a href="#" class="btn btn-outline-light btn-sm ml-2">Cetak Bukti</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabungan Siswa -->
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Tabungan Siswa</h5>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="card-text mb-1">Saldo Saat Ini:</p>
                                            <h4 class="mb-0">Rp {{ number_format($saldoTabungan, 0, ',', '.') }}</h4>
                                        </div>
                                        <div class="text-right">
                                            <i class="mdi mdi-credit-card-multiple display-4"></i>
                                            {{-- <p class="card-text mb-1">Total Tagihan:</p>
                                        <h4 class="mb-0">Rp {{ number_format($total_tagihan ?? 0, 0, ',', '.') }}</h4> --}}
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="#" class="btn btn-light btn-sm">Detail Tabungan</a>
                                        <a href="#" class="btn btn-outline-light btn-sm ml-2">Cetak Laporan</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Pembayaran dan Tabungan -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Status Pembayaran {{ $currentMonthName }} {{ $currentYear }}</div>
                    @if ($statusPembayaran)
                        <div class="alert alert-success">
                            <p>Anda sudah melakukan pembayaran SPP bulan ini.</p>
                            <p>Tanggal Bayar: {{ $statusPembayaran->created_at->format('d/m/Y') }}</p>
                            <p>Jumlah Bayar: Rp {{ number_format($statusPembayaran->jumlah_bayar, 0, ',', '.') }}</p>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <p>Anda belum melakukan pembayaran SPP bulan ini.</p>
                            <p>Total yang harus dibayarkan: Rp
                                {{ number_format($siswa->spp->nominal_spp ?? 0, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informasi Tabungan -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Ringkasan Tabungan</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-gradient-info">
                                <span class="info-box-icon"><i class="mdi mdi-wallet"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Setoran</span>
                                    <span class="info-box-number">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-gradient-warning">
                                <span class="info-box-icon"><i class="mdi mdi-cash-minus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Penarikan</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($totalPenarikan, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="mdi mdi-cash-multiple"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Saldo Akhir</span>
                                    <span class="info-box-number">Rp
                                        {{ number_format($saldoTabungan, 0, ',', '.') }}</span>
                                    <div class="progress">
                                        @php
                                            $targetTabungan = 1000000; // Contoh target tabungan
                                            $persentaseTabungan =
                                                $targetTabungan > 0 ? ($saldoTabungan / $targetTabungan) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar" style="width: {{ $persentaseTabungan }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ round($persentaseTabungan, 2) }}% dari target Rp
                                        {{ number_format($targetTabungan, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Pembayaran Terakhir -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Riwayat Pembayaran Terakhir</div>
                    @if ($pembayaranTerakhir->count() > 0)
                        @foreach ($pembayaranTerakhir as $pembayaran)
                            <div class="d-flex flex-row comment-row">
                                <i class="mdi mdi-cash-multiple display-3"></i>
                                <div class="comment-text active w-100">
                                    <hr>
                                    <span class="badge badge-primary badge-rounded float-right">
                                        {{ $pembayaran->created_at->diffforHumans() }}
                                    </span>
                                    <h6 class="font-medium">Pembayaran SPP</h6>
                                    <span class="m-b-15 d-block">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">Bulan: {{ ucfirst($pembayaran->bulan) }}</li>
                                            <li class="list-group-item">Tahun: {{ $pembayaran->tahun }}</li>
                                            <li class="list-group-item">Jumlah Bayar: Rp
                                                {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</li>
                                        </ul>
                                    </span>
                                    <div class="comment-footer">
                                        <span class="text-muted float-right">
                                            {{ $pembayaran->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="action-icons active" style="background-color: #4CAF50;">
                                            <a href="{{ route('siswa.pembayaran.cetak', $pembayaran->id) }}"
                                                class="mr-2" title="Cetak Bukti" style="color: white">
                                                <i class="mdi mdi-printer"></i> Cetak Bukti
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ url('dashboard/siswa/histori') }}" class="btn btn-success mt-3">Lihat Semua</a>
                    @else
                        <div class="text-center">Belum ada riwayat pembayaran</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Riwayat Infaq Terakhir -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Riwayat Infaq Terakhir</div>
                    @if ($infaqTerakhir->count() > 0)
                        @foreach ($infaqTerakhir as $infaq)
                            <div class="d-flex flex-row comment-row">
                                <i class="mdi mdi-home display-3"></i>
                                <div class="comment-text active w-100">
                                    <hr>
                                    <span class="badge badge-primary badge-rounded float-right">
                                        {{ $infaq->created_at->diffforHumans() }}
                                    </span>
                                    <h6 class="font-medium">Pembayaran Infaq</h6>
                                    <span class="m-b-15 d-block">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">Paket: <b
                                                    class="text-uppercase">{{ $infaq->infaqGedung->paket ?? '-' }}</b>
                                            </li>
                                            <li class="list-group-item">Angsuran Ke-{{ $infaq->angsuran_ke }}</li>
                                            <li class="list-group-item">Jumlah Bayar: Rp
                                                {{ number_format($infaq->jumlah_bayar, 0, ',', '.') }}</li>
                                            @php
                                                $totalDibayar = $infaq->siswa->angsuranInfaq->sum('jumlah_bayar');
                                                $sisaPembayaran = $infaq->infaqGedung->nominal - $totalDibayar;
                                            @endphp
                                            <li class="list-group-item">Total Dibayar: Rp
                                                {{ number_format($totalDibayar, 0, ',', '.') }}</li>
                                            <li class="list-group-item">Sisa Pembayaran: Rp
                                                {{ number_format($sisaPembayaran, 0, ',', '.') }}</li>
                                        </ul>
                                    </span>
                                    <div class="comment-footer">
                                        <span class="text-muted float-right">
                                            {{ $infaq->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="action-icons active" style="background-color: #4CAF50;">
                                            <a href="{{ route('siswa.infaq.cetak', $infaq->id) }}" class="mr-2"
                                                title="Cetak Bukti" style="color: white">
                                                <i class="mdi mdi-printer"></i> Cetak Bukti
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ url('dashboard/siswa/infaq') }}" class="btn btn-success mt-3">Lihat Semua</a>
                    @else
                        <div class="text-center">Belum ada riwayat infaq</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Riwayat Tabungan Terakhir -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Riwayat Tabungan Terakhir</div>
                    @if ($tabungan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Setoran</th>
                                        <th>Penarikan</th>
                                        <th>Saldo</th>
                                        <th>Petugas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tabungan as $transaksi)
                                        <tr>
                                            <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $transaksi->keterangan }}</td>
                                            <td class="text-success">
                                                @if ($transaksi->debit > 0)
                                                    Rp {{ number_format($transaksi->debit, 0, ',', '.') }}
                                                @endif
                                            </td>
                                            <td class="text-danger">
                                                @if ($transaksi->kredit > 0)
                                                    Rp {{ number_format($transaksi->kredit, 0, ',', '.') }}
                                                @endif
                                            </td>
                                            <td>Rp {{ number_format($transaksi->saldo, 0, ',', '.') }}</td>
                                            <td>{{ $transaksi->petugas->name ?? 'System' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="#" class="btn btn-info mt-3">Lihat Semua Riwayat</a>
                    @else
                        <div class="text-center">Belum ada riwayat tabungan</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
