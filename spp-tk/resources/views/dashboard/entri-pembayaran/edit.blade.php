@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Pembayaran</li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Edit Pembayaran</div>

                    <form method="post" action="{{ route('entry-pembayaran.update', $edit->id) }}">
                        @csrf
                        @method('put')

                        <div class="form-group">
                            <label>NISN Siswa</label>
                            <input type="text" class="form-control @error('nisn') is-invalid @enderror" name="nisn"
                                value="{{ old('nisn', $edit->siswa->nisn) }}" readonly>
                            <span class="text-danger">
                                @error('nisn')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label>Nama Siswa</label>
                            <input type="text" class="form-control" value="{{ $edit->siswa->nama }}" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>SPP</label>
                                    <input type="text" class="form-control"
                                        value="Rp {{ number_format($edit->nominal_spp, 0, ',', '.') }}" readonly>
                                </div>
                            </div>
                            @if ($edit->nominal_konsumsi > 0)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Konsumsi</label>
                                        <input type="text" class="form-control"
                                            value="Rp {{ number_format($edit->nominal_konsumsi, 0, ',', '.') }}" readonly>
                                    </div>
                                </div>
                            @endif
                            @if ($edit->nominal_fullday > 0)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fullday</label>
                                        <input type="text" class="form-control"
                                            value="Rp {{ number_format($edit->nominal_fullday, 0, ',', '.') }}" readonly>
                                    </div>
                                </div>
                            @endif
                            @if ($edit->nominal_inklusi > 0)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Inklusi</label>
                                        <input type="text" class="form-control"
                                            value="Rp {{ number_format($edit->nominal_inklusi, 0, ',', '.') }}" readonly>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Total Tagihan</label>
                            <input type="text" class="form-control"
                                value="Rp {{ number_format(
                                    $edit->nominal_spp + 
                                    ($edit->nominal_konsumsi ?? 0) + 
                                    ($edit->nominal_fullday ?? 0) +
                                    ($edit->nominal_inklusi ?? 0),
                                    0,
                                    ',',
                                    '.',
                                ) }}"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label>Bulan</label>
                            <select class="form-control @error('bulan') is-invalid @enderror" name="bulan">
                                <option value="">Pilih Bulan</option>
                                @foreach (['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'] as $bulan)
                                    <option value="{{ $bulan }}"
                                        {{ old('bulan', $edit->bulan) == $bulan ? 'selected' : '' }}>
                                        {{ ucfirst($bulan) }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                @error('bulan')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Bayar</label>
                            <input type="number" class="form-control @error('jumlah_bayar') is-invalid @enderror"
                                name="jumlah_bayar" value="{{ old('jumlah_bayar', $edit->jumlah_bayar) }}"
                                min="{{ $edit->nominal_spp + ($edit->nominal_konsumsi ?? 0) + ($edit->nominal_fullday ?? 0) }}"
                                required>
                            <span class="text-danger">
                                @error('jumlah_bayar')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Bayar</label>
                            <input type="date" class="form-control @error('tgl_bayar') is-invalid @enderror"
                                name="tgl_bayar" value="{{ old('tgl_bayar', $edit->tgl_bayar->format('Y-m-d')) }}"
                                required>
                            <span class="text-danger">
                                @error('tgl_bayar')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control @error('is_lunas') is-invalid @enderror" name="is_lunas">
                                <option value="1" {{ old('is_lunas', $edit->is_lunas) ? 'selected' : '' }}>Lunas
                                </option>
                                <option value="0" {{ !old('is_lunas', $edit->is_lunas) ? 'selected' : '' }}>Belum
                                    Lunas</option>
                            </select>
                            <span class="text-danger">
                                @error('is_lunas')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <a href="{{ route('entry-pembayaran.index') }}" class="btn btn-primary btn-rounded">
                            <i class="mdi mdi-chevron-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success btn-rounded float-right">
                            <i class="mdi mdi-check"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
