@extends('layouts.app')

@section('content')
    @include('sweetalert::alert')
    <style>
        .login-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }
    </style>
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="row justify-content-center w-100">
            <div class="col-md-8">
                <div class="card login-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('Login Siswa') }}</span>
                        <a href="{{ route('login.option') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ url('login/siswa/proses') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-right">{{ __('NISN') }}</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('nisn') is-invalid @enderror"
                                        name="nisn" value="{{ old('nisn') }}" required autofocus>

                                    @error('nisn')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Nama Lengkap') }}</label>

                                <div class="col-md-6">
                                    <input id="nama_siswa" type="text"
                                        class="form-control @error('nama_siswa') is-invalid @enderror" name="nama_siswa"
                                        value="{{ old('nisn') }}" required>

                                    @error('nama_siswa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                </div>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
