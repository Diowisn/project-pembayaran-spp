@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tabungan.index') }}">Tabungan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tabungan.cari-siswa', ['nisn' => $tabungan->siswa->nisn]) }}">Detail</a></li>
    <li class="breadcrumb-item active">Edit Transaksi</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Edit Transaksi Tabungan</div>

                    <!-- Info Transaksi -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6>Informasi Transaksi</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Tanggal:</strong> {{ $tabungan->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Petugas:</strong> {{ $tabungan->petugas->name ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Siswa:</strong> {{ $tabungan->siswa->nama }} ({{ $tabungan->siswa->nisn }})
                                </div>
                                <div class="col-md-6">
                                    <strong>Kelas:</strong> {{ $tabungan->siswa->kelas->nama_kelas ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('tabungan.update', $tabungan->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Tipe Transaksi</label>
                            <select class="form-control @error('tipe') is-invalid @enderror" 
                                    name="tipe" id="tipe" required>
                                <option value="debit" {{ old('tipe', $tabungan->debit > 0 ? 'debit' : 'kredit') == 'debit' ? 'selected' : '' }}>Setoran (Debit)</option>
                                <option value="kredit" {{ old('tipe', $tabungan->debit > 0 ? 'debit' : 'kredit') == 'kredit' ? 'selected' : '' }}>Penarikan (Kredit)</option>
                            </select>
                            @error('tipe')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Jumlah</label>
                            <input type="number" 
                                   class="form-control @error('jumlah') is-invalid @enderror" 
                                   name="jumlah" id="jumlah" 
                                   value="{{ old('jumlah', $tabungan->debit > 0 ? $tabungan->debit : $tabungan->kredit) }}" 
                                   min="1" required>
                            @error('jumlah')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      name="keterangan" id="keterangan" 
                                      rows="3" required>{{ old('keterangan', $tabungan->keterangan) }}</textarea>
                            @error('keterangan')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tabungan.cari-siswa', ['nisn' => $tabungan->siswa->nisn]) }}" 
                               class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>

                    <!-- Form Hapus -->
                    <hr>
                    <div class="mt-4">
                        <h6>Hapus Transaksi</h6>
                        <p class="text-muted">Hati-hati! Menghapus transaksi tidak dapat dikembalikan.</p>
                        <form method="POST" action="{{ route('tabungan.destroy', $tabungan->id) }}" 
                              id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="mdi mdi-delete"></i> Hapus Transaksi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('sweet')
    function confirmDelete() {
        Swal.fire({
            title: 'PERINGATAN!',
            text: "Yakin ingin menghapus transaksi tabungan ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.value) {
                document.getElementById('deleteForm').submit();
            }
        })
    }
@endsection