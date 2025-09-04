@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item"><a href="{{ route('entri-kegiatan.index') }}">Entri Kegiatan</a></li>
    <li class="breadcrumb-item active">Edit Pembayaran</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Edit Pembayaran Kegiatan</div>

                    <!-- Tampilkan error messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('entri-kegiatan.update', $edit->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informasi Siswa -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5>Informasi Siswa</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>NISN:</strong> {{ $edit->siswa->nisn }}
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Nama:</strong> {{ $edit->siswa->nama }}
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Kelas:</strong> {{ $edit->siswa->kelas->nama_kelas ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kegiatan -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5>Informasi Kegiatan</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Nama Kegiatan:</strong> {{ $edit->kegiatan->nama_kegiatan }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Nominal Kegiatan:</strong> 
                                                Rp {{ number_format($edit->kegiatan->nominal, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Edit Pembayaran -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="angsuran_ke">Angsuran Ke</label>
                                    <input type="number" class="form-control @error('angsuran_ke') is-invalid @enderror" 
                                        id="angsuran_ke" name="angsuran_ke" 
                                        value="{{ old('angsuran_ke', $edit->angsuran_ke) }}" min="1" required>
                                    @error('angsuran_ke')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tgl_bayar">Tanggal Bayar</label>
                                    <input type="date" class="form-control @error('tgl_bayar') is-invalid @enderror" 
                                        id="tgl_bayar" name="tgl_bayar" 
                                        value="{{ old('tgl_bayar', $edit->tgl_bayar ? $edit->tgl_bayar->format('Y-m-d') : date('Y-m-d')) }}" required>
                                    @error('tgl_bayar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jumlah_bayar">Jumlah Bayar</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" class="form-control @error('jumlah_bayar') is-invalid @enderror" 
                                            id="jumlah_bayar" name="jumlah_bayar" 
                                            value="{{ old('jumlah_bayar', $edit->jumlah_bayar) }}" min="1" required
                                            oninput="hitungKembalian()">
                                    </div>
                                    @error('jumlah_bayar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Input hidden untuk jumlah_tagihan -->
                        <input type="hidden" id="jumlah_tagihan" value="{{ $edit->kegiatan->nominal }}">

                        <!-- Informasi Kembalian -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card" id="info_pembayaran">
                                    <div class="card-body">
                                        <h6>Informasi Pembayaran</h6>
                                        <div id="status_pembayaran" class="alert alert-info p-2 mb-2">
                                            <span id="status_text">
                                                @php
                                                    $kekurangan = max(0, $edit->kegiatan->nominal - $edit->jumlah_bayar);
                                                    $kembalian = max(0, $edit->jumlah_bayar - $edit->kegiatan->nominal);
                                                @endphp
                                                @if($kekurangan > 0)
                                                    Pembayaran kurang: Rp {{ number_format($kekurangan, 0, ',', '.') }}
                                                @elseif($kembalian > 0)
                                                    Pembayaran lebih: Rp {{ number_format($kembalian, 0, ',', '.') }} akan masuk ke tabungan
                                                @else
                                                    Pembayaran sesuai tagihan
                                                @endif
                                            </span>
                                        </div>
                                        <div id="kembalian_info" class="{{ $kembalian > 0 ? '' : 'd-none' }}">
                                            <strong>Kembalian:</strong> 
                                            <span id="jumlah_kembalian" class="text-success">
                                                Rp {{ number_format($kembalian, 0, ',', '.') }}
                                            </span>
                                            <small class="form-text text-muted">
                                                Kembalian akan ditambahkan ke tabungan siswa
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="submit-button">
                                <i class="mdi mdi-content-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('entri-kegiatan.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function hitungKembalian() {
        const jumlahTagihan = parseFloat(document.getElementById('jumlah_tagihan').value);
        const jumlahBayar = parseFloat(document.getElementById('jumlah_bayar').value) || 0;
        
        const kembalian = Math.max(0, jumlahBayar - jumlahTagihan);
        const kekurangan = Math.max(0, jumlahTagihan - jumlahBayar);
        
        // Update tampilan kembalian
        const kembalianElement = document.getElementById('jumlah_kembalian');
        const containerKembalian = document.getElementById('kembalian_info');
        
        kembalianElement.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');
        
        // Update status pembayaran
        const statusElement = document.getElementById('status_text');
        const statusAlert = document.getElementById('status_pembayaran');
        
        if (kembalian > 0) {
            statusElement.textContent = 'Pembayaran lebih: Rp ' + kembalian.toLocaleString('id-ID') + ' akan masuk ke tabungan';
            statusAlert.className = 'alert alert-success p-2 mb-2';
            containerKembalian.classList.remove('d-none');
        } else if (kekurangan > 0) {
            statusElement.textContent = 'Pembayaran kurang: Rp ' + kekurangan.toLocaleString('id-ID');
            statusAlert.className = 'alert alert-warning p-2 mb-2';
            containerKembalian.classList.add('d-none');
        } else {
            statusElement.textContent = 'Pembayaran sesuai tagihan';
            statusAlert.className = 'alert alert-info p-2 mb-2';
            containerKembalian.classList.add('d-none');
        }
        
        // Validasi tombol submit
        const submitButton = document.getElementById('submit-button');
        if (jumlahBayar < 1) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="mdi mdi-block-helper"></i> Jumlah tidak valid';
        } else {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="mdi mdi-content-save"></i> Simpan Perubahan';
        }
    }
    
    // Inisialisasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        hitungKembalian();
        
        // Tambahkan event listener untuk form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitButton = document.getElementById('submit-button');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...';
        });
    });
</script>
@endsection