@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item"><a href="{{ route('kegiatan.index') }}">Kegiatan</a></li>
    <li class="breadcrumb-item active">Edit Kegiatan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Edit Kegiatan</div>

                    <form method="post" action="{{ route('kegiatan.update', $kegiatan->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="nama_kegiatan_input">Nama Kegiatan</label>
                            <input type="text" id="nama_kegiatan_input" name="nama_kegiatan" 
                                class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                                value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}">
                            @error('nama_kegiatan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="biaya_input">Biaya</label>
                            <input type="number" id="biaya_input" name="biaya" 
                                class="form-control @error('biaya') is-invalid @enderror" 
                                value="{{ old('biaya', $kegiatan->biaya) }}">
                            @error('biaya')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tahun_input">Tahun</label>
                            <input type="number" id="tahun_input" name="tahun"
                                class="form-control @error('tahun') is-invalid @enderror"
                                value="{{ old('tahun', $kegiatan->tahun) }}">
                            @error('tahun')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success btn-rounded float-right">
                            <i class="mdi mdi-check"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('kegiatan.index') }}" class="btn btn-light btn-rounded float-right mr-2">
                            <i class="mdi mdi-close"></i> Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection