@extends('layouts.dashboard-siswa')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori Infaq Gedung</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 15px rgba(0,0,0,0.1);">
                <div style="padding: 25px;">
                    <h4 style="color: #333; font-size: 24px; margin-bottom: 25px; font-weight: 600;">Histori Pembayaran Infaq Gedung</h4>

                    @foreach ($infaqHistori as $history)
                        <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 20px; border-left: 5px solid #28a745; transition: transform 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: center; margin-bottom: 20px;">
                                <div style="background: #28a745; border-radius: 50%; padding: 15px; margin-right: 15px;">
                                    <i class="mdi mdi-home" style="color: white; font-size: 24px;"></i>
                                </div>
                                <div>
                                    <h5 style="margin: 0; color: #2c3e50; font-size: 18px; font-weight: 600;">{{ $history->siswa->nama }}</h5>
                                    <span style="background: #4e73df; color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; display: inline-block; margin-top: 5px;">
                                        {{ $history->created_at->diffforHumans() }}
                                    </span>
                                </div>
                            </div>
                            <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <div style="padding: 15px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                        <span style="color: #555;">Kelas</span>
                                        <span style="font-weight: 600;">{{ $history->siswa->kelas->nama_kelas }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                        <span style="color: #555;">Paket Infaq</span>
                                        <span style="font-weight: 600; text-transform: uppercase;">{{ $history->infaqGedung->paket ?? '-' }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                        <span style="color: #555;">Total Infaq</span>
                                        <span style="font-weight: 600;">Rp. {{ number_format($history->infaqGedung->nominal ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                        <span style="color: #555;">Angsuran Ke-</span>
                                        <span style="font-weight: 600; color: #4e73df;">{{ $history->angsuran_ke }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                        <span style="color: #555;">Jumlah Bayar</span>
                                        <span style="font-weight: 600; color: #28a745;">Rp. {{ number_format($history->jumlah_bayar, 0, ',', '.') }}</span>
                                    </div>
                                    @php
                                        $totalDibayar = $history->siswa->angsuranInfaq->sum('jumlah_bayar');
                                        $sisaPembayaran = $history->infaqGedung->nominal - $totalDibayar;
                                    @endphp
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                                        <span style="color: #555;">Total Dibayar</span>
                                        <span style="font-weight: 600; color: #17a2b8;">Rp. {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                                        <span style="color: #555;">Sisa Pembayaran</span>
                                        <span style="font-weight: 600; color: #dc3545;">Rp. {{ number_format($sisaPembayaran, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                <span style="color: #6c757d;">{{ $history->created_at->format('M d, Y') }}</span>
                                <a href="{{ route('siswa.infaq.cetak', $history->id) }}" 
                                   style="background: #28a745; color: white; padding: 8px 16px; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center; transition: background 0.3s;">
                                    <i class="mdi mdi-printer" style="margin-right: 8px;"></i>
                                    Cetak Bukti Pembayaran
                                </a>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if ($infaqHistori->lastPage() != 1)
                        <div style="display: flex; justify-content: center; margin-top: 30px;">
                            <div style="display: inline-flex; background: white; border-radius: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 5px;">
                                <a href="{{ $infaqHistori->previousPageUrl() }}" 
                                   style="color: {{ $infaqHistori->onFirstPage() ? '#ccc' : '#28a745' }}; padding: 8px 16px; text-decoration: none; {{ $infaqHistori->onFirstPage() ? 'pointer-events: none;' : '' }}">
                                    <i class="mdi mdi-chevron-left"></i>
                                </a>
                                @for ($i = 1; $i <= $infaqHistori->lastPage(); $i++)
                                    <a href="{{ $infaqHistori->url($i) }}" 
                                       style="color: {{ $i == $infaqHistori->currentPage() ? 'white' : '#28a745' }}; 
                                              background: {{ $i == $infaqHistori->currentPage() ? '#28a745' : 'transparent' }}; 
                                              padding: 8px 16px; 
                                              text-decoration: none;
                                              border-radius: 20px;
                                              margin: 0 2px;">
                                        {{ $i }}
                                    </a>
                                @endfor
                                <a href="{{ $infaqHistori->nextPageUrl() }}" 
                                   style="color: {{ !$infaqHistori->hasMorePages() ? '#ccc' : '#28a745' }}; padding: 8px 16px; text-decoration: none; {{ !$infaqHistori->hasMorePages() ? 'pointer-events: none;' : '' }}">
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (count($infaqHistori) == 0)
                        <div style="background-color: #cce5ff; border: 1px solid #b8daff; color: #004085; padding: 20px; border-radius: 8px; text-align: center; margin-top: 20px;">
                            <i class="mdi mdi-information-outline" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                            <p style="margin: 0; font-size: 16px;">Belum ada histori pembayaran infaq gedung</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection