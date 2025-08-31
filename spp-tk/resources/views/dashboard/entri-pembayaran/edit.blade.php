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

                    <form method="post" action="{{ route('entry-pembayaran.update', $edit->id) }}" id="pembayaranForm">
                        @csrf
                        @method('put')

                        <input type="hidden" name="nominal_spp" value="{{ $edit->nominal_spp }}">
                        <input type="hidden" name="nominal_fullday" value="{{ $edit->nominal_fullday }}">
                        <input type="hidden" name="nominal_inklusi" value="{{ $edit->nominal_inklusi }}">

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
                            @if ($edit->siswa->spp->nominal_konsumsi > 0)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Konsumsi</label>
                                        <input type="text" class="form-control mb-2"
                                            value="Rp {{ number_format($edit->siswa->spp->nominal_konsumsi, 0, ',', '.') }}" 
                                            readonly>
                                        <small class="text-muted">Nominal Awal</small>
                                        
                                        <input type="number" name="nominal_konsumsi" 
                                            class="form-control @error('nominal_konsumsi') is-invalid @enderror"
                                            value="{{ old('nominal_konsumsi', $edit->nominal_konsumsi) }}"
                                            min="0" max="{{ $edit->siswa->spp->nominal_konsumsi }}"
                                            onchange="hitungTotal()">
                                        <small class="text-muted">Bisa dikurangi jika ada pengembalian</small>
                                        <span class="text-danger">
                                            @error('nominal_konsumsi')
                                                {{ $message }}
                                            @enderror
                                        </span>
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
                            <input type="text" class="form-control" id="total_tagihan"
                                value="Rp {{ number_format(
                                    $edit->nominal_spp + 
                                    $edit->nominal_konsumsi + 
                                    $edit->nominal_fullday +
                                    $edit->nominal_inklusi,
                                    0,
                                    ',',
                                    '.',
                                ) }}"
                                readonly>
                            <input type="hidden" name="jumlah_tagihan" id="jumlah_tagihan" 
                                value="{{ $edit->nominal_spp + $edit->nominal_konsumsi + $edit->nominal_fullday + $edit->nominal_inklusi }}">
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
                                name="jumlah_bayar" id="jumlah_bayar" 
                                value="{{ old('jumlah_bayar', $edit->jumlah_bayar) }}"
                                min="{{ $edit->nominal_spp + $edit->nominal_konsumsi + $edit->nominal_fullday + $edit->nominal_inklusi }}"
                                oninput="hitungKembalian()"
                                required>
                            <span class="text-danger">
                                @error('jumlah_bayar')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label>Kembalian</label>
                            <input type="text" class="form-control" id="kembalian"
                                value="Rp {{ number_format($edit->kembalian, 0, ',', '.') }}" readonly>
                            <input type="hidden" name="kembalian" id="kembalian_value" value="{{ $edit->kembalian }}">
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
                            <select class="form-control @error('is_lunas') is-invalid @enderror" name="is_lunas" id="is_lunas">
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

@section('scripts')
    <script>
    function hitungTotal() {
        const nominalSpp = parseFloat(document.querySelector('input[name="nominal_spp"]').value) || 0;
        const nominalKonsumsi = parseFloat(document.querySelector('input[name="nominal_konsumsi"]').value) || 0;
        const nominalFullday = parseFloat(document.querySelector('input[name="nominal_fullday"]').value) || 0;
        const nominalInklusi = parseFloat(document.querySelector('input[name="nominal_inklusi"]').value) || 0;
        
        const totalTagihan = nominalSpp + nominalKonsumsi + nominalFullday + nominalInklusi;
        
        document.getElementById('total_tagihan').value = 'Rp ' + totalTagihan.toLocaleString('id-ID');
        document.getElementById('jumlah_tagihan').value = totalTagihan;
        
        // Update minimum payment
        const jumlahBayar = document.getElementById('jumlah_bayar');
        jumlahBayar.min = totalTagihan;
        
        if (parseFloat(jumlahBayar.value) < totalTagihan) {
            jumlahBayar.value = totalTagihan;
        }
        
        // Hitung kembalian
        hitungKembalian();
    }

    function hitungKembalian() {
        const totalTagihan = parseFloat(document.getElementById('jumlah_tagihan').value) || 0;
        const jumlahBayar = parseFloat(document.getElementById('jumlah_bayar').value) || 0;
        
        const kembalian = Math.max(0, jumlahBayar - totalTagihan);
        
        document.getElementById('kembalian').value = 'Rp ' + kembalian.toLocaleString('id-ID');
        document.getElementById('kembalian_value').value = kembalian;
        
        // Update status lunas
        const isLunas = document.getElementById('is_lunas');
        isLunas.value = jumlahBayar >= totalTagihan ? '1' : '0';
    }

    // Inisialisasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        hitungTotal();
        
        // Event listener untuk input konsumsi
        const konsumsiInput = document.querySelector('input[name="nominal_konsumsi"]');
        if (konsumsiInput) {
            konsumsiInput.addEventListener('input', hitungTotal);
        }
        
        // Event listener untuk input jumlah bayar
        const jumlahBayarInput = document.getElementById('jumlah_bayar');
        if (jumlahBayarInput) {
            jumlahBayarInput.addEventListener('input', hitungKembalian);
        }
    });
    </script>
@endsection