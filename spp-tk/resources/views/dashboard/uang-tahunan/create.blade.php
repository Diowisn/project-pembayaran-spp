@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Uang Kegiatan Tahunan</li>
    <li class="breadcrumb-item active">Tambah Setoran Manual</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Tambah Setoran Uang Kegiatan Tahunan Manual</div>

                    <!-- Form Pencarian Siswa -->
                    <form method="GET" action="{{ route('uang-tahunan.cari-siswa') }}" class="mb-4">
                        <div class="form-group">
                            <label for="nisn">Cari Siswa (NISN)</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('nisn') is-invalid @enderror" 
                                       id="nisn" name="nisn" value="{{ request('nisn') }}" 
                                       placeholder="Masukkan NISN siswa" required>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-magnify"></i> Cari
                                    </button>
                                </div>
                            </div>
                            @error('nisn')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </form>

                    @if(isset($siswa))
                    <!-- Form Setoran Uang Kegiatan Tahunan -->
                    <form method="POST" action="{{ route('uang-tahunan.store-manual') }}">
                        @csrf

                        <!-- Tampilkan Info Siswa -->
                        <div class="card card-body mb-3">
                            <h6>Informasi Siswa</h6>
                            <p>Nama: {{ $siswa->nama }}</p>
                            <p>NISN: {{ $siswa->nisn }}</p>
                            <p>Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}</p>
                            <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">
                        </div>

                        <!-- Form Input Setoran -->
                        <div class="form-group">
                            <label for="tahun_ajaran">Tahun Ajaran</label>
                            <input type="number" class="form-control @error('tahun_ajaran') is-invalid @enderror" 
                                   id="tahun_ajaran" name="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran) }}" 
                                   min="2000" max="2100" required>
                            @error('tahun_ajaran')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="jumlah">Jumlah Setoran</label>
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

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Simpan
                            </button>
                            <a href="{{ route('uang-tahunan.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection