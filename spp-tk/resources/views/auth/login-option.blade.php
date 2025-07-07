@extends('layouts.app')

@section('content')
    <style>
        body.login-page {
            background-image: url('{{ asset("/assets/images/bangunan.jpg") }}') !important;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        main {
            padding: 0 !important;
        }

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

    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="row justify-content-center w-100">
            <div class="col-md-8">
                <div class="card login-card">
                    <div class="card-header text-center bg-transparent border-0">
                        <h3 class="mb-0">{{ __('Pilih Jenis Login') }}</h3>
                    </div>

                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-6 mb-4">
                                <a href="{{ route('login.admin') }}" class="text-decoration-none">
                                    <div class="card hover-shadow border-0">
                                        <div class="card-body py-4">
                                            <i class="fas fa-user-shield fa-5x mb-3 text-primary"></i>
                                            <h4 class="card-title">Admin/Petugas</h4>
                                            <p class="text-muted">Login untuk administrator dan petugas</p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-6 mb-4">
                                <a href="{{ route('login.siswa') }}" class="text-decoration-none">
                                    <div class="card hover-shadow border-0">
                                        <div class="card-body py-4">
                                            <i class="fas fa-user-graduate fa-5x mb-3 text-success"></i>
                                            <h4 class="card-title">Siswa</h4>
                                            <p class="text-muted">Login untuk siswa</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
