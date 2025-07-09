@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Home</li>
@endsection

@section('content')
    <div class="alert alert-success text-center"><b>Selamat Datang</b> di aplikasi pembayaran SPP Sekolah</div>

    <!-- Widget Pemasukan per Kelas -->
<!-- Widget Pemasukan per Kelas -->
<div class="row mb-4">
    @foreach($pemasukanSPPPerKelas as $kelas => $total)
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Kelas {{ $kelas }}</h6>
                        <h4 class="mb-0">Rp. {{ number_format($total, 0, ',', '.') }}</h4>
                    </div>
                    <div class="bg-primary rounded p-3">
                        <i class="mdi mdi-account-multiple text-white"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Total Pemasukan SPP</small>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    
</div>
<div class="row mb-4">
    <!-- Widget Total Infaq Gedung -->
    @foreach($pemasukanInfaqPerKelas as $kelas => $total)
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Kelas {{ $kelas }}</h6>
                        <h4 class="mb-0">Rp. {{ number_format($total, 0, ',', '.') }}</h4>
                    </div>
                    <div class="bg-success rounded p-3">
                        <i class="mdi mdi-home text-white"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Total Pemasukan Infaq</small>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
    <!-- End Widget -->

    <div class="row">
        <!-- Histori Pembayaran SPP -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Histori Pembayaran SPP</div>
                    <div class="comment-widgets scrollable" style="max-height: 600px; overflow-y: auto;">

                        @foreach ($pembayaran as $history)
                            <div class="d-flex flex-row comment-row">
                                <i class="mdi mdi-account display-3"></i>
                                <div class="comment-text active w-100">
                                    <hr>
                                    <span
                                        class="badge badge-success badge-rounded float-right">{{ $history->created_at->diffforHumans() }}</span>
                                    <h6 class="font-medium">{{ $history->siswa->nama }}</h6>
                                    <span class="m-b-15 d-block">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">Kelas {{ $history->siswa->kelas->nama_kelas }} ~ SPP
                                                Bulan <b class="text-capitalize text-bold">{{ $history->bulan }}</b></li>
                                            <li class="list-group-item">Nominal SPP Rp.
                                                {{ $history->siswa->spp->nominal_spp ?? '-' }}</li>
                                            <li class="list-group-item">Nominal Konsumsi
                                                Rp. {{ $history->siswa->spp->nominal_konsumsi ?? '-' }}</li>
                                            <li class="list-group-item">Nominal Fullday Rp.
                                                {{ $history->siswa->spp->nominal_fullday ?? '-' }}</li>
                                            <li class="list-group-item">Jumlah Bayar Rp. {{ $history->jumlah_bayar }}</li>
                                            <li class="list-group-item">Kembalian Rp. {{ $history->kembalian }}</li>
                                        </ul>
                                    </span>
                                    <div class="comment-footer">
                                        <span class="text-muted float-right">{{ $history->created_at->format('M d, Y') }}</span>
                                        <span class="action-icons active">
                                            <a href="{{ route('pembayaran.generate', $history->id) }}" class="mr-2" title="Cetak Bukti">
                                                <i class="mdi mdi-printer"></i>
                                            </a>
                                            <a href="{{ url('dashboard/pembayaran/' . $history->id . '/edit') }}" title="Edit">
                                                <i class="ti-pencil-alt"></i>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if (count($pembayaran) == 0)
                            <div class="text-center"> Tidak ada histori pembayaran SPP!</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Histori Pembayaran Infaq Gedung -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Histori Pembayaran Infaq Gedung</div>
                    <div class="comment-widgets scrollable" style="max-height: 600px; overflow-y: auto;">

                        @foreach ($infaqHistori as $history)
                            <div class="d-flex flex-row comment-row">
                                <i class="mdi mdi-home display-3"></i>
                                <div class="comment-text active w-100">
                                    <hr>
                                    <span
                                        class="badge badge-primary badge-rounded float-right">{{ $history->created_at->diffforHumans() }}</span>
                                    <h6 class="font-medium">{{ $history->siswa->nama }}</h6>
                                    <span class="m-b-15 d-block">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">Kelas {{ $history->siswa->kelas->nama_kelas }} ~ Paket 
                                                <b class="text-uppercase text-bold">{{ $history->infaqGedung->paket ?? '-' }}</b></li>
                                            <li class="list-group-item">Total Infaq Rp.
                                                {{ number_format($history->infaqGedung->nominal ?? 0, 0, ',', '.') }}</li>
                                            <li class="list-group-item">Angsuran Ke-{{ $history->angsuran_ke }}</li>
                                            <li class="list-group-item">Jumlah Bayar Rp. 
                                                {{ number_format($history->jumlah_bayar, 0, ',', '.') }}</li>
                                            <li class="list-group-item">Sisa Pembayaran Rp. 
                                                {{ number_format($history->infaqGedung->nominal - $history->siswa->angsuranInfaq->sum('jumlah_bayar'), 0, ',', '.') }}</li>
                                        </ul>
                                    </span>
                                    <div class="comment-footer">
                                        <span class="text-muted float-right">{{ $history->created_at->format('M d, Y') }}</span>
                                        <span class="action-icons active">
                                            <a href="{{ route('infaq.generate', $history->id) }}" class="mr-2" title="Cetak Bukti">
                                                <i class="mdi mdi-printer"></i>
                                            </a>
                                            <a href="{{ route('infaq.edit', $history->id) }}" title="Edit">
                                                <i class="ti-pencil-alt"></i>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if (count($infaqHistori) == 0)
                            <div class="text-center"> Tidak ada histori pembayaran infaq gedung!</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection