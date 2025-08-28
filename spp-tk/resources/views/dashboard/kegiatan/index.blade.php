@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Daftar Kegiatan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Form Tambah -->
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Tambah Kegiatan</div>

                    <form method="post" action="{{ route('kegiatan.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="nama_kegiatan_input">Nama Kegiatan</label>
                            <input type="text" id="nama_kegiatan_input" name="nama_kegiatan" 
                                class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                                value="{{ old('nama_kegiatan') }}">
                            @error('nama_kegiatan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="biaya_input">Biaya</label>
                            <input type="number" id="biaya_input" name="biaya" 
                                class="form-control @error('biaya') is-invalid @enderror" 
                                value="{{ old('biaya') }}">
                            @error('biaya')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tahun_input">Tahun</label>
                            <input type="number" id="tahun_input" name="tahun"
                                class="form-control @error('tahun') is-invalid @enderror"
                                value="{{ old('tahun') }}">
                            @error('tahun')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success btn-rounded">
                            <i class="mdi mdi-check"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabel -->
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Data Kegiatan</div>
                    <div class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama Kegiatan</th>
                                    <th scope="col">Biaya</th>
                                    <th scope="col">Tahun</th>
                                    <th scope="col">Dibuat</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @foreach ($kegiatan as $value)
                                    <tr>
                                        <th scope="row">{{ $i }}</th>
                                        <td>{{ $value->nama_kegiatan }}</td>
                                        <td>Rp {{ number_format($value->biaya, 0, ',', '.') }}</td>
                                        <td>{{ $value->tahun }}</td>
                                        <td>{{ $value->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <div class="hide-menu">
                                                <a href="javascript:void(0)" class="text-dark" id="actiondd" role="button"
                                                    data-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actiondd">
                                                    <a class="dropdown-item"
                                                        href="{{ route('kegiatan.edit', $value->id) }}">
                                                        <i class="ti-pencil"></i> Edit 
                                                    </a>
                                                    <form method="post"
                                                        action="{{ route('kegiatan.destroy', $value->id) }}"
                                                        id="delete{{ $value->id }}">
                                                        @csrf
                                                        @method('delete')

                                                        <button type="button" class="dropdown-item"
                                                            onclick="deleteData({{ $value->id }})">
                                                            <i class="ti-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php $i++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($kegiatan->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $kegiatan->previousPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $kegiatan->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $kegiatan->currentPage() ? 'active' : '' }}"
                                    href="{{ $kegiatan->url($i) }}">{{ $i }}</a>
                            @endfor
                            <a href="{{ $kegiatan->nextPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                    <!-- End Pagination -->

                    @if (count($kegiatan) == 0)
                        <div class="text-center"> Tidak ada data!</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('sweet')
    function deleteData(id){
        Swal.fire({
            title: 'PERINGATAN!',
            text: "Yakin ingin menghapus data kegiatan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yakin',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.value) {
                $('#delete'+id).submit();
            }
        })
    }
@endsection