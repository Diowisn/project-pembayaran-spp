@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('data-kegiatan-tahunan.index') }}">Kegiatan Tahunan</a></li>
    <li class="breadcrumb-item active">Tambah Kegiatan ke Paket: {{ $paket }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Tambah Kegiatan ke Paket: {{ $paket }}</div>
                    
                    <form method="post" action="{{ route('data-kegiatan-tahunan.store') }}">
                        @csrf
                        <input type="hidden" name="nama_paket" value="{{ $paket }}">
                        
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label>Nama Kegiatan</label>
                                <input type="text" class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                                       name="nama_kegiatan" value="{{ old('nama_kegiatan') }}" required>
                                <span class="text-danger">@error('nama_kegiatan') {{ $message }} @enderror</span>
                            </div>
                            
                            <div class="form-group col-md-3">
                                <label>Nominal</label>
                                <input type="number" class="form-control @error('nominal') is-invalid @enderror" 
                                       name="nominal" value="{{ old('nominal') }}" required>
                                <span class="text-danger">@error('nominal') {{ $message }} @enderror</span>
                            </div>

                            <div class="form-group col-md-3">
                                <label>Keterangan</label>
                                <input type="text" class="form-control @error('keterangan') is-invalid @enderror" 
                                       name="keterangan" value="{{ old('keterangan') }}">
                                <span class="text-danger">@error('keterangan') {{ $message }} @enderror</span>
                            </div>

                            <div class="form-group col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="mdi mdi-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="mt-3">
                        <a href="{{ route('data-kegiatan-tahunan.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali ke Daftar Paket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection