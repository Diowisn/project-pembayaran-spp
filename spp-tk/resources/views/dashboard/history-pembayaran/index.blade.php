@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori Pembayaran SPP</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Histori Pembayaran SPP</div>

                    <!-- Form Filter -->
                    <form method="GET" action="{{ route('history-pembayaran.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Cari NISN/Nama Siswa</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                        value="{{ request('search') }}" placeholder="NISN atau nama siswa">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="kelas">Kelas</label>
                                    <select class="form-control" id="kelas" name="kelas">
                                        <option value="">Semua Kelas</option>
                                        @foreach($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}" 
                                                {{ request('kelas') == $kelas->id ? 'selected' : '' }}>
                                                {{ $kelas->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="bulan">Bulan</label>
                                    <select class="form-control" id="bulan" name="bulan">
                                        <option value="">Semua Bulan</option>
                                        @foreach(['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'] as $bulan)
                                            <option value="{{ $bulan }}" 
                                                {{ request('bulan') == $bulan ? 'selected' : '' }}>
                                                {{ ucfirst($bulan) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tahun">Tahun</label>
                                    <input type="number" class="form-control" id="tahun" name="tahun" 
                                        value="{{ request('tahun') }}" placeholder="Tahun" min="2020" max="2030">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                        <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Lunas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
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
                                    <th>Bulan/Tahun</th>
                                    <th>Total Tagihan</th>
                                    <th>Jumlah Bayar</th>
                                    <th>Kembalian</th>
                                    <th>Status</th>
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pembayaran as $index => $value)
                                    @php
                                        $total_tagihan = ($value->nominal_spp ?? 0) + 
                                                        ($value->nominal_konsumsi ?? 0) + 
                                                        ($value->nominal_fullday ?? 0) + 
                                                        ($value->nominal_inklusi ?? 0);
                                    @endphp
                                    <tr>
                                        <td>{{ $pembayaran->firstItem() + $index }}</td>
                                        <td>
                                            @if($value->tgl_bayar)
                                                {{ $value->tgl_bayar->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $value->siswa->nisn }}</td>
                                        <td>{{ $value->siswa->nama }}</td>
                                        <td>{{ $value->siswa->kelas->nama_kelas ?? '-' }}</td>
                                        <td>{{ ucfirst($value->bulan) }} {{ $value->tahun }}</td>
                                        <td class="text-right">Rp {{ number_format($total_tagihan, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($value->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td class="text-right">
                                            @if($value->kembalian > 0)
                                                <span class="text-success">Rp {{ number_format($value->kembalian, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($value->is_lunas)
                                                <span class="badge badge-success">Lunas</span>
                                            @else
                                                <span class="badge badge-warning">Belum Lunas</span>
                                            @endif
                                        </td>
                                        <td>{{ $value->petugas->name ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('pembayaran.generate', $value->id) }}" 
                                                   class="btn btn-info btn-sm" target="_blank" title="Download Bukti">
                                                    <i class="mdi mdi-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('history-pembayaran.show', $value->id) }}" 
                                                   class="btn btn-primary btn-sm" title="Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                @can('admin')
                                                <form action="{{ route('history-pembayaran.destroy', $value->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('Hapus pembayaran ini?')" title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">Tidak ada data pembayaran SPP</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($pembayaran->lastPage() > 1)
                        <div class="btn-group float-right mt-3">
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
            $('#bulan').val('');
            $('#tahun').val('');
            $('#status').val('');
            $('form').submit();
        });
    });
</script>
@endsection