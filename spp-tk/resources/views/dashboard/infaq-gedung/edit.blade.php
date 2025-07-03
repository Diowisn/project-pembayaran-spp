@extends('layouts.dashboard')

@section('breadcrumb')
	<li class="breadcrumb-item">Dashboard</li>
	<li class="breadcrumb-item">Infaq Gedung</li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
   <div class="row">
         <div class="col-md-12">
              <div class="card">
                  <div class="card-body">
                       <div class="card-title">{{ __('Edit Infaq Gedung') }}</div>
                     
                        <form method="post" action="{{ route('infaq-gedung.update', $infaq->id) }}">
                           @csrf
                           @method('PUT')
                           
                           <div class="form-group">
                              <label>Paket</label>
                              <input type="text" class="form-control @error('paket') is-invalid @enderror" name="paket" value="{{ old('paket', $infaq->paket) }}" maxlength="1">
                              <span class="text-danger">@error('paket') {{ $message }} @enderror</span>
                           </div>
                           
                           <div class="form-group">
                              <label>Nominal</label>
                              <input type="number" class="form-control @error('nominal') is-invalid @enderror" name="nominal" value="{{ old('nominal', $infaq->nominal) }}">
                              <span class="text-danger">@error('nominal') {{ $message }} @enderror</span>
                           </div>

                           <div class="form-group">
                              <label>Jumlah Angsuran</label>
                              <input type="number" class="form-control @error('jumlah_angsuran') is-invalid @enderror" name="jumlah_angsuran" value="{{ old('jumlah_angsuran', $infaq->jumlah_angsuran) }}">
                              <span class="text-danger">@error('jumlah_angsuran') {{ $message }} @enderror</span>
                           </div>

                           <div class="form-group">
                              <label>Nominal per Angsuran</label>
                              <input type="number" class="form-control @error('nominal_per_angsuran') is-invalid @enderror" name="nominal_per_angsuran" value="{{ old('nominal_per_angsuran', $infaq->nominal_per_angsuran) }}">
                              <span class="text-danger">@error('nominal_per_angsuran') {{ $message }} @enderror</span>
                           </div>
                           
                           <a href="{{ route('infaq-gedung.index') }}" class="btn btn-primary btn-rounded">
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