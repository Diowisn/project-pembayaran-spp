@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Kelas</li>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('Tambah Kelas') }}</div>

                    <form method="post" action="{{ url('/dashboard/data-kelas') }}">
                        @csrf

                        <div class="form-group">
                            <label>Nama Kelas</label>
                            <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror"
                                name="nama_kelas" value="{{ old('nama_kelas') }}">
                            <span class="text-danger">
                                @error('nama_kelas')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="has_konsumsi" id="has_konsumsi"
                                    value="1" {{ old('has_konsumsi') ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_konsumsi">Menyediakan Konsumsi</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="has_fullday" id="has_fullday"
                                    value="1" {{ old('has_fullday') ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_fullday">Program Fullday</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-rounded">
                            <i class="mdi mdi-check"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Data SPP</div>

                    <div class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">KELAS</th>
                                    <th scope="col">KONSUMSI</th>
                                    <th scope="col">FULLDAY</th>
                                    <th scope="col">DIBUAT</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach ($kelas as $value)
                                    <tr>
                                        <th scope="row">{{ $i }}</th>
                                        <td>{{ $value->nama_kelas }}</td>
                                        <td>{{ $value->has_konsumsi ? 'Ya' : 'Tidak' }}</td>
                                        <td>{{ $value->has_fullday ? 'Ya' : 'Tidak' }}</td>
                                        <td>{{ $value->created_at->format('d M, Y') }}</td>

                                        <td>
                                            <div class="hide-menu">
                                                <a href="javascript:void(0)" class="text-dark" id="actiondd" role="button"
                                                    data-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actiondd">
                                                    <a class="dropdown-item"
                                                        href="{{ url('dashboard/data-kelas/' . $value->id . '/edit') }}"><i
                                                            class="ti-pencil"></i> Edit </a>
                                                    <form method="post"
                                                        action="{{ url('dashboard/data-kelas', $value->id) }}"
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
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($kelas->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $kelas->previousPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $kelas->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $kelas->currentPage() ? 'active' : '' }}"
                                    href="{{ $kelas->url($i) }}">{{ $i }}</a>
                            @endfor
                            <a href="{{ $kelas->nextPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                    <!-- End Pagination -->

                    @if (count($kelas) == 0)
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
    text: "Yakin ingin menghapus data kelas?",
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
