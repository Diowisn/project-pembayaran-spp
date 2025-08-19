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
            <div style="background: #fff; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.05); margin-bottom: 30px;">
                <div style="padding: 25px;">
                    <h4
                        style="color: #2c3e50; font-size: 24px; margin-bottom: 25px; font-weight: 600; border-left: 5px solid #4e73df; padding-left: 15px;">
                        Informasi Keuangan
                    </h4>

                    <!-- Virtual Account -->
                    <div
                        style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border-radius: 12px; padding: 25px; color: white; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(78,115,223,0.2);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h5 style="font-size: 18px; margin-bottom: 15px; color: rgba(255,255,255,0.9);">Pembayaran
                                    Melalui</h5>
                                <p style="font-size: 24px; font-weight: 600; margin-bottom: 5px; letter-spacing: 2px;">
                                    Rekening Bank Jatim 1362004752</p>
                                <p style="margin-bottom: 15px;">a.n. <strong>TK ASSAKIINAH</strong></p>
                                <div style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px;">
                                    <small style="color: rgba(255,255,255,0.8);">
                                        <i class="mdi mdi-information-outline" style="margin-right: 5px;"></i>
                                        Gunakan nomor ini untuk pembayaran via transfer bank
                                    </small>
                                </div>
                            </div>
                            {{-- <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 50%;">
                                <i class="mdi mdi-credit-card-multiple" style="font-size: 40px; color: rgba(255,255,255,0.9);"></i>
                            </div> --}}
                        </div>
                    </div>

                    <div class="row">
                        <!-- Saldo Tagihan -->
                        <div class="col-md-4">
                            @php
                                $nominal_spp = $siswa->spp->nominal_spp ?? 0;
                                $nominal_konsumsi = $siswa->spp->nominal_konsumsi ?? 0;
                                $nominal_fullday = $siswa->spp->nominal_fullday ?? 0;
                                $nominal_inklusi = $siswa->spp->nominal_inklusi ?? 0;
                                $total_tagihan = $nominal_spp + $nominal_konsumsi + $nominal_fullday + $nominal_inklusi;
                            @endphp
                            <div
                                style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); border-radius: 12px; padding: 20px; color: white; height: 100%; box-shadow: 0 5px 15px rgba(46,204,113,0.2);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                                    <h5 style="font-size: 18px; margin: 0;">Saldo Tagihan</h5>
                                    <i class="mdi mdi-currency-usd" style="font-size: 24px;"></i>
                                </div>
                                <div
                                    style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                    <p style="margin-bottom: 5px; font-size: 12px;">Total Tagihan Bulan Ini:</p>
                                    <h4 style="margin: 0; font-size: 24px; font-weight: 600;">Rp
                                        {{ number_format($total_tagihan, 0, ',', '.') }}</h4>
                                </div>
                                <div style="font-size: 14px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span>SPP</span>
                                        <span>Rp {{ number_format($nominal_spp, 0, ',', '.') }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <span>Konsumsi</span>
                                        <span>Rp {{ number_format($nominal_konsumsi, 0, ',', '.') }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <span>Fullday</span>
                                        <span>Rp {{ number_format($nominal_fullday, 0, ',', '.') }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                                        <span>Inklusi</span>
                                        <span>Rp {{ number_format($nominal_inklusi, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Infaq Gedung -->
                        <div class="col-md-4">
                            <div
                                style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); border-radius: 12px; padding: 20px; color: white; height: 100%; box-shadow: 0 5px 15px rgba(155,89,182,0.2);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                                    <h5 style="font-size: 18px; margin: 0;">Infaq Gedung</h5>
                                    <i class="mdi mdi-mosque" style="font-size: 24px;"></i>
                                </div>
                                <div style="margin-bottom: 20px;">
                                    <div
                                        style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                        <small>Total Tagihan</small>
                                        <h4 style="margin: 5px 0 0 0;">Rp
                                            {{ number_format($totalTagihanInfaq, 0, ',', '.') }}</h4>
                                    </div>
                                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px;">
                                        <small>Sisa Pembayaran</small>
                                        <h4 style="margin: 5px 0 0 0;">Rp
                                            {{ number_format($sisaPembayaranInfaq, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                                {{-- <div style="display: flex; gap: 10px;">
                                    <a href="{{ url('dashboard/siswa/infaq') }}" 
                                       style="background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 6px; color: white; text-decoration: none; flex: 1; text-align: center; transition: all 0.3s;">
                                        <i class="mdi mdi-file-document-outline"></i> Detail
                                    </a>
                                    <a href="#" 
                                       style="background: rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 6px; color: white; text-decoration: none; flex: 1; text-align: center; transition: all 0.3s;">
                                        <i class="mdi mdi-printer"></i> Cetak
                                    </a>
                                </div> --}}
                            </div>
                        </div>

                        <!-- Tabungan Siswa -->
                        <div class="col-md-4">
                            <div
                                style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); border-radius: 12px; padding: 20px; color: white; height: 100%; box-shadow: 0 5px 15px rgba(52,152,219,0.2);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                                    <h5 style="font-size: 18px; margin: 0;">Tabungan Siswa</h5>
                                    <i class="mdi mdi-wallet" style="font-size: 24px;"></i>
                                </div>
                                <div
                                    style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                                    <small>Saldo Saat Ini</small>
                                    <h4 style="margin: 5px 0 0 0; font-size: 24px;">Rp
                                        {{ number_format($saldoTabungan, 0, ',', '.') }}</h4>
                                </div>
                                {{-- <div style="display: flex; gap: 10px;">
                                    <a href="#" 
                                       style="background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 6px; color: white; text-decoration: none; flex: 1; text-align: center; transition: all 0.3s;">
                                        <i class="mdi mdi-file-document-outline"></i> Detail
                                    </a>
                                    <a href="#" 
                                       style="background: rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 6px; color: white; text-decoration: none; flex: 1; text-align: center; transition: all 0.3s;">
                                        <i class="mdi mdi-printer"></i> Cetak
                                    </a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Pembayaran dan Tabungan -->
        <div class="col-md-6">
            <div style="background: #fff; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.05); margin-bottom: 30px;">
                <div
                    style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); padding: 20px; border-radius: 15px 15px 0 0; color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h5 style="margin: 0; font-size: 18px; font-weight: 600;">
                            <i class="fas fa-clipboard-check mr-2"></i>
                            Status Pembayaran {{ $currentMonthName }} {{ $currentYear }}
                        </h5>
                        <div
                            style="background: rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 20px; font-size: 14px;">
                            <i class="fas fa-calendar mr-2"></i>{{ $currentMonthName }} {{ $currentYear }}
                        </div>
                    </div>
                </div>

                @php
                    $nominal_spp = $siswa->spp->nominal_spp ?? 0;
                    $nominal_konsumsi = $siswa->spp->nominal_konsumsi ?? 0;
                    $nominal_fullday = $siswa->spp->nominal_fullday ?? 0;
                    $nominal_inklusi = $siswa->spp->nominal_inklusi ?? 0;
                    $total_tagihan = $nominal_spp + $nominal_konsumsi + $nominal_fullday + $nominal_inklusi;
                @endphp

                <div style="padding: 20px;">
                    @if ($statusPembayaran)
                        <div
                            style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); border-radius: 12px; padding: 20px; color: white; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <div
                                    style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <i class="fas fa-check" style="font-size: 20px;"></i>
                                </div>
                                <div>
                                    <h6 style="margin: 0; font-size: 16px;">Pembayaran Lunas</h6>
                                    <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">Terima kasih atas
                                        pembayaran tepat waktu</p>
                                </div>
                            </div>
                            <div
                                style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="color: rgba(255,255,255,0.9);">Tanggal Pembayaran</span>
                                    <span
                                        style="font-weight: 600;">{{ $statusPembayaran->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: rgba(255,255,255,0.9);">Jumlah Dibayar</span>
                                    <span style="font-weight: 600;">Rp
                                        {{ number_format($statusPembayaran->jumlah_bayar, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div
                            style="background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%); border-radius: 12px; padding: 20px; color: white; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <div
                                    style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i>
                                </div>
                                <div>
                                    <h6 style="margin: 0; font-size: 16px;">Belum Lunas</h6>
                                    <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">Mohon segera lakukan
                                        pembayaran</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Rincian Tagihan Card -->
                    <div style="background: #f8f9fc; border-radius: 12px; padding: 20px;">
                        <h6 style="margin: 0 0 15px 0; color: #4e73df; font-weight: 600;">
                            <i class="fas fa-receipt mr-2"></i>Rincian Tagihan
                        </h6>
                        <div style="display: grid; gap: 12px;">
                            <div
                                style="display: flex; justify-content: space-between; padding: 10px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <span style="color: #2c3e50;">SPP</span>
                                <span style="font-weight: 600;">Rp {{ number_format($nominal_spp, 0, ',', '.') }}</span>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; padding: 10px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <span style="color: #2c3e50;">Konsumsi</span>
                                <span style="font-weight: 600;">Rp
                                    {{ number_format($nominal_konsumsi, 0, ',', '.') }}</span>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; padding: 10px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <span style="color: #2c3e50;">Fullday</span>
                                <span style="font-weight: 600;">Rp
                                    {{ number_format($nominal_fullday, 0, ',', '.') }}</span>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; padding: 10px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <span style="color: #2c3e50;">Inklusi</span>
                                <span style="font-weight: 600;">Rp
                                    {{ number_format($nominal_inklusi, 0, ',', '.') }}</span>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; padding: 12px; background: #4e73df; color: white; border-radius: 8px; margin-top: 8px;">
                                <span style="font-weight: 600;">Total Tagihan</span>
                                <span style="font-weight: 600;">Rp {{ number_format($total_tagihan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Tabungan -->
        <div class="col-md-6">
            <div style="background: #fff; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.05); margin-bottom: 30px;">
                <div
                    style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 20px; border-radius: 15px 15px 0 0; color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h5 style="margin: 0; font-size: 18px; font-weight: 600;">
                            <i class="fas fa-piggy-bank mr-2"></i>
                            Ringkasan Tabungan
                        </h5>
                        <a href="{{ route('siswa.tabungan') }}"
                            style="background: rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 20px; font-size: 14px; color: white; text-decoration: none; transition: all 0.3s ease;"
                            onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-external-link-alt mr-1"></i> Detail
                        </a>
                    </div>
                </div>

                <div style="padding: 20px;">
                    <div class="row">
                        <div class="col-md-6">
                            <div
                                style="background: linear-gradient(135deg, #00b894 0%, #00a885 100%); border-radius: 12px; padding: 20px; color: white; height: 100%; box-shadow: 0 4px 15px rgba(0,184,148,0.2);">
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div
                                        style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                        <i class="fas fa-arrow-up" style="font-size: 18px;"></i>
                                    </div>
                                    <span style="font-size: 16px;">Total Setoran</span>
                                </div>
                                <h4 style="margin: 0; font-size: 20px; font-weight: 600;">
                                    Rp {{ number_format($totalSetoran, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div
                                style="background: linear-gradient(135deg, #e17055 0%, #d15745 100%); border-radius: 12px; padding: 20px; color: white; height: 100%; box-shadow: 0 4px 15px rgba(225,112,85,0.2);">
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div
                                        style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                        <i class="fas fa-arrow-down" style="font-size: 18px;"></i>
                                    </div>
                                    <span style="font-size: 16px;">Total Penarikan</span>
                                </div>
                                <h4 style="margin: 0; font-size: 20px; font-weight: 600;">
                                    Rp {{ number_format($totalPenarikan, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div
                        style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border-radius: 12px; padding: 20px; color: white; margin-top: 20px; box-shadow: 0 4px 15px rgba(78,115,223,0.2);">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <div>
                                <h6 style="margin: 0; font-size: 14px; opacity: 0.9;">Saldo Akhir</h6>
                                <h3 style="margin: 8px 0 0 0; font-size: 24px; font-weight: 600;">
                                    Rp {{ number_format($saldoTabungan, 0, ',', '.') }}
                                </h3>
                            </div>
                            <div
                                style="background: rgba(255,255,255,0.1); border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-wallet" style="font-size: 24px;"></i>
                            </div>
                        </div>

                        @php
                            $targetTabungan = 1000000; // Contoh target tabungan
                            $persentaseTabungan = $targetTabungan > 0 ? ($saldoTabungan / $targetTabungan) * 100 : 0;
                        @endphp

                        <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 15px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span style="font-size: 14px;">Progress Target Tabungan</span>
                                <span
                                    style="font-size: 14px; font-weight: 600;">{{ round($persentaseTabungan, 1) }}%</span>
                            </div>
                            <div
                                style="background: rgba(255,255,255,0.1); height: 8px; border-radius: 4px; overflow: hidden;">
                                <div
                                    style="width: {{ $persentaseTabungan }}%; height: 100%; background: rgba(255,255,255,0.8); border-radius: 4px; transition: width 1s ease-in-out;">
                                </div>
                            </div>
                            <div style="margin-top: 10px; font-size: 12px; opacity: 0.9;">
                                Target: Rp {{ number_format($targetTabungan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Pembayaran Terakhir -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-left-success">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-history mr-2"></i>Riwayat Pembayaran Terakhir
                        </h6>
                        <a href="{{ url('dashboard/siswa/histori') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-list mr-1"></i>Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($pembayaranTerakhir->count() > 0)
                        @foreach ($pembayaranTerakhir as $pembayaran)
                            <div class="border-left-success pl-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold text-success mb-0">
                                        <i class="fas fa-receipt mr-2"></i>Pembayaran SPP
                                    </h6>
                                    <span class="badge badge-success px-3 py-2">
                                        {{ $pembayaran->created_at->diffforHumans() }}
                                    </span>
                                </div>
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><i
                                                        class="fas fa-calendar-alt mr-2"></i>{{ ucfirst($pembayaran->bulan) }}
                                                    {{ $pembayaran->tahun }}</p>
                                                <h5 class="text-success mb-0">Rp
                                                    {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</h5>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <small
                                                    class="text-muted d-block mb-2">{{ $pembayaran->created_at->format('d M Y') }}</small>
                                                <a href="{{ route('siswa.pembayaran.cetak', $pembayaran->id) }}"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-print mr-1"></i>Cetak Bukti
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500 mb-0">Belum ada riwayat pembayaran</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Riwayat Infaq Terakhir -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-left-purple">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-purple">
                            <i class="fas fa-mosque mr-2"></i>Riwayat Infaq Terakhir
                        </h6>
                        <a href="{{ url('dashboard/siswa/infaq') }}" class="btn btn-sm btn-purple">
                            <i class="fas fa-list mr-1"></i>Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($infaqTerakhir->count() > 0)
                        @foreach ($infaqTerakhir as $infaq)
                            <div class="border-left-purple pl-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="font-weight-bold text-purple mb-0">
                                            <i class="fas fa-hand-holding-heart mr-2"></i>Paket
                                            <span class="badge badge-purple ml-1">
                                                {{ strtoupper($infaq->infaqGedung->paket ?? '-') }}
                                            </span>
                                        </h6>
                                        <small class="text-muted">Angsuran Ke-{{ $infaq->angsuran_ke }}</small>
                                    </div>
                                    <span class="badge badge-purple px-3 py-2">
                                        {{ $infaq->created_at->diffforHumans() }}
                                    </span>
                                </div>
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        @php
                                            $totalDibayar = $infaq->siswa->angsuranInfaq->sum('jumlah_bayar');
                                            $sisaPembayaran = $infaq->infaqGedung->nominal - $totalDibayar;
                                        @endphp
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1">Pembayaran Kali Ini</p>
                                                <h5 class="text-purple mb-0">Rp
                                                    {{ number_format($infaq->jumlah_bayar, 0, ',', '.') }}</h5>
                                            </div>
                                            <div class="col-md-6 border-left">
                                                <div class="text-right">
                                                    <p class="mb-1 small">Total Dibayar / Sisa</p>
                                                    <h6 class="mb-0">
                                                        <span class="text-success">Rp
                                                            {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                                                        <span class="text-muted mx-1">/</span>
                                                        <span class="text-danger">Rp
                                                            {{ number_format($sisaPembayaran, 0, ',', '.') }}</span>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ $infaq->created_at->format('d M Y') }}</small>
                                            <a href="{{ route('siswa.infaq.cetak', $infaq->id) }}"
                                                class="btn btn-sm btn-outline-purple">
                                                <i class="fas fa-print mr-1"></i>Cetak Bukti
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-hand-holding-heart fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500 mb-0">Belum ada riwayat infaq</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Riwayat Tabungan Terakhir -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-left-info">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-history mr-2"></i>Riwayat Tabungan Terakhir
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    @if ($tabungan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="fas fa-calendar-alt mr-2"></i>Tanggal</th>
                                        <th><i class="fas fa-info-circle mr-2"></i>Keterangan</th>
                                        <th class="text-center"><i class="fas fa-arrow-up mr-2"></i>Setoran</th>
                                        <th class="text-center"><i class="fas fa-arrow-down mr-2"></i>Penarikan</th>
                                        <th class="text-center"><i class="fas fa-wallet mr-2"></i>Saldo</th>
                                        <th class="text-center"><i class="fas fa-user mr-2"></i>Petugas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tabungan as $transaksi)
                                        <tr>
                                            <td class="text-center">
                                                <span
                                                    class="font-weight-bold">{{ $transaksi->created_at->format('d M Y') }}</span>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $transaksi->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="d-block">{{ $transaksi->keterangan }}</span>
                                                <small class="text-muted">ID:
                                                    #{{ str_pad($transaksi->id, 5, '0', STR_PAD_LEFT) }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if ($transaksi->debit > 0)
                                                    <span class="badge badge-success px-3 py-2">
                                                        <i class="fas fa-plus-circle mr-1"></i>
                                                        Rp {{ number_format($transaksi->debit, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($transaksi->kredit > 0)
                                                    <span class="badge badge-danger px-3 py-2">
                                                        <i class="fas fa-minus-circle mr-1"></i>
                                                        Rp {{ number_format($transaksi->kredit, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="font-weight-bold">Rp
                                                    {{ number_format($transaksi->saldo, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info px-3 py-2">
                                                    <i class="fas fa-user-circle mr-1"></i>
                                                    {{ $transaksi->petugas->name ?? 'System' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('siswa.tabungan') }}" class="btn btn-info btn-icon-split">
                                <span class="icon text-white-50">
                                    <i class="fas fa-list"></i>
                                </span>
                                <span class="text">Lihat Semua Riwayat</span>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-piggy-bank fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500 mb-0">Belum ada riwayat tabungan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
