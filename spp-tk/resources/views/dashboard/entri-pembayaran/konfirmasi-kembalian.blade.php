@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="#">
            <i class="mdi mdi-home text-muted"></i>
        </a>
    </li>
    <li class="breadcrumb-item">
        <a href="#" class="text-muted">Pembayaran</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Konfirmasi Kembalian</li>
@endsection

@section('styles')
<style>
    /* Success Banner */
    .payment-success-banner {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        position: relative;
    }
    
    .banner-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0.1;
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='1' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .success-icon {
        font-size: 4rem;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
        40% {transform: translateY(-15px);}
        60% {transform: translateY(-7px);}
    }

    /* Method Cards */
    .method-card {
        border: none;
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .method-savings:hover {
        background: rgba(40, 167, 69, 0.1);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);
    }

    .method-cash:hover {
        background: rgba(255, 193, 7, 0.1);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.15);
    }

    .method-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 2rem;
    }

    .method-savings .method-icon {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }

    .method-cash .method-icon {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .method-content {
        flex: 1;
    }

    .method-content h5 {
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #212529;
    }

    .method-content p {
        color: #6c757d;
        font-size: 0.9rem;
        margin: 0;
    }

    .method-arrow {
        color: #adb5bd;
        font-size: 1.5rem;
        transition: transform 0.3s ease;
    }

    .method-card:hover .method-arrow {
        transform: translateX(5px);
    }

    /* Custom Alert */
    .custom-alert {
        background: rgba(13, 110, 253, 0.1);
        border-radius: 12px;
        padding: 1.5rem;
    }

    .custom-alert .alert-icon {
        width: 48px;
        height: 48px;
        background: rgba(13, 110, 253, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #0d6efd;
    }

    /* Detail Items */
    .detail-item {
        transition: all 0.3s ease;
        border-radius: 10px;
        padding: 1rem;
    }

    .detail-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .detail-item small {
        font-size: 0.8rem;
        font-weight: 500;
    }

    .detail-item strong {
        font-size: 1.1rem;
        font-weight: 600;
        display: block;
        margin: 4px 0;
    }

    /* Back Button */
    .btn-light {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .btn-light:hover {
        background: #e9ecef;
        border-color: #ced4da;
        transform: translateY(-1px);
    }

    /* Utility Classes */
    .shadow-hover {
        transition: box-shadow 0.3s ease;
    }

    .shadow-hover:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important;
    }

    .bg-success-subtle {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto" style="background-color: white">
        <div class="payment-success-banner text-center py-4 mb-4 rounded-4 position-relative overflow-hidden" style="color: #212529">
            <div class="banner-bg"></div>
            <div class="banner-content position-relative">
                <div class="success-icon-wrap mb-3">
                    <i class="mdi mdi-check-circle-outline success-icon"></i>
                </div>
                <h3 class="text-black mb-2">Pembayaran Berhasil!</h3>
                <p class="text-black-50 mb-0">Transaksi telah diproses dengan sukses</p>
            </div>
        </div>

        <div class="card border-0 shadow-hover">
            <div class="card-header border-0 bg-white pt-4 pb-0">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="mb-0 d-flex align-items-center">
                        <i class="mdi mdi-cash-refund text-primary me-2"></i>
                        Konfirmasi Penanganan Kembalian
                    </h4>
                    <span class="badge bg-success-subtle text-success px-3 py-2">
                        <i class="mdi mdi-check-circle me-1"></i>
                        Pembayaran Sukses
                    </span>
                </div>
                <div class="alert custom-alert border-0 mb-0">
                    <div class="d-flex align-items-center">
                        <div class="alert-icon me-3">
                            <i class="mdi mdi-information-outline"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="text-primary mb-1">Kembalian Terdeteksi</h5>
                            <h3 class="text-success mb-2">Rp {{ number_format($kembalian, 0, ',', '.') }}</h3>
                            <p class="text-muted mb-0">Silakan pilih metode penanganan kembalian di bawah ini:</p>
                        </div>
                    </div>
                </div>
            </div>

                <div class="card-body px-4 pb-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('entri-pembayaran.handle-kembalian') }}">
                                @csrf
                                <input type="hidden" name="id_pembayaran" value="{{ $pembayaran->id }}">
                                <input type="hidden" name="jumlah_kembalian" value="{{ $kembalian }}">
                                <input type="hidden" name="action" value="tabungan">
                                
                                <button type="submit" class="method-card method-savings w-100 text-start">
                                    <div class="method-icon">
                                        <i class="mdi mdi-wallet"></i>
                                    </div>
                                    <div class="method-content">
                                        <h5>Masukkan ke Tabungan</h5>
                                        <p class="mb-0">Kembalian akan ditambahkan ke saldo tabungan siswa</p>
                                    </div>
                                </button>
                            </form>
                        </div>
                        
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('entri-pembayaran.handle-kembalian') }}">
                                @csrf
                                <input type="hidden" name="id_pembayaran" value="{{ $pembayaran->id }}">
                                <input type="hidden" name="jumlah_kembalian" value="{{ $kembalian }}">
                                <input type="hidden" name="action" value="tunai">
                                
                                <button type="submit" class="method-card method-cash w-100 text-start">
                                    <div class="method-icon">
                                        <i class="mdi mdi-cash-multiple"></i>
                                    </div>
                                    <div class="method-content">
                                        <h5>Kembalikan Tunai</h5>
                                        <p class="mb-0">Kembalian akan dikembalikan secara tunai</p>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>

                <div class="text-center mt-4">
                    <a href="{{ route('pembayaran.cari-siswa', ['nisn' => $siswa->nisn]) }}" 
                       class="btn btn-light btn-lg px-4">
                        <i class="mdi mdi-arrow-left me-2"></i> Kembali ke Data Siswa
                    </a>
                </div>

                <!-- Info Pembayaran -->
                <div class="mt-4">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="d-flex align-items-center mb-3">
                                <i class="mdi mdi-information-outline text-primary me-2"></i>
                                Detail Pembayaran
                            </h6>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="detail-item p-2 rounded bg-white">
                                        <small class="text-muted d-block">Nama Siswa</small>
                                        <strong class="text-primary">{{ $siswa->nama }}</strong>
                                        <small class="text-muted d-block">NISN: {{ $siswa->nisn }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-item p-2 rounded bg-white">
                                        <small class="text-muted d-block">Periode</small>
                                        <strong class="text-primary">
                                            {{ ucfirst($pembayaran->bulan) }} {{ $pembayaran->tahun }}
                                        </strong>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-item p-2 rounded bg-white">
                                        <small class="text-muted d-block">Total Bayar</small>
                                        <strong class="text-success">
                                            Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                                        </strong>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-item p-2 rounded bg-white">
                                        <small class="text-muted d-block">Tagihan</small>
                                        <strong class="text-danger">
                                            Rp {{ number_format($pembayaran->jumlah_bayar - $kembalian, 0, ',', '.') }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection