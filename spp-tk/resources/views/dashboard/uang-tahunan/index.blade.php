@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Uang Kegiatan Tahunan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Entri Uang Kegiatan Tahunan</div>

                    <!-- Form Pencarian Siswa -->
                    <form method="GET" action="{{ route('uang-tahunan.index') }}">
                        <div class="form-group">
                            <label for="nisn">Cari Berdasarkan NISN</label>
                            <div class="input-group">
                                <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN"
                                    value="{{ request('nisn') }}" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Cari</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if (isset($siswa))
                        <form method="POST" action="{{ route('uang-tahunan.store-manual') }}">
                            @csrf
                            <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">

                            <!-- Info Siswa dalam Grid -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nama Siswa</label>
                                        <input type="text" class="form-control" value="{{ $siswa->nama }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>NISN</label>
                                        <input type="text" class="form-control" value="{{ $siswa->nisn }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Kelas</label>
                                        <input type="text" class="form-control"
                                            value="{{ $siswa->kelas->nama_kelas ?? '-' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Input -->
                            <div class="form-group">
                                <label for="tahun_ajaran">Tahun Ajaran</label>
                                <input type="number" class="form-control @error('tahun_ajaran') is-invalid @enderror"
                                    id="tahun_ajaran" name="tahun_ajaran" value="{{ old('tahun_ajaran', now()->year) }}"
                                    min="2000" max="2100" required>
                                @error('tahun_ajaran')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="jumlah">Jumlah</label>
                                <input type="number" class="form-control @error('jumlah') is-invalid @enderror"
                                    id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="1" required>
                                @error('jumlah')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <input type="text" class="form-control @error('keterangan') is-invalid @enderror"
                                    id="keterangan" name="keterangan" value="{{ old('keterangan') }}" required>
                                @error('keterangan')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success btn-rounded float-right">
                                <i class="mdi mdi-check"></i> Simpan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Form Pencarian -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('uang-tahunan.index') }}" class="form-inline">
                        <input type="text" name="search" class="form-control mr-2" placeholder="Cari NISN/Nama Siswa"
                            value="{{ request('search') }}">
                        <select name="tahun" class="form-control mr-2">
                            <option value="">Semua Tahun</option>
                            @foreach ($tahunAjaran as $tahunOpt)
                                <option value="{{ $tahunOpt }}" {{ request('tahun') == $tahunOpt ? 'selected' : '' }}>
                                    {{ $tahunOpt }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary mr-2">Cari</button>
                        @if (request()->has('search') || request()->has('tahun'))
                            <a href="{{ route('uang-tahunan.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">Data Uang Kegiatan Tahunan</div>

                    <div class="table-responsive mb-3">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>NISN</th>
                                    <th>NAMA SISWA</th>
                                    <th>TAHUN</th>
                                    <th>DEBIT</th>
                                    <th>KREDIT</th>
                                    <th>SALDO</th>
                                    <th>KETERANGAN</th>
                                    <th>TANGGAL</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($uangTahunan as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->siswa->nisn }}</td>
                                        <td>{{ $item->siswa->nama }}</td>
                                        <td>{{ $item->tahun_ajaran }}</td>
                                        <td class="text-success">Rp {{ number_format($item->debit, 0, ',', '.') }}</td>
                                        <td class="text-danger">Rp {{ number_format($item->kredit, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('uang-tahunan.show', ['id' => $item->id_siswa, 'tahun' => $item->tahun_ajaran]) }}"
                                                class="btn btn-sm btn-info" title="Detail">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('uang-tahunan.edit', $item->id) }}"
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            @if (auth()->user()->level == 'admin')
                                                <form method="post"
                                                    action="{{ route('uang-tahunan.destroy', $item->id) }}"
                                                    id="delete{{ $item->id }}" style="display: inline">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="deleteData({{ $item->id }})" title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($uangTahunan->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $uangTahunan->appends(request()->query())->previousPageUrl() }}"
                                class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $uangTahunan->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $uangTahunan->currentPage() ? 'active' : '' }}"
                                    href="{{ $uangTahunan->appends(request()->query())->url($i) }}">
                                    {{ $i }}
                                </a>
                            @endfor
                            <a href="{{ $uangTahunan->appends(request()->query())->nextPageUrl() }}"
                                class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                    <!-- End Pagination -->

                    @if (count($uangTahunan) == 0)
                        <div class="text-center">Tidak ada data uang kegiatan tahunan!</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('sweet')
    function deleteData(id) {
    Swal.fire({
    title: 'PERINGATAN!',
    text: "Yakin ingin menghapus transaksi uang kegiatan tahunan?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yakin',
    cancelButtonText: 'Batal',
    }).then((result) => {
    if (result.value) {
    $('#delete' + id).submit();
    }
    })
    }
@endsection
