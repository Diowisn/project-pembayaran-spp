@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Infaq Gedung</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Entri Pembayaran Infaq Gedung</div>

                    <form method="GET" action="{{ route('infaq.cari-siswa') }}">
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
                        <form method="post" action="{{ route('infaq.store') }}">
                            @csrf
                            <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                            <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">

                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" class="form-control" value="{{ $siswa->nama ?? '' }}" disabled>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Paket Infaq Gedung</label>
                                        <input type="text" class="form-control"
                                            value="{{ $siswa->infaqGedung->paket ?? 'Tidak ada paket' }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total Infaq Gedung</label>
                                        <input type="text" class="form-control"
                                            value="{{ isset($siswa->infaqGedung) ? 'Rp ' . number_format($siswa->infaqGedung->nominal, 0, ',', '.') : 'Rp 0' }}"
                                            disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total Sudah Dibayar</label>
                                        <input type="text" class="form-control"
                                            value="Rp {{ number_format($total_dibayar ?? 0, 0, ',', '.') }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Sisa Pembayaran</label>
                                        <input type="text" class="form-control"
                                            value="Rp {{ number_format($sisa_pembayaran ?? 0, 0, ',', '.') }}" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <input type="text" class="form-control {{ $sisa_pembayaran <= 0 ? 'text-success' : 'text-warning' }}"
                                    value="{{ $sisa_pembayaran <= 0 ? 'LUNAS' : 'BELUM LUNAS' }}" disabled>
                            </div>

                            <div class="form-group">
                                <label>Jumlah Bayar</label>
                                <input type="number" class="form-control @error('jumlah_bayar') is-invalid @enderror"
                                    name="jumlah_bayar" value="{{ old('jumlah_bayar') }}" required min="1"
                                    max="{{ $sisa_pembayaran ?? 0 }}">
                                <span class="text-danger">
                                    @error('jumlah_bayar')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Bayar</label>
                                <input type="date" class="form-control @error('tgl_bayar') is-invalid @enderror"
                                    name="tgl_bayar" value="{{ old('tgl_bayar', date('Y-m-d')) }}" required>
                                <span class="text-danger">
                                    @error('tgl_bayar')
                                        {{ $message }}
                                    @enderror
                                </span>
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
                    <form method="GET" action="{{ route('infaq.index') }}" class="form-inline">
                        <input type="text" name="search" class="form-control mr-2" placeholder="Cari NISN / Nama Siswa"
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary mr-2">Cari</button>

                        @if (request()->has('search'))
                            <a href="{{ route('infaq.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">Data Pembayaran Infaq</div>

                    <div class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">NISN SISWA</th>
                                    <th scope="col">NAMA SISWA</th>
                                    <th scope="col">PAKET</th>
                                    <th scope="col">ANGSURAN KE</th>
                                    <th scope="col">
                                        <a href="{{ route('infaq.index', [
                                            'search' => request('search'),
                                            'sort_by' => 'jumlah_bayar',
                                            'order' => request('sort_by') == 'jumlah_bayar' && request('order') == 'asc' ? 'desc' : 'asc',
                                        ]) }}"
                                            class="text-dark">
                                            JUMLAH BAYAR
                                            @if (request('sort_by') == 'jumlah_bayar')
                                                <i class="mdi mdi-chevron-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col">STATUS</th>
                                    <th scope="col">
                                        <a href="{{ route('infaq.index', [
                                            'search' => request('search'),
                                            'sort_by' => 'tgl_bayar',
                                            'order' => request('sort_by') == 'tgl_bayar' && request('order') == 'asc' ? 'desc' : 'asc',
                                        ]) }}"
                                            class="text-dark">
                                            TANGGAL BAYAR
                                            @if (request('sort_by') == 'tgl_bayar')
                                                <i class="mdi mdi-chevron-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = ($angsuran->currentPage() - 1) * $angsuran->perPage() + 1;
                                @endphp
                                @foreach ($angsuran as $value)
                                    <tr>
                                        <th scope="row">{{ $i }}</th>
                                        <td>{{ $value->siswa->nisn }}</td>
                                        <td>{{ $value->siswa->nama }}</td>
                                        <td>{{ $value->infaqGedung->paket ?? '-' }}</td>
                                        <td>{{ $value->angsuran_ke }}</td>
                                        <td>Rp {{ number_format($value->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($value->is_lunas)
                                                <span class="badge bg-success">Lunas</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Belum Lunas</span>
                                            @endif
                                        </td>
                                        <td>{{ $value->tgl_bayar->format('d M, Y') }}</td>
                                        <td>
                                            <div class="hide-menu">
                                                <a href="javascript:void(0)" class="text-dark" id="actiondd"
                                                    role="button" data-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actiondd">
                                                    {{-- <a class="dropdown-item"
                                                        href="{{ route('infaq.generate', $value->id) }}">
                                                        <i class="ti-printer"></i> Cetak
                                                    </a> --}}
                                                    <a class="dropdown-item"
                                                        href="{{ url('dashboard/infaq/' . $value->id . '/edit') }}"><i
                                                            class="ti-pencil"></i> Edit
                                                    </a>

                                                    @if (auth()->user()->level == 'admin')
                                                        <form method="post"
                                                            action="{{ route('infaq.destroy', $value->id) }}"
                                                            id="delete{{ $value->id }}">
                                                            @csrf
                                                            @method('delete')

                                                            <button type="button" class="dropdown-item"
                                                                onclick="deleteData({{ $value->id }})">
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
                    @if ($angsuran->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $angsuran->appends(request()->query())->previousPageUrl() }}"
                                class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $angsuran->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $angsuran->currentPage() ? 'active' : '' }}"
                                    href="{{ $angsuran->appends(request()->query())->url($i) }}">
                                    {{ $i }}
                                </a>
                            @endfor
                            <a href="{{ $angsuran->appends(request()->query())->nextPageUrl() }}"
                                class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                    <!-- End Pagination -->

                    @if (count($angsuran) == 0)
                        <div class="text-center">Tidak ada data pembayaran infaq!</div>
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
    text: "Yakin ingin menghapus data pembayaran infaq?",
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
