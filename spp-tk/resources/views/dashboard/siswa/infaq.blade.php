@extends('layouts.dashboard-siswa')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori Infaq Gedung</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div style="background: #fff; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.05); margin-bottom: 30px;">
                <div
                    style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); padding: 25px; border-radius: 15px 15px 0 0; color: white;">
                    <h4 style="margin: 0; font-size: 20px; font-weight: 600;">
                        <i class="fas fa-mosque"></i>
                        Histori Pembayaran Infaq Gedung
                    </h4>

                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <a href="{{ route('siswa.infaq.rekap.cetak') }}"
                            style="background: rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 8px; color: white; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.3s ease;"
                            target="_blank" onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Cetak Rekap Infaq
                        </a>
                    </div>
                </div>
                <div class="history-container">

                    @if (count($infaqHistori) > 0)
                        @php
                            $totalDibayarSemua = $siswa->angsuranInfaq->sum('jumlah_bayar');
                            $totalTagihan = $siswa->infaqGedung->nominal ?? 0;
                            $sisaPembayaranSemua = max(0, $totalTagihan - $totalDibayarSemua);
                        @endphp

                        <!-- Tabel untuk tampilan desktop -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Tanggal</th>
                                        <th>Paket</th>
                                        <th>Total Infaq</th>
                                        <th>Angsuran Ke-</th>
                                        <th>Jumlah Bayar</th>
                                        <th>Total Dibayar</th>
                                        <th>Sisa Pembayaran</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($infaqHistori as $history)
                                        @php
                                            // Hitung total dibayar sampai transaksi ini
                                            $totalDibayarSampaiIni = $siswa->angsuranInfaq
                                                ->where('created_at', '<=', $history->created_at)
                                                ->sum('jumlah_bayar');
                                            $sisaPembayaranSampaiIni = max(0, $totalTagihan - $totalDibayarSampaiIni);
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $infaqHistori->firstItem() + $loop->index }}</td>
                                            <td>
                                                <div>{{ $history->created_at->format('d M Y') }}</div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-success text-uppercase">
                                                    {{ $history->infaqGedung->paket ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="amount-text">
                                                Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <span class="status-badge status-info">
                                                    {{ $history->angsuran_ke }}
                                                </span>
                                            </td>
                                            <td class="amount-text success-amount">
                                                Rp {{ number_format($history->jumlah_bayar, 0, ',', '.') }}
                                            </td>
                                            <td class="amount-text info-amount">
                                                Rp {{ number_format($totalDibayarSampaiIni, 0, ',', '.') }}
                                            </td>
                                            <td
                                                class="amount-text {{ $sisaPembayaranSampaiIni > 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($sisaPembayaranSampaiIni, 0, ',', '.') }}
                                                @if ($sisaPembayaranSampaiIni == 0 && $history->is_lunas)
                                                    <br><small class="text-success">LUNAS</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('siswa.infaq.cetak', $history->id) }}"
                                                    class="btn-print btn-success">
                                                    <i class="fas fa-print"></i>
                                                    Cetak
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Card untuk tampilan mobile -->
                        <div class="d-block d-md-none">
                            @foreach ($infaqHistori as $history)
                                @php
                                    $totalDibayarSampaiIni = $siswa->angsuranInfaq
                                        ->where('created_at', '<=', $history->created_at)
                                        ->sum('jumlah_bayar');
                                    $sisaPembayaranSampaiIni = max(0, $totalTagihan - $totalDibayarSampaiIni);
                                @endphp
                                <div
                                    style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">
                                    <div style="padding: 15px;">
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                            <span style="color: #555;">Kelas</span>
                                            <span style="font-weight: 600;">{{ $history->siswa->kelas->nama_kelas }}</span>
                                        </div>
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                            <span style="color: #555;">Paket Infaq</span>
                                            <span
                                                style="font-weight: 600; text-transform: uppercase;">{{ $history->infaqGedung->paket ?? '-' }}</span>
                                        </div>
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                            <span style="color: #555;">Total Infaq</span>
                                            <span style="font-weight: 600;">Rp.
                                                {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                                        </div>
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                            <span style="color: #555;">Angsuran Ke-</span>
                                            <span
                                                style="font-weight: 600; color: #4e73df;">{{ $history->angsuran_ke }}</span>
                                        </div>
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                            <span style="color: #555;">Jumlah Bayar</span>
                                            <span style="font-weight: 600; color: #28a745;">Rp.
                                                {{ number_format($history->jumlah_bayar, 0, ',', '.') }}</span>
                                        </div>
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                            <span style="color: #555;">Total Dibayar</span>
                                            <span style="font-weight: 600; color: #17a2b8;">Rp.
                                                {{ number_format($totalDibayarSampaiIni, 0, ',', '.') }}</span>
                                        </div>
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                                            <span style="color: #555;">Sisa Pembayaran</span>
                                            <span
                                                style="font-weight: 600; color: {{ $sisaPembayaranSampaiIni > 0 ? '#dc3545' : '#28a745' }};">Rp.
                                                {{ number_format($sisaPembayaranSampaiIni, 0, ',', '.') }}</span>
                                            @if ($sisaPembayaranSampaiIni == 0 && $history->is_lunas)
                                                <br><small class="text-success">LUNAS</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border-top: 1px solid #eee;">
                                        <span style="color: #6c757d;">{{ $history->created_at->format('M d, Y') }}</span>
                                        <a href="{{ route('siswa.infaq.cetak', $history->id) }}"
                                            style="background: #28a745; color: white; padding: 8px 16px; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center; transition: background 0.3s;">
                                            <i class="mdi mdi-printer" style="margin-right: 8px;"></i>
                                            Cetak
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($infaqHistori->lastPage() != 1)
                            <div class="pagination-container">
                                <div class="pagination-wrapper">
                                    <a href="{{ $infaqHistori->previousPageUrl() }}"
                                        class="pagination-item {{ $infaqHistori->onFirstPage() ? 'disabled' : '' }}">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>

                                    @for ($i = 1; $i <= $infaqHistori->lastPage(); $i++)
                                        <a href="{{ $infaqHistori->url($i) }}"
                                            class="pagination-item {{ $i == $infaqHistori->currentPage() ? 'active' : '' }}">
                                            {{ $i }}
                                        </a>
                                    @endfor

                                    <a href="{{ $infaqHistori->nextPageUrl() }}"
                                        class="pagination-item {{ !$infaqHistori->hasMorePages() ? 'disabled' : '' }}">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-mosque"></i>
                            <p>Belum ada histori pembayaran infaq gedung</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
