@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori Pembayaran Kegiatan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Histori Pembayaran Kegiatan</div>

                    <!-- Form Filter -->
                    <form method="GET" action="{{ route('history-kegiatan.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Cari NISN/Nama Siswa</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                        value="{{ $search }}" placeholder="NISN atau nama siswa">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="kegiatan">Kegiatan</label>
                                    <select class="form-control" id="kegiatan" name="kegiatan">
                                        <option value="">Semua Kegiatan</option>
                                        @foreach($kegiatanList as $kegiatan)
                                            <option value="{{ $kegiatan->id }}" 
                                                {{ $selectedKegiatan == $kegiatan->id ? 'selected' : '' }}>
                                                {{ $kegiatan->nama_kegiatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="kelas">Kelas</label>
                                    <select class="form-control" id="kelas" name="kelas">
                                        <option value="">Semua Kelas</option>
                                        @foreach($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}" 
                                                {{ $selectedKelas == $kelas->id ? 'selected' : '' }}>
                                                {{ $kelas->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tanggal_mulai">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" 
                                        value="{{ $tanggalMulai }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tanggal_akhir">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" 
                                        value="{{ $tanggalAkhir }}">
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
                                    <th>Kegiatan</th>
                                    <th>Angsuran Ke</th>
                                    <th>Jumlah Bayar</th>
                                    <th>Kembalian</th>
                                    <th>Status</th>
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pembayaranKegiatan as $index => $value)
                                    <tr>
                                        <td>{{ $pembayaranKegiatan->firstItem() + $index }}</td>
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
                                        <td>{{ $value->kegiatan->nama_kegiatan }}</td>
                                        <td class="text-center">{{ $value->angsuran_ke }}</td>
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
                                                <a href="{{ route('entri-kegiatan.generate-pdf', $value->id) }}" 
                                                   class="btn btn-info btn-sm" target="_blank" title="Download Bukti">
                                                    <i class="mdi mdi-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('history-kegiatan.show', $value->id) }}" 
                                                   class="btn btn-primary btn-sm" title="Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                @can('admin')
                                                <form action="{{ route('history-kegiatan.destroy', $value->id) }}" 
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
                                        <td colspan="12" class="text-center">Tidak ada data pembayaran kegiatan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($pembayaranKegiatan->lastPage() > 1)
                        <div class="btn-group float-right mt-3">
                            <a href="{{ $pembayaranKegiatan->previousPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $pembayaranKegiatan->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $pembayaranKegiatan->currentPage() ? 'active' : '' }}"
                                    href="{{ $pembayaranKegiatan->url($i) }}">{{ $i }}</a>
                            @endfor
                            <a href="{{ $pembayaranKegiatan->nextPageUrl() }}" class="btn btn-success">
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
            $('#kegiatan').val('');
            $('#kelas').val('');
            $('#tanggal_mulai').val('');
            $('#tanggal_akhir').val('');
            $('form').submit();
        });
    });
</script>
@endsection