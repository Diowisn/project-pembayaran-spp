@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Kegiatan Tahunan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Card untuk Form Tambah Kegiatan -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-title">Tambah Kegiatan Baru</div>
                    
                    <form method="post" action="{{ route('data-kegiatan-tahunan.store') }}">
                        @csrf
                        
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label>Nama Kegiatan</label>
                                <input type="text" class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                                       name="nama_kegiatan" value="{{ old('nama_kegiatan') }}" required>
                                <span class="text-danger">@error('nama_kegiatan') {{ $message }} @enderror</span>
                            </div>
                            
                            <div class="form-group col-md-3">
                                <label>Nominal</label>
                                <input type="number" class="form-control @error('nominal') is-invalid @enderror" 
                                       name="nominal" value="{{ old('nominal') }}" required>
                                <span class="text-danger">@error('nominal') {{ $message }} @enderror</span>
                            </div>

                            <div class="form-group col-md-3">
                                <label>Keterangan</label>
                                <input type="text" class="form-control @error('keterangan') is-invalid @enderror" 
                                       name="keterangan" value="{{ old('keterangan') }}">
                                <span class="text-danger">@error('keterangan') {{ $message }} @enderror</span>
                            </div>

                            <div class="form-group col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="mdi mdi-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card untuk Daftar Kegiatan -->
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Daftar Kegiatan Tahunan</div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kegiatan as $item)
                                    <tr>
                                        <td>{{ $loop->iteration + ($kegiatan->currentPage() - 1) * $kegiatan->perPage() }}</td>
                                        <td>{{ $item->nama_kegiatan }}</td>
                                        <td>Rp. {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                        <td>{{ $item->keterangan ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('data-kegiatan-tahunan.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('data-kegiatan-tahunan.destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data kegiatan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($kegiatan->lastPage() != 1)
                        <div class="btn-group float-right mt-3">
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
                </div>
            </div>
        </div>
    </div>
@endsection