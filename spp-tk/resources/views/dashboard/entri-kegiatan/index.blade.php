@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Entri Kegiatan</li>
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
                    <div class="card-title">Entri Pembayaran Kegiatan Tahunan</div>

                    <form method="GET" action="{{ route('entri-kegiatan.cari-siswa') }}">
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
                                    </div>
                                    <div>
                                        <form method="GET" action="{{ route('entri-kegiatan.generate-rekap-siswa-pdf') }}"
                                            class="d-inline">
                                            <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                                            <button type="submit" class="btn btn-success">
                                                <i class="mdi mdi-file-pdf"></i> Download Rekap PDF
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Total -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6>Total Tagihan Kegiatan</h6>
                                        <h4>Rp {{ number_format($total_tagihan_kegiatan, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6>Total Dibayar</h6>
                                        <h4>Rp {{ number_format($total_dibayar_semua, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card {{ $sisa_semua > 0 ? 'bg-warning' : 'bg-secondary' }} text-white">
                                    <div class="card-body text-center">
                                        <h6>Sisa Pembayaran</h6>
                                        <h4>Rp {{ number_format($sisa_semua, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Kegiatan dalam Tabel -->
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center">Nama Kegiatan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Total Tagihan</th>
                                        <th class="text-center">Sudah Dibayar</th>
                                        <th class="text-center">Sisa</th>
                                        <th class="text-center" style="width: 300px;">Pembayaran</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detailKegiatan as $index => $detail)
                                        <tr>
                                            <td class="text-center">{{ $detail['kegiatan']->nama_kegiatan }}</td>
                                            <td class="text-center">
                                                @if ($detail['partisipasi'] === 'tidak_ikut')
                                                    <span class="badge badge-danger">TIDAK IKUT</span>
                                                    @if ($detail['alasan_tidak_ikut'])
                                                        <small
                                                            class="d-block text-muted mt-1">{{ $detail['alasan_tidak_ikut'] }}</small>
                                                    @endif
                                                @else
                                                    <span
                                                        class="badge {{ $detail['is_lunas'] ? 'badge-success' : 'badge-warning' }}">
                                                        {{ $detail['is_lunas'] ? 'LUNAS' : 'BELUM LUNAS' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">Rp
                                                {{ number_format($detail['kegiatan']->nominal, 0, ',', '.') }}</td>
                                            <td class="text-center text-success">Rp
                                                {{ number_format($detail['total_dibayar'], 0, ',', '.') }}</td>
                                            <td
                                                class="text-center {{ $detail['sisa_pembayaran'] > 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($detail['sisa_pembayaran'], 0, ',', '.') }}
                                            </td>
                                            <td>
                                                @if ($detail['partisipasi'] === 'ikut' && !$detail['is_lunas'])
                                                    <form method="post" action="{{ route('entri-kegiatan.store') }}"
                                                        id="formPembayaran{{ $detail['kegiatan']->id }}"
                                                        class="form-inline">
                                                        @csrf
                                                        <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                                                        <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">
                                                        <input type="hidden" name="id_kegiatan"
                                                            value="{{ $detail['kegiatan']->id }}">
                                                        <input type="hidden" name="jumlah_tagihan"
                                                            value="{{ $detail['sisa_pembayaran'] }}">

                                                        <div class="input-group input-group-sm mr-2">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Rp</span>
                                                            </div>
                                                            <input type="number" class="form-control" name="jumlah_bayar"
                                                                value="{{ $detail['sisa_pembayaran'] }}" min="0"
                                                                oninput="hitungKembalian({{ $detail['sisa_pembayaran'] }}, this.value, '{{ $detail['kegiatan']->id }}')"
                                                                style="width: 120px;" required>
                                                        </div>
                                                        <div class="input-group input-group-sm mr-2">
                                                            <input type="date" class="form-control" name="tgl_bayar"
                                                                value="{{ date('Y-m-d') }}" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="mdi mdi-cash"></i> Bayar
                                                        </button>
                                                    </form>
                                                @elseif ($detail['is_lunas'])
                                                    <div class="text-success text-center">
                                                        <i class="mdi mdi-check-circle"></i> Lunas
                                                        @if ($detail['total_dibayar'] > $detail['kegiatan']->nominal)
                                                            <small class="d-block text-info">
                                                                Kembalian: Rp
                                                                {{ number_format($detail['total_dibayar'] - $detail['kegiatan']->nominal, 0, ',', '.') }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <form method="post"
                                                    action="{{ route('entri-kegiatan.toggle-partisipasi', ['siswaId' => $siswa->id]) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="kegiatan_id"
                                                        value="{{ $detail['kegiatan']->id }}">
                                                    <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                                                    @if ($detail['partisipasi'] === 'ikut')
                                                        <input type="hidden" name="partisipasi" value="tidak_ikut">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                            onclick="return confirm('Yakin siswa tidak ikut kegiatan ini?')">
                                                            <i class="mdi mdi-close"></i>
                                                        </button>
                                                    @else
                                                        <input type="hidden" name="partisipasi" value="ikut">
                                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                                            <i class="mdi mdi-check"></i>
                                                        </button>
                                                    @endif
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Pembayaran Sekaligus untuk Semua Kegiatan -->
                    @if (isset($siswa) && $sisa_semua > 0)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Pembayaran Sekaligus</h5>
                                        <p>Bayar semua sisa pembayaran kegiatan sekaligus</p>

                                        <form method="post" action="{{ route('entri-kegiatan.bayar-semua') }}"
                                            id="formPembayaranSemua">
                                            @csrf
                                            <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                                            <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">
                                            <input type="hidden" name="total_sisa" value="{{ $sisa_semua }}">

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="jumlah_bayar_semua">Jumlah Bayar</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Rp</span>
                                                            </div>
                                                            <input type="number" class="form-control"
                                                                name="jumlah_bayar_semua" id="jumlah_bayar_semua"
                                                                value="{{ $sisa_semua }}" min="{{ $sisa_semua }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="tgl_bayar_semua">Tanggal Bayar</label>
                                                        <input type="date" class="form-control" name="tgl_bayar_semua"
                                                            value="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <button type="submit" class="btn btn-success btn-block">
                                                            <i class="mdi mdi-cash-multiple"></i> Bayar Semua Sekaligus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="info_pembayaran_semua" class="mt-2 p-2 bg-light rounded">
                                                <small>
                                                    <div id="status_pembayaran_semua">
                                                        <span id="status_text_semua">Pembayaran sesuai total sisa</span>
                                                    </div>
                                                    <div id="kembalian_semua" class="text-success d-none">
                                                        Kembalian: <span id="jumlah_kembalian_semua">0</span>
                                                    </div>
                                                </small>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    <div class="card-title">Data Pembayaran Kegiatan</div>

                    <!-- Form Pencarian -->
                    <form method="GET" action="{{ route('entri-kegiatan.index') }}" class="form-inline mb-3">
                        <input type="text" name="search" class="form-control mr-2"
                            placeholder="Cari NISN / Nama Siswa" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary mr-2">Cari</button>

                        @if (request()->has('search'))
                            <a href="{{ route('entri-kegiatan.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Kegiatan</th>
                                    <th>Angsuran Ke</th>
                                    <th>Nominal Kegiatan</th>
                                    <th>Jumlah Bayar</th>
                                    <th>Kembalian</th>
                                    <th>Status</th>
                                    <th>Tanggal Bayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = ($pembayaran->currentPage() - 1) * $pembayaran->perPage() + 1;
                                    $hasData = false;
                                @endphp

                                @foreach ($pembayaran as $value)
                                    @if ($value->partisipasi === 'ikut')
                                        @php
                                            $hasData = true;
                                            $total_tagihan = $value->kegiatan->nominal ?? 0;
                                            $kembalian = $value->kembalian ?? 0;
                                            $is_lunas = $value->is_lunas;
                                        @endphp
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $value->siswa->nisn }}</td>
                                            <td>{{ $value->siswa->nama }}</td>
                                            <td>{{ $value->kegiatan->nama_kegiatan }}</td>
                                            <td>{{ $value->angsuran_ke }}</td>
                                            <td>Rp {{ number_format($total_tagihan, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($value->jumlah_bayar, 0, ',', '.') }}</td>
                                            <td>
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
                                                    @php
                                                        $kekurangan = max(0, $total_tagihan - $value->jumlah_bayar);
                                                    @endphp
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
                                                <a href="{{ route('entri-kegiatan.edit', $value->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <a href="{{ route('entri-kegiatan.generate-pdf', $value->id) }}"
                                                    class="btn btn-info btn-sm" target="_blank">
                                                    <i class="mdi mdi-file-pdf"></i>
                                                </a>
                                                <form action="{{ route('entri-kegiatan.destroy', $value->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Hapus pembayaran ini?')">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
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
                    @if ($pembayaran->lastPage() > 1)
                        <div class="btn-group float-right mt-3">
                            <a href="{{ $pembayaran->previousPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $pembayaran->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $pembayaran->currentPage() ? 'active' : '' }}"
                                    href="{{ $pembayaran->url($i) }}">{{ $i }}</a>
                            @endfor
                            <a href="{{ $pembayaran->nextPageUrl() }}" class="btn btn-success">
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
        function hitungKembalian(tagihan, bayar, kegiatanId) {
            const jumlahBayar = parseFloat(bayar) || 0;
            const jumlahTagihan = parseFloat(tagihan);

            const kembalian = Math.max(0, jumlahBayar - jumlahTagihan);
            const kekurangan = Math.max(0, jumlahTagihan - jumlahBayar);

            // Update tampilan kembalian
            const kembalianElement = document.getElementById('kembalian_' + kegiatanId);
            kembalianElement.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');

            // Update status pembayaran
            const statusElement = document.getElementById('status_text_' + kegiatanId);
            const statusAlert = document.getElementById('status_pembayaran_' + kegiatanId);

            if (kembalian > 0) {
                statusElement.textContent = 'Pembayaran lebih: Rp ' + kembalian.toLocaleString('id-ID') +
                    ' akan masuk ke tabungan';
                statusAlert.className = 'alert alert-success p-2 mb-2';
            } else if (kekurangan > 0) {
                statusElement.textContent = 'Pembayaran kurang: Rp ' + kekurangan.toLocaleString('id-ID');
                statusAlert.className = 'alert alert-warning p-2 mb-2';
            } else {
                statusElement.textContent = 'Pembayaran sesuai tagihan';
                statusAlert.className = 'alert alert-info p-2 mb-2';
            }

            // Validasi minimal pembayaran
            const submitButton = document.querySelector('#formPembayaran' + kegiatanId + ' button[type="submit"]');
            if (jumlahBayar < 1) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="mdi mdi-block-helper"></i> Jumlah tidak valid';
            } else {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="mdi mdi-cash"></i> Proses Pembayaran';
            }
        }

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Set event listener untuk semua input pembayaran
            const inputs = document.querySelectorAll('input[name="jumlah_bayar"]');
            inputs.forEach(input => {
                const kegiatanId = input.id.split('_')[2];
                const tagihan = parseFloat(input.getAttribute('max'));

                // Hitung kembalian awal
                hitungKembalian(tagihan, input.value, kegiatanId);

                // Event listener untuk perubahan input
                input.addEventListener('input', function() {
                    hitungKembalian(tagihan, this.value, kegiatanId);
                });
            });

            // Set maksimal pembayaran untuk semua form
            const forms = document.querySelectorAll('form[id^="formPembayaran"]');
            forms.forEach(form => {
                const input = form.querySelector('input[name="jumlah_bayar"]');
                const maxValue = parseFloat(input.getAttribute('max'));
                input.setAttribute('max', maxValue * 2); // Allow overpayment
            });
        });

        // Hitung kembalian untuk pembayaran sekaligus
        function hitungKembalianSemua(totalSisa, jumlahBayar) {
            const bayar = parseFloat(jumlahBayar) || 0;
            const sisa = parseFloat(totalSisa);

            const kembalian = Math.max(0, bayar - sisa);
            const kekurangan = Math.max(0, sisa - bayar);

            const statusElement = document.getElementById('status_text_semua');
            const kembalianElement = document.getElementById('jumlah_kembalian_semua');
            const containerKembalian = document.getElementById('kembalian_semua');

            if (kembalian > 0) {
                statusElement.textContent = 'Pembayaran lebih: Rp ' + kembalian.toLocaleString('id-ID') +
                    ' akan masuk ke tabungan';
                statusElement.className = 'text-success';
                kembalianElement.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');
                containerKembalian.classList.remove('d-none');
            } else if (kekurangan > 0) {
                statusElement.textContent = 'Pembayaran kurang: Rp ' + kekurangan.toLocaleString('id-ID');
                statusElement.className = 'text-danger';
                containerKembalian.classList.add('d-none');
            } else {
                statusElement.textContent = 'Pembayaran sesuai total sisa';
                statusElement.className = 'text-info';
                containerKembalian.classList.add('d-none');
            }

            // Validasi tombol submit
            const submitButton = document.querySelector('#formPembayaranSemua button[type="submit"]');
            if (bayar < sisa) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="mdi mdi-block-helper"></i> Jumlah kurang';
            } else {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="mdi mdi-cash-multiple"></i> Bayar Semua Sekaligus';
            }
        }

        // Event listener untuk input pembayaran sekaligus
        document.addEventListener('DOMContentLoaded', function() {
            const inputBayarSemua = document.getElementById('jumlah_bayar_semua');
            if (inputBayarSemua) {
                const totalSisa = parseFloat(inputBayarSemua.getAttribute('min'));

                // Hitung awal
                hitungKembalianSemua(totalSisa, inputBayarSemua.value);

                // Event listener untuk perubahan
                inputBayarSemua.addEventListener('input', function() {
                    hitungKembalianSemua(totalSisa, this.value);
                });
            }
        });
    </script>
@endsection
