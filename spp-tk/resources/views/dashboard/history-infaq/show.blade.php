@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('history-infaq.index') }}">Histori Infaq</a></li>
    <li class="breadcrumb-item active">Detail Pembayaran</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Detail Pembayaran Infaq Gedung</div>

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
                                    <th>Paket Infaq</th>
                                    <td>{{ $pembayaran->infaqGedung->paket ?? '-' }}</td>
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
                                    <th>Angsuran Ke</th>
                                    <td>{{ $pembayaran->angsuran_ke }}</td>
                                </tr>
                                <tr>
                                    <th>Petugas</th>
                                    <td>{{ $pembayaran->petugas->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($sisa_pembayaran <= 0)
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
                                        <td>Total Tagihan Infaq</td>
                                        <td class="text-right">Rp {{ number_format($total_tagihan, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <th>Total Tagihan</th>
                                        <th class="text-right">Rp {{ number_format($total_tagihan, 0, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Total Dibayar</th>
                                        <th class="text-right">Rp {{ number_format($total_dibayar, 0, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Sisa Pembayaran</th>
                                        <th class="text-right">
                                            @if($sisa_pembayaran > 0)
                                                <span class="text-danger">Rp {{ number_format($sisa_pembayaran, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-success">Rp 0</span>
                                            @endif
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Bayar (Angsuran ini)</th>
                                        <th class="text-right">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Kembalian</th>
                                        <th class="text-right">
                                            @if($pembayaran->kembalian > 0)
                                                <span class="text-success">Rp {{ number_format($pembayaran->kembalian, 0, ',', '.') }}</span>
                                            @else
                                                Rp 0
                                            @endif
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('history-infaq.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('infaq.generate', $pembayaran->id) }}" class="btn btn-info" target="_blank">
                            <i class="mdi mdi-file-pdf"></i> Download Bukti
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection