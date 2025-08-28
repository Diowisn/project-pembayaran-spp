@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Entri Kegiatan Siswa</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Entri Kegiatan Siswa</div>

                    <form method="GET" action="{{ route('entri-kegiatan.cari-siswa') }}">
                        <div class="form-group">
                            <label for="nisn">Cari Berdasarkan NISN</label>
                            <div class="input-group">
                                <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN"
                                    value="{{ old('nisn', $siswa->nisn ?? '') }}" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Cari</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if (isset($siswa))
                        <div class="mt-4">
                            <h5 class="mb-3">Data Siswa</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama</label>
                                        <input type="text" class="form-control" value="{{ $siswa->nama ?? '' }}"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>NISN</label>
                                        <input type="text" class="form-control" value="{{ $siswa->nisn ?? '' }}"
                                            disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Total Biaya Kegiatan</label>
                                <input type="text" class="form-control"
                                    value="Rp {{ number_format($total_biaya ?? 0, 0, ',', '.') }}" disabled>
                            </div>
                        </div>

                        @if (!empty($kegiatan_dengan_status))
                            <h5 class="mb-3 mt-4">Pilihan Kegiatan</h5>
                            <form method="post" action="{{ route('entri-kegiatan.update-partisipasi', $siswa->id) }}">
                                @csrf
                                <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Nama Kegiatan</th>
                                                <th class="text-center">Biaya</th>
                                                <th class="text-center">Tahun</th>
                                                <th class="text-center">Ikut?</th>
                                                <th class="text-center">Status Bayar</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($kegiatan_dengan_status as $kegiatan)
                                                <tr>
                                                    <td>{{ $kegiatan['nama'] }}</td>
                                                    <td class="text-right">Rp
                                                        {{ number_format($kegiatan['biaya'], 0, ',', '.') }}</td>
                                                    <td class="text-center">{{ $kegiatan['tahun'] }}</td>
                                                    <td class="text-center">
                                                        <select name="kegiatan[{{ $kegiatan['id'] }}]"
                                                            class="form-control form-control-sm">
                                                            <option value="1"
                                                                {{ $kegiatan['ikut'] ? 'selected' : '' }}>Ya</option>
                                                            <option value="0"
                                                                {{ !$kegiatan['ikut'] ? 'selected' : '' }}>Tidak</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge 
                                                            @if ($kegiatan['status_bayar'] == 'lunas') bg-success
                                                            @elseif($kegiatan['status_bayar'] == 'dicicil') bg-warning text-dark
                                                            @else bg-danger @endif">
                                                            {{ ucfirst($kegiatan['status_bayar']) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($kegiatan['ikut'])
                                                            <button type="button" class="btn btn-sm btn-info"
                                                                data-toggle="modal"
                                                                data-target="#statusBayarModal{{ $kegiatan['id'] }}">
                                                                <i class="mdi mdi-cash"></i> Ubah Status
                                                            </button>

                                                            <!-- Modal untuk Ubah Status Bayar -->
                                                            <div class="modal fade"
                                                                id="statusBayarModal{{ $kegiatan['id'] }}" tabindex="-1"
                                                                role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Ubah Status Bayar -
                                                                                {{ $kegiatan['nama'] }}</h5>
                                                                            <button type="button" class="close"
                                                                                data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <form method="post"
                                                                            action="{{ route('entri-kegiatan.update-status-bayar', ['id' => $siswa->kegiatanSiswa->firstWhere('kegiatan_id', $kegiatan['id'])->id ?? 0]) }}">
                                                                            @csrf
                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label>Status Bayar</label>
                                                                                    <select name="status_bayar"
                                                                                        class="form-control" required>
                                                                                        <option value="belum"
                                                                                            {{ $kegiatan['status_bayar'] == 'belum' ? 'selected' : '' }}>
                                                                                            Belum Bayar</option>
                                                                                        <option value="dicicil"
                                                                                            {{ $kegiatan['status_bayar'] == 'dicicil' ? 'selected' : '' }}>
                                                                                            Dicicil</option>
                                                                                        <option value="lunas"
                                                                                            {{ $kegiatan['status_bayar'] == 'lunas' ? 'selected' : '' }}>
                                                                                            Lunas</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                    class="btn btn-secondary"
                                                                                    data-dismiss="modal">Batal</button>
                                                                                <button type="submit"
                                                                                    class="btn btn-primary">Simpan</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <button type="submit" class="btn btn-success btn-rounded float-right">
                                    <i class="mdi mdi-check"></i> Simpan Pilihan
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning mt-3">
                                <i class="mdi mdi-alert-circle"></i> Tidak ada kegiatan tersedia.
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <!-- Form Pencarian -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('entri-kegiatan.index') }}" class="form-inline">
                        <input type="text" name="search" class="form-control mr-2"
                            placeholder="Cari NISN / Nama Siswa" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary mr-2">Cari</button>

                        @if (request()->has('search'))
                            <a href="{{ route('entri-kegiatan.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">Data Partisipasi Kegiatan Siswa</div>

                    <div class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">NISN</th>
                                    <th scope="col">NAMA SISWA</th>
                                    <th scope="col">KEGIATAN</th>
                                    <th scope="col">BIAYA</th>
                                    <th scope="col">PARTISIPASI</th>
                                    <th scope="col">STATUS BAYAR</th>
                                    <th scope="col">TANGGAL UPDATE</th>
                                    <th scope="col">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = ($kegiatanSiswa->currentPage() - 1) * $kegiatanSiswa->perPage() + 1;
                                @endphp
                                @foreach ($kegiatanSiswa as $item)
                                    <tr>
                                        <th scope="row">{{ $i }}</th>
                                        <td>{{ $item->siswa->nisn }}</td>
                                        <td>{{ $item->siswa->nama }}</td>
                                        <td>{{ $item->kegiatan->nama_kegiatan }}</td>
                                        <td>Rp {{ number_format($item->kegiatan->biaya, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if ($item->ikut)
                                                <span class="badge bg-success">Ikut</span>
                                            @else
                                                <span class="badge bg-danger">Tidak</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge 
                                                @if ($item->status_bayar == 'lunas') bg-success
                                                @elseif($item->status_bayar == 'dicicil') bg-warning text-dark
                                                @else bg-danger @endif">
                                                {{ ucfirst($item->status_bayar ?? 'belum') }}
                                            </span>
                                        </td>
                                        <td>{{ $item->updated_at->format('d M, Y') }}</td>
                                        <td>
                                            <div class="hide-menu">
                                                <a href="javascript:void(0)" class="text-dark" id="actiondd"
                                                    role="button" data-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actiondd">
                                                    <a class="dropdown-item"
                                                        href="{{ route('entri-kegiatan.edit', $item->id) }}">
                                                        <i class="ti-pencil"></i> Edit
                                                    </a>

                                                    @if (auth()->user()->level == 'admin')
                                                        <form method="post"
                                                            action="{{ route('entri-kegiatan.destroy', $item->id) }}"
                                                            id="delete{{ $item->id }}">
                                                            @csrf
                                                            @method('delete')

                                                            <button type="button" class="dropdown-item"
                                                                onclick="deleteData({{ $item->id }})">
                                                                <i class="ti-trash"></i> Hapus
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($kegiatanSiswa->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $kegiatanSiswa->appends(request()->query())->previousPageUrl() }}"
                                class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $kegiatanSiswa->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $kegiatanSiswa->currentPage() ? 'active' : '' }}"
                                    href="{{ $kegiatanSiswa->appends(request()->query())->url($i) }}">
                                    {{ $i }}
                                </a>
                            @endfor
                            <a href="{{ $kegiatanSiswa->appends(request()->query())->nextPageUrl() }}"
                                class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                    <!-- End Pagination -->

                    @if (count($kegiatanSiswa) == 0)
                        <div class="text-center">Tidak ada data partisipasi kegiatan!</div>
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
    text: "Yakin ingin menghapus data partisipasi kegiatan?",
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
