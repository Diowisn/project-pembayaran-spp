@extends('layouts.dashboard-siswa')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div style="background: #fff; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.05); margin-bottom: 30px;">
                <div
                    style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); padding: 25px; border-radius: 15px 15px 0 0; color: white;">
                    <h4 style="margin: 0; font-size: 20px; font-weight: 600;">
                        <i class="fas fa-history"></i>
                        Histori Pembayaran SPP
                    </h4>
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <a href="{{ route('siswa.spp.rekap.cetak') }}"
                            style="background: rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 8px; color: white; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.3s ease;"
                            target="_blank" onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Cetak Rekap SPP
                        </a>
                    </div>
                </div>
                <div class="history-container">
                    @if (count($pembayaran) > 0)
                        <div class="table-responsive">
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Tanggal</th>
                                        <th>Bulan</th>
                                        <th>SPP</th>
                                        <th>Konsumsi</th>
                                        <th>Fullday</th>
                                        <th>Jumlah Bayar</th>
                                        <th>Kembalian</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pembayaran as $index => $history)
                                        <tr>
                                            <td class="text-center">{{ $pembayaran->firstItem() + $loop->index }}</td>
                                            <td>
                                                <div>{{ $history->created_at->format('d M Y') }}</div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-success text-capitalize">
                                                    {{ $history->bulan }}
                                                </span>
                                            </td>
                                            <td class="amount-text">
                                                Rp {{ number_format($history->siswa->spp->nominal_spp ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="amount-text">
                                                Rp
                                                {{ number_format($history->siswa->spp->nominal_konsumsi ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="amount-text">
                                                Rp
                                                {{ number_format($history->siswa->spp->nominal_fullday ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="amount-text success-amount">
                                                Rp {{ number_format($history->jumlah_bayar, 0, ',', '.') }}
                                            </td>
                                            <td class="amount-text info-amount">
                                                Rp {{ number_format($history->kembalian, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <span class="status-badge status-success">
                                                    <i class="fas fa-check-circle"></i> Lunas
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('siswa.pembayaran.cetak', $history->id) }}"
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
                    @else
                        <div class="empty-state">
                            <i class="fas fa-receipt"></i>
                            <p>Tidak ada histori pembayaran ditemukan!</p>
                        </div>
                    @endif

                    <!-- Pagination -->
                    @if ($pembayaran->lastPage() != 1)
                        <div class="pagination-container">
                            <div class="pagination-wrapper">
                                <a href="{{ $pembayaran->previousPageUrl() }}"
                                    class="pagination-item {{ $pembayaran->onFirstPage() ? 'disabled' : '' }}">
                                    <i class="fas fa-chevron-left"></i>
                                </a>

                                @for ($i = 1; $i <= $pembayaran->lastPage(); $i++)
                                    <a href="{{ $pembayaran->url($i) }}"
                                        class="pagination-item {{ $i == $pembayaran->currentPage() ? 'active' : '' }}">
                                        {{ $i }}
                                    </a>
                                @endfor

                                <a href="{{ $pembayaran->nextPageUrl() }}"
                                    class="pagination-item {{ !$pembayaran->hasMorePages() ? 'disabled' : '' }}">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
