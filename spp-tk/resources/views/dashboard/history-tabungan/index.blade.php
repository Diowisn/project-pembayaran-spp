@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori Tabungan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Histori Transaksi Tabungan</div>

                    <!-- Form Filter -->
                    <form method="GET" action="{{ route('history-tabungan.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">Cari NISN/Nama Siswa</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                        value="{{ request('search') }}" placeholder="NISN atau nama siswa">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kelas">Kelas</label>
                                    <select class="form-control" id="kelas" name="kelas">
                                        <option value="">Semua Kelas</option>
                                        @foreach($kelasList as $kelasItem)
                                            <option value="{{ $kelasItem->id }}" 
                                                {{ request('kelas') == $kelasItem->id ? 'selected' : '' }}>
                                                {{ $kelasItem->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                        @if (request()->has('search') || request()->has('kelas'))
                                            <a href="{{ route('history-tabungan.index') }}" class="btn btn-secondary">Reset</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tabel Histori -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Saldo</th>
                                    <th>Keterangan</th>
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tabunganHistori as $index => $transaksi)
                                    <tr>
                                        <td>{{ $tabunganHistori->firstItem() + $index }}</td>
                                        <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $transaksi->siswa->nisn }}</td>
                                        <td>{{ $transaksi->siswa->nama }}</td>
                                        <td>{{ $transaksi->siswa->kelas->nama_kelas ?? '-' }}</td>
                                        <td>
                                            @if($transaksi->debit > 0)
                                                <span class="badge badge-success">SETOR</span>
                                            @else
                                                <span class="badge badge-danger">TARIK</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if($transaksi->debit > 0)
                                                <span class="text-success">Rp {{ number_format($transaksi->debit, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-danger">Rp {{ number_format($transaksi->kredit, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-right">Rp {{ number_format($transaksi->saldo, 0, ',', '.') }}</td>
                                        <td>{{ $transaksi->keterangan }}</td>
                                        <td>{{ $transaksi->petugas->name ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('tabungan.transaksi.cetak', $transaksi->id) }}" 
                                                   class="btn btn-info btn-sm" target="_blank" title="Download Bukti">
                                                    <i class="mdi mdi-file-pdf"></i>
                                                </a>
                                                @can('admin')
                                                <form action="{{ route('tabungan.destroy', $transaksi->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('Hapus transaksi ini?')" title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">Tidak ada data transaksi tabungan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($tabunganHistori->lastPage() > 1)
                        <div class="btn-group float-right mt-3">
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
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Reset filter
        $('#reset-filter').click(function() {
            $('#search').val('');
            $('#kelas').val('');
            $('form').submit();
        });
    });
</script>
@endsection