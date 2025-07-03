@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Siswa</li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('Edit Data Siswa') }}</div>
                    
                    <form method="post" action="{{ url('dashboard/data-siswa', $siswa->id) }}">
                        @csrf
                        @method('put')
                        
                        <div class="form-group">
                            <label>NISN</label>
                            <input type="number" class="form-control @error('nisn') is-invalid @enderror" name="nisn" value="{{ old('nisn', $siswa->nisn) }}">
                            <span class="text-danger">@error('nisn') {{ $message }} @enderror</span>
                        </div>
                        
                        {{-- <div class="form-group">
                            <label>NIS</label>
                            <input type="number" class="form-control @error('nis') is-invalid @enderror" name="nis" value="{{ old('nis', $siswa->nis) }}">
                            <span class="text-danger">@error('nis') {{ $message }} @enderror</span>
                        </div> --}}
                        
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama', $siswa->nama) }}">
                            <span class="text-danger">@error('nama') {{ $message }} @enderror</span>
                        </div>
                        
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text">Kelas</label>
                            </div>
                            <select name="id_kelas" class="custom-select @error('id_kelas') is-invalid @enderror" {{ count($kelas) == 0 ? 'disabled' : '' }}>
                                @if(count($kelas) == 0)
                                    <option>Pilihan tidak ada</option>
                                @else
                                    <option value="">Silahkan Pilih</option>
                                    @foreach($kelas as $value)
                                        <option value="{{ $value->id }}" {{ old('id_kelas', $siswa->id_kelas) == $value->id ? 'selected' : '' }}>
                                            {{ $value->nama_kelas }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <span class="text-danger">@error('id_kelas') {{ $message }} @enderror</span>
                        
                        <div class="form-group">
                            <label>Nomor Telepon</label>
                            <input type="text" class="form-control @error('nomor_telp') is-invalid @enderror" name="nomor_telp" value="{{ old('nomor_telp', $siswa->nomor_telp) }}">
                            <span class="text-danger">@error('nomor_telp') {{ $message }} @enderror</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" rows="5" name="alamat">{{ old('alamat', $siswa->alamat) }}</textarea>
                            <span class="text-danger">@error('alamat') {{ $message }} @enderror</span>
                        </div>
<div class="input-group mb-3">
    <div class="input-group-prepend">
        <label class="input-group-text">SPP</label>
    </div>
    <select name="id_spp" class="custom-select @error('id_spp') is-invalid @enderror" id="id_spp">
        <option value="">Pilih Kelas terlebih dahulu</option>
        @if($siswa->spp)
            <option value="{{ $siswa->spp->id }}" selected>
                Rp {{ number_format($siswa->spp->nominal_spp, 0, ',', '.') }} - Tahun {{ $siswa->spp->tahun }}
            </option>
        @endif
    </select>
</div>
<span class="text-danger">@error('id_spp') {{ $message }} @enderror</span>

<div class="input-group mb-3">
    <div class="input-group-prepend">
        <label class="input-group-text">Paket Infaq Gedung</label>
    </div>
    <select name="id_infaq_gedung" class="custom-select">
        <option value="">Pilih Paket (Opsional)</option>
        @foreach($infaq as $item)
            <option value="{{ $item->id }}" 
                {{ (old('id_infaq_gedung', $siswa->id_infaq_gedung) == $item->id ? 'selected' : '' )}}>
                Paket {{ $item->paket }} - 
                Rp {{ number_format($item->nominal, 0, ',', '.') }} 
                ({{ $item->jumlah_angsuran }}x @ Rp {{ number_format($item->nominal_per_angsuran, 0, ',', '.') }})
            </option>
        @endforeach
    </select>
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
$(document).ready(function() {
    // Fungsi untuk memuat SPP berdasarkan kelas
    function loadSpp(kelasId, selectedSpp = null) {
        if(kelasId) {
            $.get('/get-spp/' + kelasId, function(data) {
                $('#id_spp').empty().append('<option value="">Pilih SPP</option>');
                
                $.each(data, function(key, value) {
                    $('#id_spp').append('<option value="'+value.id+'">'+
                        'Rp '+value.nominal_spp.toLocaleString('id-ID')+' - Tahun '+value.tahun+
                        '</option>');
                });
                
                if(selectedSpp) {
                    $('#id_spp').val(selectedSpp);
                }
            });
        } else {
            $('#id_spp').empty().append('<option value="">Pilih Kelas terlebih dahulu</option>');
        }
    }

    // Ketika kelas dipilih
    $('#id_kelas').change(function() {
        var kelasId = $(this).val();
        loadSpp(kelasId);
    });
    
    // Untuk edit, trigger change jika kelas sudah ada
    @if(isset($siswa) && $siswa->id_kelas)
        loadSpp('{{ $siswa->id_kelas }}', '{{ $siswa->id_spp }}');
    @endif
});
</script>
@endsection