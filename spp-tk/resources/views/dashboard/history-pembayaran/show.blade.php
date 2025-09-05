@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('history-pembayaran.index') }}">Histori Pembayaran</a></li>
    <li class="breadcrumb-item active">Detail Pembayaran</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Detail Pembayaran SPP</div>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">NISN</th>
                                    <td>{{ $pembayaran->siswa->nisn }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <td>{{ $pembayaran->siswa->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td>{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Bulan/Tahun</th>
                                    <td>{{ ucfirst($pembayaran->bulan) }} {{ $pembayaran->tahun }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Tanggal Bayar</th>
                                    <td>{{ $pembayaran->tgl_bayar ? $pembayaran->tgl_bayar->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Petugas</th>
                                    <td>{{ $pembayaran->petugas->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($pembayaran->is_lunas)
                                            <span class="badge badge-success">Lunas</span>
                                        @else
                                            <span class="badge badge-warning">Belum Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Rincian Pembayaran</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Komponen</th>
                                        <th class="text-right">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>SPP</td>
                                        <td class="text-right">Rp {{ number_format($pembayaran->nominal_spp, 0, ',', '.') }}</td>
                                    </tr>
                                    @if($pembayaran->nominal_konsumsi > 0)
                                    <tr>
                                        <td>Konsumsi</td>
                                        <td class="text-right">Rp {{ number_format($pembayaran->nominal_konsumsi, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if($pembayaran->nominal_fullday > 0)
                                    <tr>
                                        <td>Fullday</td>
                                        <td class="text-right">Rp {{ number_format($pembayaran->nominal_fullday, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if($pembayaran->nominal_inklusi > 0)
                                    <tr>
                                        <td>Inklusi</td>
                                        <td class="text-right">Rp {{ number_format($pembayaran->nominal_inklusi, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <th>Total Tagihan</th>
                                        <th class="text-right">Rp {{ number_format($pembayaran->nominal_spp + $pembayaran->nominal_konsumsi + $pembayaran->nominal_fullday + $pembayaran->nominal_inklusi, 0, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Bayar</th>
                                        <th class="text-right">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Kembalian</th>
                                        <th class="text-right">Rp {{ number_format($pembayaran->kembalian, 0, ',', '.') }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('history-pembayaran.index') }}" class="btn btn-secondary">Kembali</a>
                        <a href="{{ route('pembayaran.generate', $pembayaran->id) }}" class="btn btn-info" target="_blank">
                            <i class="mdi mdi-file-pdf"></i> Download Bukti
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection