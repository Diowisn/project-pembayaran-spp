@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tabungan.index') }}">Tabungan</a></li>
    <li class="breadcrumb-item active">Detail Tabungan - {{ $siswa->nama }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('tabungan.rekap.cetak', $siswa->id) }}" class="btn btn-primary" target="_blank">
                            <i class="mdi mdi-file-pdf"></i> Cetak Rekap
                        </a>

                        <a href="{{ route('tabungan.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <!-- Info Siswa -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>NISN:</strong> {{ $siswa->nisn }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Nama:</strong> {{ $siswa->nama }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas ?? '-' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Saldo Tabungan:</strong>
                                    <span class="badge badge-info">Rp {{ number_format($saldo, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Transaksi -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Riwayat Transaksi Tabungan</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Tipe</th>
                                            <th>Debit</th>
                                            <th>Kredit</th>
                                            <th>Saldo</th>
                                            <th>Keterangan</th>
                                            <th>Petugas</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($tabungan as $transaksi)
                                            <tr>
                                                <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @if ($transaksi->debit > 0)
                                                        <span class="badge badge-success">Setoran</span>
                                                    @else
                                                        <span class="badge badge-danger">Penarikan</span>
                                                    @endif
                                                </td>
                                                <td class="text-success">
                                                    @if ($transaksi->debit > 0)
                                                        Rp {{ number_format($transaksi->debit, 0, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-danger">
                                                    @if ($transaksi->kredit > 0)
                                                        Rp {{ number_format($transaksi->kredit, 0, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>Rp {{ number_format($transaksi->saldo, 0, ',', '.') }}</td>
                                                <td>{{ $transaksi->keterangan }}</td>
                                                <td>{{ $transaksi->petugas->name ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('tabungan.transaksi.cetak', $transaksi->id) }}"
                                                        class="btn btn-primary btn-sm" target="_blank">
                                                        <i class="mdi mdi-printer"></i> Cetak
                                                    </a>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada transaksi tabungan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if ($tabungan->lastPage() != 1)
                                <div class="btn-group float-right mt-3">
                                    <a href="{{ $tabungan->appends(request()->query())->previousPageUrl() }}"
                                        class="btn btn-success">
                                        <i class="mdi mdi-chevron-left"></i>
                                    </a>
                                    @for ($i = 1; $i <= $tabungan->lastPage(); $i++)
                                        <a class="btn btn-success {{ $i == $tabungan->currentPage() ? 'active' : '' }}"
                                            href="{{ $tabungan->appends(request()->query())->url($i) }}">
                                            {{ $i }}
                                        </a>
                                    @endfor
                                    <a href="{{ $tabungan->appends(request()->query())->nextPageUrl() }}"
                                        class="btn btn-success">
                                        <i class="mdi mdi-chevron-right"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
