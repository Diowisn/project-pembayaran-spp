@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tabungan.index') }}">Tabungan</a></li>
    <li class="breadcrumb-item active">Tambah Setoran Manual</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Form Setoran Tabungan Manual</div>

                    <form method="POST" action="{{ route('tabungan.store-manual') }}">
                        @csrf

                        <div class="form-group">
                            <label for="id_siswa">Pilih Siswa</label>
                            <select class="form-control @error('id_siswa') is-invalid @enderror" 
                                    name="id_siswa" id="id_siswa" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($siswaList as $siswa)
                                    <option value="{{ $siswa->id }}" 
                                        {{ old('id_siswa') == $siswa->id ? 'selected' : '' }}>
                                        {{ $siswa->nama }} (NISN: {{ $siswa->nisn }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_siswa')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="jumlah">Jumlah Setoran</label>
                            <input type="number" 
                                   class="form-control @error('jumlah') is-invalid @enderror" 
                                   name="jumlah" id="jumlah" 
                                   value="{{ old('jumlah') }}" 
                                   min="1" required>
                            @error('jumlah')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" 
                                   class="form-control @error('keterangan') is-invalid @enderror" 
                                   name="keterangan" id="keterangan" 
                                   value="{{ old('keterangan') }}" required>
                            @error('keterangan')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> Simpan Setoran
                        </button>
                        <a href="{{ route('tabungan.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection