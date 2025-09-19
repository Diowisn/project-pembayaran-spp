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
                                    <th>Paket Kegiatan</th>
                                    <td>{{ $pembayaran->kegiatan->nama_paket ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Kegiatan</th>
                                    <td>{{ $pembayaran->kegiatan->nama_kegiatan }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Bayar</th>
                                    <td>{{ $pembayaran->tgl_bayar ? $pembayaran->tgl_bayar->format('d/m/Y H:i') : '-' }}</td>
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
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $pembayaran->keterangan ?? '-' }}</td>
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
                                        <td>Nominal Kegiatan</td>
                                        <td class="text-right">Rp {{ number_format($pembayaran->kegiatan->nominal, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <th>Total Tagihan</th>
                                        <th class="text-right">Rp {{ number_format($pembayaran->kegiatan->nominal, 0, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Bayar</th>
                                        <th class="text-right">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Kembalian</th>
                                        <th class="text-right">
                                            @if($pembayaran->kembalian > 0)
                                                <span class="text-success">Rp {{ number_format($pembayaran->kembalian, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Total Dibayar</th>
                                        <th class="text-right">
                                            @php
                                                $totalDibayar = \App\Models\SiswaKegiatan::where('id_siswa', $pembayaran->id_siswa)
                                                    ->where('id_kegiatan', $pembayaran->id_kegiatan)
                                                    ->where('partisipasi', 'ikut')
                                                    ->sum('jumlah_bayar');
                                            @endphp
                                            Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Sisa Pembayaran</th>
                                        <th class="text-right {{ ($pembayaran->kegiatan->nominal - $totalDibayar) > 0 ? 'text-danger' : 'text-success' }}">
                                            Rp {{ number_format(max(0, $pembayaran->kegiatan->nominal - $totalDibayar), 0, ',', '.') }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Riwayat Pembayaran untuk Kegiatan yang Sama -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Riwayat Pembayaran untuk Kegiatan Ini</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tanggal</th>
                                            <th>Angsuran</th>
                                            <th class="text-right">Jumlah Bayar</th>
                                            <th class="text-right">Kembalian</th>
                                            <th>Petugas</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $riwayatPembayaran = \App\Models\SiswaKegiatan::with('petugas')
                                                ->where('id_siswa', $pembayaran->id_siswa)
                                                ->where('id_kegiatan', $pembayaran->id_kegiatan)
                                                ->where('partisipasi', 'ikut')
                                                ->orderBy('tgl_bayar', 'asc')
                                                ->get();
                                        @endphp
                                        
                                        @foreach($riwayatPembayaran as $index => $riwayat)
                                            <tr class="{{ $riwayat->id == $pembayaran->id ? 'table-info' : '' }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $riwayat->tgl_bayar ? $riwayat->tgl_bayar->format('d/m/Y') : '-' }}</td>
                                                <td>{{ $riwayat->angsuran_ke }}</td>
                                                <td class="text-right">Rp {{ number_format($riwayat->jumlah_bayar, 0, ',', '.') }}</td>
                                                <td class="text-right">
                                                    @if($riwayat->kembalian > 0)
                                                        <span class="text-success">Rp {{ number_format($riwayat->kembalian, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $riwayat->petugas->name ?? '-' }}</td>
                                                <td>
                                                    @if ($riwayat->is_lunas)
                                                        <span class="badge badge-success">Lunas</span>
                                                    @else
                                                        <span class="badge badge-warning">Belum</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if($riwayatPembayaran->isEmpty())
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada riwayat pembayaran lainnya</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('history-kegiatan.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali ke Daftar
                        </a>
                        <a href="{{ route('entri-kegiatan.generate-pdf', $pembayaran->id) }}" class="btn btn-info" target="_blank">
                            <i class="mdi mdi-file-pdf"></i> Download Bukti Pembayaran
                        </a>
                        
                        @can('admin')
                        <form action="{{ route('history-kegiatan.destroy', $pembayaran->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')">
                                <i class="mdi mdi-delete"></i> Hapus Pembayaran
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .table th {
        background-color: #f8f9fa;
    }
    .badge-success {
        background-color: #28a745;
    }
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    .table-info {
        background-color: #d1ecf1;
    }
</style>
@endsection