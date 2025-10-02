@extends('layouts.dashboard-siswa')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori Kegiatan Siswa</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div style="background: #fff; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.05); margin-bottom: 30px;">
                <div
                    style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); padding: 25px; border-radius: 15px 15px 0 0; color: white;">
                    <h4 style="margin: 0; font-size: 20px; font-weight: 600;">
                        <i class="fas fa-running"></i>
                        Histori Pembayaran Kegiatan
                    </h4>
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <a href="{{ route('siswa.kegiatan.rekap.cetak') }}"
                            style="background: rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 8px; color: white; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.3s ease;"
                            target="_blank" onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Cetak Rekap Kegiatan
                        </a>
                    </div>
                </div>
                <div class="history-container">
                    @if (count($kegiatanHistori) > 0)
                        <div class="table-responsive">
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Nama Kegiatan</th>
                                        <th class="text-center">Partisipasi</th>
                                        <th class="text-center">Angsuran Ke</th>
                                        <th class="text-center">Jumlah Bayar</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Petugas</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kegiatanHistori as $index => $kegiatan)
                                        <tr>
                                            <td class="text-center">{{ $kegiatanHistori->firstItem() + $index }}</td>
                                            <td>
                                                <div class="activity-name">{{ $kegiatan->kegiatan->nama_kegiatan ?? '-' }}</div>
                                                @if ($kegiatan->kegiatan && $kegiatan->kegiatan->deskripsi)
                                                    <small class="activity-desc">{{ $kegiatan->kegiatan->deskripsi }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="status-badge {{ $kegiatan->partisipasi == 'ikut' ? 'status-success' : 'status-secondary' }}">
                                                    {{ ucfirst($kegiatan->partisipasi) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="status-badge status-info">{{ $kegiatan->angsuran_ke }}</span>
                                            </td>
                                            <td class="text-center amount-text warning-amount">
                                                Rp {{ number_format($kegiatan->jumlah_bayar, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <div>{{ \Carbon\Carbon::parse($kegiatan->tgl_bayar)->format('d M Y') }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="status-badge {{ $kegiatan->is_lunas ? 'status-success' : 'status-secondary' }}">
                                                    <i
                                                        class="fas {{ $kegiatan->is_lunas ? 'fa-check' : 'fa-clock' }} mr-1"></i>
                                                    {{ $kegiatan->is_lunas ? 'Lunas' : 'Belum Lunas' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="status-badge status-info">
                                                    <i class="fas fa-user-circle mr-1"></i>
                                                    {{ $kegiatan->petugas->name ?? 'System' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('siswa.kegiatan.cetak', $kegiatan->id) }}"
                                                    style="background: #28a745; color: white; padding: 8px 16px; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center; transition: background 0.3s; font-size: 13px;"
                                                    onmouseover="this.style.background='#218838'"
                                                    onmouseout="this.style.background='#28a745'" target="_blank">
                                                    <i class="fas fa-print mr-2"></i>
                                                    Cetak
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
    
                        <!-- Pagination Custom -->
                        @if ($kegiatanHistori->lastPage() != 1)
                            <div class="pagination-container">
                                <div class="pagination-wrapper">
                                    <a href="{{ $kegiatanHistori->previousPageUrl() }}"
                                        class="pagination-item {{ $kegiatanHistori->onFirstPage() ? 'disabled' : '' }}">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
    
                                    @for ($i = 1; $i <= $kegiatanHistori->lastPage(); $i++)
                                        <a href="{{ $kegiatanHistori->url($i) }}"
                                            class="pagination-item {{ $i == $kegiatanHistori->currentPage() ? 'active' : '' }}">
                                            {{ $i }}
                                        </a>
                                    @endfor
    
                                    <a href="{{ $kegiatanHistori->nextPageUrl() }}"
                                        class="pagination-item {{ !$kegiatanHistori->hasMorePages() ? 'disabled' : '' }}">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-running"></i>
                            <p>Belum ada data pembayaran kegiatan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
