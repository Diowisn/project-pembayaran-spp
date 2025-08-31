@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">SPP</li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('Edit SPP') }}</div>
                    
                    <form method="post" action="{{ url('/dashboard/data-spp', $edit->id) }}">
                        @csrf
                        @method('put')
                        
                        <div class="form-group">
                            <label>Tahun</label>
                            <input type="number" class="form-control @error('tahun') is-invalid @enderror" 
                                   name="tahun" value="{{ old('tahun', $edit->tahun) }}" min="2020" max="2030" required>
                            <span class="text-danger">@error('tahun') {{ $message }} @enderror</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Kelas</label>
                            <select class="form-control @error('id_kelas') is-invalid @enderror" name="id_kelas" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('id_kelas', $edit->id_kelas) == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger">@error('id_kelas') {{ $message }} @enderror</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Nominal SPP (Rp)</label>
                            <input type="text" class="form-control @error('nominal_spp') is-invalid @enderror" 
                                   name="nominal_spp" value="{{ old('nominal_spp', number_format($edit->nominal_spp, 0, ',', '.')) }}" id="nominal-spp" required>
                            <span class="text-danger">@error('nominal_spp') {{ $message }} @enderror</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Nominal Konsumsi (Rp)</label>
                            <input type="text" class="form-control @error('nominal_konsumsi') is-invalid @enderror" 
                                   name="nominal_konsumsi" value="{{ old('nominal_konsumsi', $edit->nominal_konsumsi ? number_format($edit->nominal_konsumsi, 0, ',', '.') : '') }}" id="nominal-konsumsi">
                            <span class="text-danger">@error('nominal_konsumsi') {{ $message }} @enderror</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Nominal Fullday (Rp)</label>
                            <input type="text" class="form-control @error('nominal_fullday') is-invalid @enderror" 
                                   name="nominal_fullday" value="{{ old('nominal_fullday', $edit->nominal_fullday ? number_format($edit->nominal_fullday, 0, ',', '.') : '') }}" id="nominal-fullday">
                            <span class="text-danger">@error('nominal_fullday') {{ $message }} @enderror</span>
                        </div>

                        {{-- <div class="form-group">
                            <label>Nominal Inklusi (Rp)</label>
                            <input type="text" class="form-control @error('nominal_inklusi') is-invalid @enderror" 
                                name="nominal_inklusi" value="{{ old('nominal_inklusi', $edit->nominal_inklusi ? number_format($edit->nominal_inklusi, 0, ',', '.') : '') }}" id="nominal-inklusi">
                            <span class="text-danger">@error('nominal_inklusi') {{ $message }} @enderror</span>
                        </div> --}}
                        
                        {{-- <div class="form-group" id="infaq-container">
                            <label>Infaq Gedung (Opsional)</label>
                            <select class="form-control @error('id_infaq_gedung') is-invalid @enderror" name="id_infaq_gedung">
                                <option value="">-- Pilih Paket Infaq --</option>
                                @foreach($infaqGedung as $infaq)
                                    <option value="{{ $infaq->id }}" {{ old('id_infaq_gedung', $edit->id_infaq_gedung) == $infaq->id ? 'selected' : '' }}>
                                        Paket {{ $infaq->paket }} (Rp {{ number_format($infaq->nominal, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger">@error('id_infaq_gedung') {{ $message }} @enderror</span>
                        </div> --}}
                        
                        <a href="{{ url('dashboard/data-spp') }}" class="btn btn-primary btn-rounded">
                            <i class="mdi mdi-chevron-left"></i> Kembali
                        </a>
                        
                        <button type="submit" class="btn btn-success btn-rounded float-right">
                            <i class="mdi mdi-check"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>     
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Format input nominal
    function formatRupiahInput(inputId) {
        $(`#${inputId}`).on('keyup', function() {
            let value = $(this).val().replace(/\D/g, '');
            $(this).val(formatRupiah(value));
        });
    }

    function formatRupiah(angka) {
        if (!angka) return '';
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Format semua input nominal
    formatRupiahInput('nominal-spp');
    formatRupiahInput('nominal-konsumsi');
    formatRupiahInput('nominal-fullday');
    // formatRupiahInput('nominal-inklusi');
</script>
@endsection