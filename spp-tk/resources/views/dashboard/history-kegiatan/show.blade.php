@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('history-kegiatan.index') }}">Histori Kegiatan</a></li>
    <li class="breadcrumb-item active">Detail Pembayaran</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Detail Pembayaran Kegiatan</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Informasi Siswa</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="120">NISN</th>
                                            <td>{{ $pembayaran->siswa->nisn }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama</th>
                                            <td>{{ $pembayaran->siswa->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kelas</th>
                                            <td>{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Informasi Pembayaran</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="120">Kegiatan</th>
                                            <td>{{ $pembayaran->kegiatan->nama_kegiatan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Bayar</th>
                                            <td>{{ $pembayaran->tgl_bayar->format('d F Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Angsuran Ke</th>
                                            <td>{{ $pembayaran->angsuran_ke }}</td>
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
                                        <tr>
                                            <th>Petugas</th>
                                            <td>{{ $pembayaran->petugas->name ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Rincian Pembayaran</h5>
                                    <table class="table">
                                        <tr>
                                            <th width="200">Nominal Kegiatan</th>
                                            <td>Rp {{ number_format($pembayaran->kegiatan->nominal, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Bayar</th>
                                            <td>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kembalian</th>
                                            <td class="{{ $pembayaran->kembalian > 0 ? 'text-success' : 'text-muted' }}">
                                                Rp {{ number_format($pembayaran->kembalian, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Total Dibayar</th>
                                            <td class="font-weight-bold">
                                                @php
                                                    $totalDibayar = \App\Models\SiswaKegiatan::where('id_siswa', $pembayaran->id_siswa)
                                                        ->where('id_kegiatan', $pembayaran->id_kegiatan)
                                                        ->sum('jumlah_bayar');
                                                @endphp
                                                Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Sisa Pembayaran</th>
                                            <td class="{{ ($pembayaran->kegiatan->nominal - $totalDibayar) > 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format(max(0, $pembayaran->kegiatan->nominal - $totalDibayar), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('history-kegiatan.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('entri-kegiatan.generate-pdf', $pembayaran->id) }}" 
                           class="btn btn-info" target="_blank">
                            <i class="mdi mdi-file-pdf"></i> Download Bukti
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection