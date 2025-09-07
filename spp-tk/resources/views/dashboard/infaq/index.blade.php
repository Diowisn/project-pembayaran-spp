@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Infaq Gedung</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Tampilkan Alert Kembalian jika ada -->
            @if (session('kembalian_info'))
                @php
                    $kembalianInfo = session('kembalian_info');
                @endphp
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-information-outline"></i>
                    <strong>Kembalian:</strong> {{ $kembalianInfo['message'] }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="card-title">Entri Pembayaran Infaq Gedung</div>

                    <form method="GET" action="{{ route('infaq.cari-siswa') }}">
                        <div class="form-group">
                            <label for="nisn_search">Cari Berdasarkan NISN</label>
                            <div class="input-group">
                                <input type="text" name="nisn" id="nisn_search" class="form-control"
                                    placeholder="Masukkan NISN" value="{{ old('nisn', $siswa->nisn ?? '') }}" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Cari</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if (isset($siswa))
                        <!-- Info Siswa -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5>Informasi Siswa</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>NISN:</strong> {{ $siswa->nisn }}
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Nama:</strong> {{ $siswa->nama }}
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas ?? '-' }}
                                            </div>
                                        </div>

                                        <a href="{{ route('infaq.rekap', $siswa->id) }}" class="btn btn-info btn-sm"
                                            target="_blank">
                                            <i class="mdi mdi-file-pdf"></i> Rekap Infaq
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Total -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6>Total Tagihan Infaq</h6>
                                        <h4>Rp {{ number_format($total_tagihan_infaq, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6>Total Dibayar</h6>
                                        <h4>Rp {{ number_format($total_dibayar, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card {{ $sisa_pembayaran > 0 ? 'bg-warning' : 'bg-secondary' }} text-white">
                                    <div class="card-body text-center">
                                        <h6>Sisa Pembayaran</h6>
                                        <h4>Rp {{ number_format($sisa_pembayaran, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Pembayaran -->
                        @if ($sisa_pembayaran > 0)
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Pembayaran Infaq</h5>

                                    <form method="post" action="{{ route('infaq.store') }}" id="formPembayaranInfaq">
                                        @csrf
                                        <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                                        <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">
                                        <input type="hidden" name="jumlah_tagihan" value="{{ $sisa_pembayaran }}">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Paket Infaq Gedung</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $siswa->infaqGedung->paket ?? 'Tidak ada paket' }}"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Jumlah Bayar</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp</span>
                                                        </div>
                                                        <input type="number"
                                                            class="form-control @error('jumlah_bayar') is-invalid @enderror"
                                                            name="jumlah_bayar" id="jumlah_bayar"
                                                            value="{{ old('jumlah_bayar', $sisa_pembayaran) }}"
                                                            min="1" max="{{ $sisa_pembayaran * 2 }}"
                                                            oninput="hitungKembalianInfaq({{ $sisa_pembayaran }}, this.value)"
                                                            required>
                                                    </div>
                                                    <span class="text-danger">
                                                        @error('jumlah_bayar')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Tanggal Bayar</label>
                                                    <input type="date"
                                                        class="form-control @error('tgl_bayar') is-invalid @enderror"
                                                        name="tgl_bayar" value="{{ old('tgl_bayar', date('Y-m-d')) }}"
                                                        required>
                                                    <span class="text-danger">
                                                        @error('tgl_bayar')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <button type="submit" class="btn btn-success btn-block"
                                                        id="submitButton">
                                                        <i class="mdi mdi-check"></i> Simpan Pembayaran
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="info_pembayaran_infaq" class="mt-2 p-2 bg-light rounded">
                                            <small>
                                                <div id="status_pembayaran_infaq" class="alert alert-info p-2 mb-2">
                                                    <span id="status_text_infaq">Pembayaran sesuai sisa tagihan</span>
                                                </div>
                                                <div id="kembalian_infaq" class="text-success d-none">
                                                    Kembalian: <span id="jumlah_kembalian_infaq">0</span>
                                                </div>
                                            </small>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="mdi mdi-check-circle"></i> Pembayaran Infaq sudah LUNAS
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Pembayaran -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Data Pembayaran Infaq</div>

                    <!-- Form Pencarian -->
                    <form method="GET" action="{{ route('infaq.index') }}" class="form-inline mb-3">
                        <input type="text" name="search" class="form-control mr-2"
                            placeholder="Cari NISN / Nama Siswa" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary mr-2">Cari</button>

                        @if (request()->has('search'))
                            <a href="{{ route('infaq.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Paket</th>
                                    <th>Angsuran Ke</th>
                                    <th>Nominal Infaq</th>
                                    <th>Jumlah Bayar</th>
                                    <th>Kembalian</th>
                                    <th>Status</th>
                                    <th>Tanggal Bayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = ($angsuran->currentPage() - 1) * $angsuran->perPage() + 1;
                                    $hasData = false;
                                @endphp

                                @foreach ($angsuran as $value)
                                    @php
                                        $hasData = true;
                                        $total_tagihan = $value->infaqGedung->nominal ?? 0;

                                        // Gunakan data yang sudah dihitung di controller
                                        $item_data = $angsuran_data[$value->id] ?? [
                                            'is_lunas' => $value->is_lunas,
                                            'total_dibayar' => 0,
                                            'kekurangan' => $total_tagihan,
                                        ];

                                        $is_lunas = $item_data['is_lunas'];
                                        $kekurangan = $item_data['kekurangan'];
                                        $kembalian = $value->kembalian ?? 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $value->siswa->nisn }}</td>
                                        <td>{{ $value->siswa->nama }}</td>
                                        <td>{{ $value->infaqGedung->paket ?? '-' }}</td>
                                        <td>{{ $value->angsuran_ke }}</td>
                                        <td>Rp {{ number_format($total_tagihan, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($value->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $kembalian =
                                                    $angsuran_data[$value->id]['kembalian'] ?? ($value->kembalian ?? 0);
                                            @endphp
                                            @if ($kembalian > 0)
                                                <span class="text-success">+Rp
                                                    {{ number_format($kembalian, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">Rp 0</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($is_lunas)
                                                @if ($kembalian > 0)
                                                    <span class="badge badge-info">Lunas</span>
                                                @else
                                                    <span class="badge badge-success">Lunas</span>
                                                @endif
                                            @else
                                                <span class="badge badge-warning">Belum Lunas</span>
                                                @if ($kekurangan > 0)
                                                    <br>
                                                    <small class="text-danger">Kurang: Rp
                                                        {{ number_format($kekurangan, 0, ',', '.') }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($value->tgl_bayar)
                                                {{ $value->tgl_bayar->format('d M, Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('infaq.edit', $value->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <a href="{{ route('infaq.generate', $value->id) }}"
                                                class="btn btn-info btn-sm" target="_blank">
                                                <i class="mdi mdi-file-pdf"></i>
                                            </a>
                                            <form action="{{ route('infaq.destroy', $value->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Hapus pembayaran ini?')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                @if (!$hasData)
                                    <tr>
                                        <td colspan="11" class="text-center">Tidak ada data pembayaran</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($angsuran->lastPage() > 1)
                        <div class="btn-group float-right mt-3">
                            <a href="{{ $angsuran->previousPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $angsuran->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $angsuran->currentPage() ? 'active' : '' }}"
                                    href="{{ $angsuran->url($i) }}">{{ $i }}</a>
                            @endfor
                            <a href="{{ $angsuran->nextPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function hitungKembalianInfaq(tagihan, bayar) {
            const jumlahBayar = parseFloat(bayar) || 0;
            const jumlahTagihan = parseFloat(tagihan);

            const kembalian = Math.max(0, jumlahBayar - jumlahTagihan);
            const kekurangan = Math.max(0, jumlahTagihan - jumlahBayar);

            // Update tampilan kembalian
            const kembalianElement = document.getElementById('jumlah_kembalian_infaq');
            kembalianElement.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');

            // Update status pembayaran
            const statusElement = document.getElementById('status_text_infaq');
            const statusAlert = document.getElementById('status_pembayaran_infaq');
            const containerKembalian = document.getElementById('kembalian_infaq');

            if (kembalian > 0) {
                statusElement.textContent = 'Pembayaran lebih: Rp ' + kembalian.toLocaleString('id-ID') +
                    ' akan masuk ke tabungan';
                statusAlert.className = 'alert alert-success p-2 mb-2';
                containerKembalian.classList.remove('d-none');
            } else if (kekurangan > 0) {
                statusElement.textContent = 'Pembayaran kurang: Rp ' + kekurangan.toLocaleString('id-ID');
                statusAlert.className = 'alert alert-warning p-2 mb-2';
                containerKembalian.classList.add('d-none');
            } else {
                statusElement.textContent = 'Pembayaran sesuai tagihan';
                statusAlert.className = 'alert alert-info p-2 mb-2';
                containerKembalian.classList.add('d-none');
            }

            // Validasi tombol submit
            const submitButton = document.getElementById('submitButton');
            if (jumlahBayar < 1) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="mdi mdi-block-helper"></i> Jumlah tidak valid';
            } else {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="mdi mdi-check"></i> Simpan Pembayaran';
            }
        }

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const inputBayar = document.getElementById('jumlah_bayar');
            if (inputBayar) {
                const sisaPembayaran = parseFloat(inputBayar.getAttribute('max')) /
                    2; // Karena max = sisa_pembayaran * 2

                // Hitung kembalian awal
                hitungKembalianInfaq(sisaPembayaran, inputBayar.value);

                // Event listener untuk perubahan
                inputBayar.addEventListener('input', function() {
                    hitungKembalianInfaq(sisaPembayaran, this.value);
                });
            }
        });
    </script>
@endsection
