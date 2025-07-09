@extends('layouts.dashboard')

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

                    @foreach ($infaqHistori as $value)
                        <div class="border-top">
                            <div class="float-right">
                                <i class="mdi mdi-check text-success"></i> {{ $value->created_at->format('d M, Y') }}
                            </div>
                            <div class="mt-4 text-uppercase">
                                {{ $value->siswa->nama . ' - ' . $value->siswa->kelas->nama_kelas }}
                            </div>
                            <div>Paket Infaq <b class="text-uppercase">{{ $value->infaqGedung->paket ?? '-' }}</b></div>
                            <div>=========================</div>
                            <div>Total Infaq Rp. {{ number_format($value->infaqGedung->nominal ?? 0, 0, ',', '.') }}</div>
                            <div>Angsuran Ke-{{ $value->angsuran_ke }}</div>
                            <div>=========================</div>
                            @php
                                $totalDibayar = $value->siswa->angsuranInfaq->sum('jumlah_bayar');
                                $sisaPembayaran = $value->infaqGedung->nominal - $totalDibayar;
                            @endphp
                            <div>Bayar Rp. {{ number_format($value->jumlah_bayar, 0, ',', '.') }}</div>
                            <div>Total Dibayar Rp. {{ number_format($totalDibayar, 0, ',', '.') }}</div>
                            <div>Sisa Rp. {{ number_format($sisaPembayaran, 0, ',', '.') }}</div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if ($infaqHistori->lastPage() != 1)
                        <div class="btn-group float-right">
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
                    <!-- End Pagination -->

                    @if (count($infaqHistori) == 0)
                        <div class="text-center">Tidak ada histori pembayaran infaq gedung</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection