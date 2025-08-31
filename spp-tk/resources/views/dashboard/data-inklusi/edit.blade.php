@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Paket Inklusi</li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Edit Paket Inklusi</div>

                    <form method="post" action="{{ route('data-inklusi.update', $inklusi->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Nama Paket</label>
                            <input type="text" name="nama_paket" maxlength="50"
                                class="form-control @error('nama_paket') is-invalid @enderror" 
                                value="{{ old('nama_paket', $inklusi->nama_paket) }}" required>
                            @error('nama_paket')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Nominal</label>
                            <input type="number" name="nominal"
                                class="form-control @error('nominal') is-invalid @enderror" 
                                value="{{ old('nominal', $inklusi->nominal) }}" required>
                            @error('nominal')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Keterangan (Opsional)</label>
                            <textarea name="keterangan"
                                class="form-control @error('keterangan') is-invalid @enderror"
                                rows="3">{{ old('keterangan', $inklusi->keterangan) }}</textarea>
                            @error('keterangan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="border-top">
                            <button type="submit" class="btn btn-success btn-rounded float-right mt-3">
                                <i class="mdi mdi-check"></i> Update
                            </button>

                            <a href="{{ route('data-inklusi.index') }}" class="btn btn-primary btn-rounded mt-3">
                                <i class="mdi mdi-chevron-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection