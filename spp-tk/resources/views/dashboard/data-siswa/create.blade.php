@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Siswa</li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('Tambah Siswa') }}</div>

                    <form method="post" action="{{ url('dashboard/data-siswa') }}">
                        @csrf

                        <div class="form-group">
                            <label>NISN</label>
                            <input type="number" class="form-control @error('nisn') is-invalid @enderror" name="nisn"
                                value="{{ old('nisn') }}">
                            <span class="text-danger">
                                @error('nisn')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama"
                                value="{{ old('nama') }}">
                            <span class="text-danger">
                                @error('nama')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text">Kelas</label>
                            </div>
                            <select name="id_kelas" class="custom-select @error('id_kelas') is-invalid @enderror"
                                {{ count($kelas) == 0 ? 'disabled' : '' }}>
                                @if (count($kelas) == 0)
                                    <option>Pilihan tidak ada</option>
                                @else
                                    <option value="">Silahkan Pilih</option>
                                    @foreach ($kelas as $value)
                                        <option value="{{ $value->id }}"
                                            {{ old('id_kelas') == $value->id ? 'selected' : '' }}>
                                            {{ $value->nama_kelas }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <span class="text-danger">
                            @error('id_kelas')
                                {{ $message }}
                            @enderror
                        </span>

                        <div class="form-group">
                            <label>Nomor Telepon</label>
                            <input type="text" class="form-control @error('nomor_telp') is-invalid @enderror"
                                name="nomor_telp" value="{{ old('nomor_telp') }}">
                            <span class="text-danger">
                                @error('nomor_telp')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" rows="5" name="alamat">{{ old('alamat') }}</textarea>
                            <span class="text-danger">
                                @error('alamat')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text">SPP</label>
                            </div>
                            <select name="id_spp" class="custom-select @error('id_spp') is-invalid @enderror"
                                id="id_spp" required>
                                <option value="">Pilih SPP</option>
                                @foreach ($spp as $item)
                                    <option value="{{ $item->id }}" @if (old('id_spp') == $item->id) selected @endif>
                                        Rp {{ number_format($item->nominal_spp, 0, ',', '.') }} -
                                        Tahun {{ $item->tahun }}
                                        (Kelas: {{ $item->kelas->nama_kelas ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <span class="text-danger">
                            @error('id_spp')
                                {{ $message }}
                            @enderror
                        </span>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text">Paket Infaq Gedung</label>
                            </div>
                            <select name="id_infaq_gedung" class="custom-select">
                                <option value="">Pilih Paket</option>
                                @foreach ($infaq as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('id_infaq_gedung') == $item->id ? 'selected' : '' }}>
                                        Paket {{ $item->paket }} -
                                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        ({{ $item->jumlah_angsuran }}x @ Rp
                                        {{ number_format($item->nominal_per_angsuran, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tambahkan pilihan paket kegiatan -->
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text">Paket Kegiatan Tahunan</label>
                            </div>
                            <select name="id_paket_kegiatan" class="custom-select">
                                <option value="">Pilih Paket Kegiatan</option>
                                @foreach ($paketKegiatan as $paket)
                                    <option value="{{ $paket->id }}"
                                        {{ old('id_paket_kegiatan') == $paket->id ? 'selected' : '' }}>
                                        {{ $paket->nama_paket }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <small class="form-text text-muted mb-3">
                            Pilih paket kegiatan tahunan yang akan diikuti siswa
                        </small>

                        <div class="form-group">
                            <label>Status Inklusi</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="inklusi" name="inklusi"
                                    value="1" {{ old('inklusi') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="inklusi">
                                    Siswa Inklusi (Anak Berkebutuhan Khusus)
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Centang jika siswa termasuk anak berkebutuhan khusus
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Paket Inklusi (Opsional)</label>
                            <select name="id_inklusi" class="form-control">
                                <option value="">Pilih Paket Inklusi (Jika Siswa Inklusi)</option>
                                @foreach ($inklusi as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('id_inklusi') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_paket }} - Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Pilih paket inklusi jika siswa termasuk anak berkebutuhan khusus
                            </small>
                        </div>

                        <div class="border-top">
                            <button type="submit" class="btn btn-success btn-rounded float-right mt-3">
                                <i class="mdi mdi-check"></i> {{ __('Simpan') }}
                            </button>

                            <a href="{{ url('dashboard/data-siswa') }}" class="btn btn-primary btn-rounded mt-3">
                                <i class="mdi mdi-chevron-left"></i> {{ __('Kembali') }}
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
function toggleInklusiPackage() {
    const inklusiCheckbox = document.getElementById('inklusi');
    const inklusiPackage = document.getElementById('inklusiPackage');
    
    if (inklusiCheckbox && inklusiPackage) {
        if (inklusiCheckbox.checked) {
            inklusiPackage.style.display = 'block';
        } else {
            inklusiPackage.style.display = 'none';
            // Reset value ketika disembunyikan
            const selectElement = inklusiPackage.querySelector('select[name="id_inklusi"]');
            if (selectElement) {
                selectElement.value = '';
            }
        }
    }
}

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Panggil fungsi toggle saat halaman dimuat
    toggleInklusiPackage();
    
    // Tambahkan event listener untuk perubahan checkbox
    const checkbox = document.getElementById('inklusi');
    if (checkbox) {
        checkbox.addEventListener('change', toggleInklusiPackage);
    }
});

// Juga panggil setelah event DOMContentLoaded untuk memastikan
setTimeout(function() {
    toggleInklusiPackage();
}, 100);
</script>
@endsection