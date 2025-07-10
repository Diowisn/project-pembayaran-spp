@extends('layouts.dashboard-siswa')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori Infaq Gedung</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Histori Pembayaran Infaq Gedung</div>

                    @foreach ($infaqHistori as $history)
                        <div class="d-flex flex-row comment-row">
                            <i class="mdi mdi-home display-3"></i> <!-- Ganti icon untuk infaq -->
                            <div class="comment-text active w-100">
                                <hr>
                                <span class="badge badge-primary badge-rounded float-right">
                                    {{ $history->created_at->diffforHumans() }}
                                </span>
                                <h6 class="font-medium">{{ $history->siswa->nama }}</h6>
                                <span class="m-b-15 d-block">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">Kelas {{ $history->siswa->kelas->nama_kelas }}</li>
                                        <li class="list-group-item">Paket Infaq
                                            <b class="text-uppercase">{{ $history->infaqGedung->paket ?? '-' }}</b>
                                        </li>
                                        <li class="list-group-item">Total Infaq Rp.
                                            {{ number_format($history->infaqGedung->nominal ?? 0, 0, ',', '.') }}
                                        </li>
                                        <li class="list-group-item">Angsuran Ke-{{ $history->angsuran_ke }}</li>
                                        <li class="list-group-item">Jumlah Bayar Rp.
                                            {{ number_format($history->jumlah_bayar, 0, ',', '.') }}
                                        </li>
                                        @php
                                            $totalDibayar = $history->siswa->angsuranInfaq->sum('jumlah_bayar');
                                            $sisaPembayaran = $history->infaqGedung->nominal - $totalDibayar;
                                        @endphp
                                        <li class="list-group-item">Total Dibayar Rp.
                                            {{ number_format($totalDibayar, 0, ',', '.') }}
                                        </li>
                                        <li class="list-group-item">Sisa Pembayaran Rp.
                                            {{ number_format($sisaPembayaran, 0, ',', '.') }}
                                        </li>
                                    </ul>
                                </span>
                                <div class="comment-footer">
                                    <span class="text-muted float-right">
                                        {{ $history->created_at->format('M d, Y') }}
                                    </span>
                                    <span class="action-icons active" style="background-color: #4CAF50;">
                                        <a href="{{ route('siswa.infaq.cetak', $history->id) }}" class="mr-2"
                                            title="Cetak Bukti" style="color: white">
                                            <i class="mdi mdi-printer"></i> Cetak Bukti Pembayaran
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if ($infaqHistori->lastPage() != 1)
                        <div class="btn-group float-right mt-4">
                            <a href="{{ $infaqHistori->previousPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $infaqHistori->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $infaqHistori->currentPage() ? 'active' : '' }}"
                                    href="{{ $infaqHistori->url($i) }}">{{ $i }}</a>
                            @endfor
                            <a href="{{ $infaqHistori->nextPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif

                    @if (count($infaqHistori) == 0)
                        <div class="text-center">Belum ada histori pembayaran infaq gedung</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
