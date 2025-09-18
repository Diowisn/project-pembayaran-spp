@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Kegiatan Tahunan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Card untuk Form Tambah Paket -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-title">Tambah Paket Baru</div>
                    
                    <form method="post" action="{{ route('data-kegiatan-tahunan.store') }}">
                        @csrf
                        
                        <div class="form-row">
                            <div class="form-group col-md-10">
                                <label>Nama Paket</label>
                                <input type="text" class="form-control @error('nama_paket') is-invalid @enderror" 
                                       name="nama_paket" value="{{ old('nama_paket') }}" required 
                                       placeholder="Masukkan nama paket baru">
                                <span class="text-danger">@error('nama_paket') {{ $message }} @enderror</span>
                            </div>

                            <div class="form-group col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="mdi mdi-plus"></i> Tambah Paket
                                </button>
                            </div>
                        </div>
                        
                        <!-- Input hidden untuk memastikan hanya paket yang ditambahkan -->
                        <input type="hidden" name="nama_kegiatan" value="">
                    </form>
                </div>
            </div>

            <!-- Widget Paket -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="mdi mdi-package-variant"></i> Daftar Paket Kegiatan
                    </h4>
                    
                    <div class="row">
                        @php
                            $paketWidgets = App\Models\KegiatanTahunan::getPaketWidgets();
                        @endphp
                        
                        @forelse($paketWidgets as $paket)
                            @php
                                $kegiatanCount = App\Models\KegiatanTahunan::where('nama_paket', $paket->nama_paket)
                                    ->whereNotNull('nama_kegiatan')
                                    ->count();
                            @endphp
                            
                            <div class="col-md-4 mb-4">
                                <div class="card card-hover border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="card-title mb-0 text-primary">
                                                {{ $paket->nama_paket }}
                                            </h5>
                                            <span class="badge badge-soft-info rounded-pill px-3">
                                                <i class="mdi mdi-calendar-check me-1"></i>
                                                {{ $kegiatanCount }} Kegiatan
                                            </span>
                                        </div>
                                        
                                        <div class="d-flex flex-column gap-2">
                                            {{-- <a href="{{ route('data-kegiatan-tahunan.create.kegiatan', $paket->nama_paket) }}" 
                                               class="btn btn-soft-success btn-sm d-flex align-items-center">
                                                <i class="mdi mdi-plus me-1"></i> Tambah Kegiatan
                                            </a> --}}
                                            <a href="?paket={{ urlencode($paket->nama_paket) }}" 
                                               class="btn btn-soft-primary btn-sm d-flex align-items-center">
                                                <i class="mdi mdi-eye me-1"></i> Lihat Detail
                                            </a>
                                            <div class="d-flex gap-2 mt-2">
                                                {{-- <a href="{{ route('data-kegiatan-tahunan.edit', $paket->id) }}" 
                                                   class="btn btn-soft-warning btn-sm flex-grow-1">
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </a> --}}
                                                <form action="{{ route('data-kegiatan-tahunan.destroy', $paket->id) }}" 
                                                      method="POST" class="d-inline flex-grow-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-soft-danger btn-sm w-100" 
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus paket ini?')">
                                                        <i class="mdi mdi-delete"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    Belum ada paket kegiatan. Silakan tambahkan paket terlebih dahulu.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Card untuk Daftar Kegiatan (jika paket dipilih) -->
            @if(isset($selectedPaket) && $selectedPaket)
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-title">Form Tambah Kegiatan ke Paket: {{ $selectedPaket }}</div>
                        
                        <form method="post" action="{{ route('data-kegiatan-tahunan.store') }}">
                            @csrf
                            <input type="hidden" name="nama_paket" value="{{ $selectedPaket }}">
                            
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
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="card-title">Daftar Kegiatan dalam Paket: {{ $selectedPaket }}</div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kegiatan</th>
                                        <th>Nominal</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kegiatanPaket as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nama_kegiatan }}</td>
                                            <td>{{ $item->nominal_formatted }}</td>
                                            <td>{{ $item->keterangan ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('data-kegiatan-tahunan.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <form action="{{ route('data-kegiatan-tahunan.destroy', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada kegiatan dalam paket ini</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('styles')
<style>
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
    }
    .btn-soft-primary {
        background-color: rgba(0,123,255,0.1);
        color: #007bff;
        border: none;
    }
    .btn-soft-success {
        background-color: rgba(40,167,69,0.1);
        color: #28a745;
        border: none;
    }
    .btn-soft-warning {
        background-color: rgba(255,193,7,0.1);
        color: #ffc107;
        border: none;
    }
    .btn-soft-danger {
        background-color: rgba(220,53,69,0.1);
        color: #dc3545;
        border: none;
    }
    .badge-soft-info {
        background-color: rgba(23,162,184,0.1);
        color: #17a2b8;
    }
    .btn-soft-primary:hover {
        background-color: #007bff;
        color: white;
    }
    .btn-soft-success:hover {
        background-color: #28a745;
        color: white;
    }
    .btn-soft-warning:hover {
        background-color: #ffc107;
        color: white;
    }
    .btn-soft-danger:hover {
        background-color: #dc3545;
        color: white;
    }
    .gap-2 {
        gap: 0.5rem !important;
    }
    .me-1 {
        margin-right: 0.25rem !important;
    }
</style>
@endsection