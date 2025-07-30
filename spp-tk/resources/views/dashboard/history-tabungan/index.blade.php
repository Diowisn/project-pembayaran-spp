@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori Tabungan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Form Pencarian -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('histori.tabungan') }}" class="form-inline">
                        <input type="text" name="search" class="form-control mr-2" placeholder="Cari NISN/Nama Siswa"
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary mr-2">Cari</button>
                        @if (request()->has('search'))
                            <a href="{{ route('histori.tabungan') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">Histori Transaksi Tabungan</div>

                    @foreach ($tabunganHistori as $transaksi)
                        <div class="border-top">
                            <div class="float-right">
                                <i class="mdi mdi-check text-success"></i> {{ $transaksi->created_at->format('d M, Y H:i') }}
                            </div>
                            <div class="mt-4 text-uppercase">
                                {{ $transaksi->siswa->nama . ' - ' . $transaksi->siswa->kelas->nama_kelas }}
                            </div>
                            <div>=========================</div>
                            <div>
                                @if($transaksi->debit > 0)
                                    <span class="text-success">Setoran: Rp. {{ number_format($transaksi->debit, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-danger">Penarikan: Rp. {{ number_format($transaksi->kredit, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <div>Saldo: Rp. {{ number_format($transaksi->saldo, 0, ',', '.') }}</div>
                            <div>=========================</div>
                            <div>Petugas: {{ $transaksi->petugas->name ?? 'Administrator' }}</div>
                            <div>Keterangan: {{ $transaksi->keterangan }}</div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if ($tabunganHistori->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $tabunganHistori->previousPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $tabunganHistori->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $tabunganHistori->currentPage() ? 'active' : '' }}"
                                    href="{{ $tabunganHistori->url($i) }}">{{ $i }}</a>
                            @endfor
                            <a href="{{ $tabunganHistori->nextPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                    <!-- End Pagination -->

                    @if (count($tabunganHistori) == 0)
                        <div class="text-center">Tidak ada histori transaksi tabungan</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection