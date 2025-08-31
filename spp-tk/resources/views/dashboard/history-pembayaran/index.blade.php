@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Histori Pembayaran SPP</div>

                    @foreach ($pembayaran as $value)
                        <div class="border-top mb-3 p-3">
                            <div class="float-right">
                                <i class="mdi mdi-check text-success"></i> {{ $value->created_at->format('d M, Y') }}
                            </div>
                            <div class="mt-4 text-uppercase">
                                {{ $value->siswa->nama . ' - ' . $value->siswa->kelas->nama_kelas }}
                            </div>
                            <div>SPP Bulan <b class="text-capitalize">{{ $value->bulan }}</b></div>
                            <div>=========================</div>
                            <div>Nominal SPP: Rp. {{ number_format($value->nominal_spp, 0, ',', '.') }}</div>
                            
                            @if($value->nominal_konsumsi > 0)
                                <div>Nominal Konsumsi: Rp. {{ number_format($value->nominal_konsumsi, 0, ',', '.') }}</div>
                            @endif
                            
                            @if($value->nominal_fullday > 0)
                                <div>Nominal Fullday: Rp. {{ number_format($value->nominal_fullday, 0, ',', '.') }}</div>
                            @endif
                            
                            @if($value->nominal_inklusi > 0)
                                <div>Nominal Inklusi: Rp. {{ number_format($value->nominal_inklusi, 0, ',', '.') }}</div>
                                @if($value->siswa->paketInklusi)
                                    <small class="text-muted">
                                        (Paket: {{ $value->siswa->paketInklusi->nama_paket }})
                                    </small>
                                @endif
                            @endif
                            
                            <div>=========================</div>
                            <div>Total Bayar: Rp. {{ number_format($value->jumlah_bayar, 0, ',', '.') }}</div>
                            <div>Kembalian: Rp. {{ number_format($value->kembalian, 0, ',', '.') }}</div>
                            <div>Status: 
                                @if ($value->is_lunas)
                                    <span class="badge badge-success">Lunas</span>
                                @else
                                    <span class="badge badge-warning">Belum Lunas</span>
                                @endif
                            </div>
                            <div>Petugas: {{ $value->petugas->name }}</div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if ($pembayaran->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $pembayaran->previousPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $pembayaran->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $pembayaran->currentPage() ? 'active' : '' }}"
                                    href="{{ $pembayaran->url($i) }}">{{ $i }}</a>
                            @endfor
                            <a href="{{ $pembayaran->nextPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                    <!-- End Pagination -->

                    @if (count($pembayaran) == 0)
                        <div class="text-center">Tidak ada histori pembayaran</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection