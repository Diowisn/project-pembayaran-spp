@extends('layouts.dashboard-siswa')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori</li>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <div class="card-title">Histori Pembayaran</div>

                    <!--  Row -->
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
                                            Rp.{{ $history->siswa->spp->nominal_konsumsi ?? '-' }}</li>
                                        <li class="list-group-item">Nominal Fullday Rp.
                                            {{ $history->siswa->spp->nominal_fullday ?? '-' }}</li>
                                        <li class="list-group-item">Jumlah Bayar Rp. {{ $history->jumlah_bayar }}</li>
                                        <li class="list-group-item">Kembalian Rp. {{ $history->kembalian }}</li>
                                    </ul>
                                </span>
                                <div class="comment-footer">
                                    <span class="text-muted float-right">{{ $history->created_at->format('M d, Y') }}</span>
                                    <span class="action-icons active" style="background-color: #FB9E3A;">
                                        <a href="{{ route('siswa.pembayaran.cetak', $history->id) }}" class="mr-2"
                                            title="Cetak Bukti" style="color: white;">
                                            <i class="mdi mdi-printer"></i> Cetak Bukti Pembayaran 
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if ($pembayaran->lastPage() != 1)
                        <div class="btn-group float-right mt-4">
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
                        <div class="text-center">Tidak ada histori!</div>
                    @endif
                </div>

            </div>


        </div>
    </div>

@endsection
