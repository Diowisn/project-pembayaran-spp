@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Kegiatan Tahunan</li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
   <div class="row">
         <div class="col-md-12">
              <div class="card">
                  <div class="card-body">
                       <div class="card-title">{{ __('Tambah Kegiatan Tahunan') }}</div>
                     
                        <form method="post" action="{{ route('data-kegiatan-tahunan.store') }}">
                           @csrf
                           
                           <div class="form-group">
                              <label>Nama Kegiatan</label>
                              <input type="text" class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                                     name="nama_kegiatan" value="{{ old('nama_kegiatan') }}" required>
                              <span class="text-danger">@error('nama_kegiatan') {{ $message }} @enderror</span>
                           </div>
                           
                           <div class="form-group">
                              <label>Nominal</label>
                              <input type="number" class="form-control @error('nominal') is-invalid @enderror" 
                                     name="nominal" value="{{ old('nominal') }}" required>
                              <span class="text-danger">@error('nominal') {{ $message }} @enderror</span>
                           </div>

                           {{-- <div class="form-group">
                              <label>Status Kegiatan</label>
                              <select class="form-control @error('wajib') is-invalid @enderror" name="wajib" required>
                                  <option value="1" {{ old('wajib') == 1 ? 'selected' : '' }}>Wajib</option>
                                  <option value="0" {{ old('wajib') == 0 ? 'selected' : '' }}>Tidak Wajib</option>
                              </select>
                              <span class="text-danger">@error('wajib') {{ $message }} @enderror</span>
                           </div> --}}

                           <div class="form-group">
                              <label>Keterangan</label>
                              <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                        name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                              <span class="text-danger">@error('keterangan') {{ $message }} @enderror</span>
                           </div>
                           
                           <a href="{{ route('data-kegiatan-tahunan.index') }}" class="btn btn-primary btn-rounded">
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