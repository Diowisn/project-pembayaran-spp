@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Uang Kegiatan Tahunan</li>
    <li class="breadcrumb-item active">Edit Transaksi</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Edit Transaksi Uang Kegiatan Tahunan</div>

                    <form method="POST" action="{{ route('uang-tahunan.update', $uangTahunan->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Info Siswa -->
                        <div class="form-group">
                            <label>Nama Siswa</label>
                            <input type="text" class="form-control" value="{{ $uangTahunan->siswa->nama }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>NISN</label>
                            <input type="text" class="form-control" value="{{ $uangTahunan->siswa->nisn }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Kelas</label>
                            <input type="text" class="form-control"
                                value="{{ $uangTahunan->siswa->kelas->nama_kelas ?? '-' }}" readonly>
                        </div>

                        <input type="hidden" name="id_siswa" value="{{ $uangTahunan->id_siswa }}">

                        <div class="form-group">
                            <label>Tahun Ajaran</label>
                            <input type="text" class="form-control" value="{{ $uangTahunan->tahun_ajaran }}" readonly>
                            <small class="text-muted">Tahun ajaran tidak dapat diubah</small>
                        </div>

                        <div class="form-group">
                            <label for="tipe">Tipe Transaksi</label>
                            <select class="form-control @error('tipe') is-invalid @enderror" id="tipe" name="tipe"
                                required>
                                <option value="debit" {{ $uangTahunan->debit > 0 ? 'selected' : '' }}>Setoran (Debit)
                                </option>
                                <option value="kredit" {{ $uangTahunan->kredit > 0 ? 'selected' : '' }}>Penarikan (Kredit)
                                </option>
                            </select>
                            @error('tipe')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah"
                                name="jumlah"
                                value="{{ old('jumlah', $uangTahunan->debit > 0 ? $uangTahunan->debit : $uangTahunan->kredit) }}"
                                min="1" required>
                            @error('jumlah')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" class="form-control @error('keterangan') is-invalid @enderror"
                                id="keterangan" name="keterangan" value="{{ old('keterangan', $uangTahunan->keterangan) }}"
                                required>
                            @error('keterangan')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('uang-tahunan.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
