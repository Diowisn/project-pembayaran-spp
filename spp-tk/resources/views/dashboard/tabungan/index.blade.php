@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Tabungan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Form Pencarian -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('tabungan.index') }}" class="form-inline">
                        <input type="text" name="search" class="form-control mr-2" placeholder="Cari NISN/Nama Siswa"
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary mr-2">Cari</button>
                        @if (request()->has('search'))
                            <a href="{{ route('tabungan.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="card-title mb-0">Data Tabungan Siswa</div>
                        <a href="{{ route('tabungan.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> Tambah Setoran Manual
                        </a>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>NISN</th>
                                    <th>NAMA SISWA</th>
                                    <th>DEBIT</th>
                                    <th>KREDIT</th>
                                    <th>SALDO</th>
                                    <th>KETERANGAN</th>
                                    <th>TANGGAL</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tabungan as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->siswa->nisn }}</td>
                                        <td>{{ $item->siswa->nama }}</td>
                                        <td class="text-success">Rp {{ number_format($item->debit, 0, ',', '.') }}</td>
                                        <td class="text-danger">Rp {{ number_format($item->kredit, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('tabungan.show', $item->id_siswa) }}"
                                                class="btn btn-sm btn-info" title="Detail Tabungan">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('tabungan.edit', $item->id) }}"
                                                class="btn btn-sm btn-warning" title="Edit Tabungan">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            @if (auth()->user()->level == 'admin')
                                                <form method="post" action="{{ route('tabungan.destroy', $item->id) }}"
                                                    id="delete{{ $item->id }}" style="display: inline">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="deleteData({{ $item->id }})" title="Hapus Tabungan">
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
                    @if ($tabungan->lastPage() != 1)
                        <div class="btn-group float-right">
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
                            <a href="{{ $tabungan->appends(request()->query())->nextPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                    <!-- End Pagination -->

                    @if (count($tabungan) == 0)
                        <div class="alert alert-warning text-center">
                            Tidak ada data tabungan ditemukan
                        </div>
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
    text: "Yakin ingin menghapus data SPP?",
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
