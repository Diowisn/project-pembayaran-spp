@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Tabungan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Manajemen Tabungan Siswa</div>

                    <!-- Form Pencarian Siswa -->
                    <form method="GET" action="{{ route('tabungan.cari-siswa') }}">
                        <div class="form-group">
                            <label for="nisn">Cari Berdasarkan NISN</label>
                            <div class="input-group">
                                <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN"
                                    value="{{ old('nisn', $nisn_search ?? '') }}" required>
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
                                            <div class="col-md-3">
                                                <strong>NISN:</strong> {{ $siswa->nisn }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Nama:</strong> {{ $siswa->nama }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas ?? '-' }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Saldo Tabungan:</strong>
                                                <span class="badge badge-info">Rp
                                                    {{ number_format($saldo, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('tabungan.rekap.cetak', $siswa->id) }}"
                                                class="btn btn-primary" target="_blank">
                                                <i class="mdi mdi-file-pdf"></i> Cetak Rekap
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Transaksi Tabungan -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Transaksi Tabungan</h5>

                                <form method="post" action="{{ route('tabungan.store') }}">
                                    @csrf
                                    <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Tipe Transaksi</label>
                                                <select class="form-control @error('tipe') is-invalid @enderror"
                                                    name="tipe" id="tipe" required onchange="toggleJumlah()">
                                                    <option value="debit" {{ old('tipe') == 'debit' ? 'selected' : '' }}>
                                                        Setoran (Debit)</option>
                                                    <option value="kredit" {{ old('tipe') == 'kredit' ? 'selected' : '' }}>
                                                        Penarikan (Kredit)</option>
                                                </select>
                                                @error('tipe')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label id="label_jumlah">Jumlah Setoran</label>
                                                <input type="number"
                                                    class="form-control @error('jumlah') is-invalid @enderror"
                                                    name="jumlah" id="jumlah" value="{{ old('jumlah') }}"
                                                    min="1" required>
                                                @error('jumlah')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Keterangan</label>
                                                <input type="text"
                                                    class="form-control @error('keterangan') is-invalid @enderror"
                                                    name="keterangan" id="keterangan" value="{{ old('keterangan') }}"
                                                    required>
                                                @error('keterangan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div id="saldo-info" class="alert alert-warning d-none">
                                        <i class="mdi mdi-alert-circle"></i>
                                        Saldo tidak mencukupi untuk penarikan ini.
                                    </div>

                                    <button type="submit" class="btn btn-success btn-rounded float-right mt-3">
                                        <i class="mdi mdi-check"></i> Simpan Transaksi
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Riwayat Transaksi Tabungan -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Riwayat Transaksi Tabungan</h5>

                                <div class="table-responsive">
                                    <!-- Di dalam tabel riwayat transaksi -->
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Tipe</th>
                                                <th>Debit</th>
                                                <th>Kredit</th>
                                                <th>Saldo</th>
                                                <th>Keterangan</th>
                                                <th>Petugas</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($riwayat_tabungan as $transaksi)
                                                <tr>
                                                    <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @if ($transaksi->debit > 0)
                                                            <span class="badge badge-success">Setoran</span>
                                                        @else
                                                            <span class="badge badge-danger">Penarikan</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-success">
                                                        @if ($transaksi->debit > 0)
                                                            Rp {{ number_format($transaksi->debit, 0, ',', '.') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-danger">
                                                        @if ($transaksi->kredit > 0)
                                                            Rp {{ number_format($transaksi->kredit, 0, ',', '.') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>Rp {{ number_format($transaksi->saldo, 0, ',', '.') }}</td>
                                                    <td>{{ $transaksi->keterangan }}</td>
                                                    <td>{{ $transaksi->petugas->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('tabungan.edit', $transaksi->id) }}"
                                                                class="btn btn-sm btn-warning" title="Edit Transaksi">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </a>

                                                            <a href="{{ route('tabungan.transaksi.cetak', $transaksi->id) }}"
                                                                class="btn btn-sm btn-primary" target="_blank"
                                                                title="Cetak Bukti Transaksi">
                                                                <i class="mdi mdi-printer"></i>
                                                            </a>

                                                            @if (auth()->user()->level == 'admin')
                                                                <form method="POST"
                                                                    action="{{ route('tabungan.destroy', $transaksi->id) }}"
                                                                    style="display: inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn btn-sm btn-danger"
                                                                        onclick="confirmDelete({{ $transaksi->id }})"
                                                                        title="Hapus Transaksi">
                                                                        <i class="mdi mdi-delete"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination untuk Riwayat -->
                                @if ($riwayat_tabungan->lastPage() != 1)
                                    <div class="btn-group float-right mt-3">
                                        <a href="{{ $riwayat_tabungan->appends(request()->query())->previousPageUrl() }}"
                                            class="btn btn-success">
                                            <i class="mdi mdi-chevron-left"></i>
                                        </a>
                                        @for ($i = 1; $i <= $riwayat_tabungan->lastPage(); $i++)
                                            <a class="btn btn-success {{ $i == $riwayat_tabungan->currentPage() ? 'active' : '' }}"
                                                href="{{ $riwayat_tabungan->appends(request()->query())->url($i) }}">
                                                {{ $i }}
                                            </a>
                                        @endfor
                                        <a href="{{ $riwayat_tabungan->appends(request()->query())->nextPageUrl() }}"
                                            class="btn btn-success">
                                            <i class="mdi mdi-chevron-right"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Tabungan -->
    <div class="row mt-4">
        <div class="col-md-12">
            <!-- Form Pencarian Umum -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('tabungan.index') }}" class="form-inline">
                        <input type="text" name="search" class="form-control mr-2"
                            placeholder="Cari NISN/Nama Siswa" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary mr-2">Cari</button>
                        @if (request()->has('search') || request()->has('nisn_search'))
                            <a href="{{ route('tabungan.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="card-title mb-0">
                            @if (isset($nisn_search) && $nisn_search)
                                Data Tabungan Siswa: {{ $nisn_search }}
                            @else
                                Data Tabungan Semua Siswa
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>NISN</th>
                                    <th>NAMA SISWA</th>
                                    <th>KELAS</th>
                                    <th>SALDO</th>
                                    <th>TRANSAKSI TERAKHIR</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tabungan as $item)
                                    <tr>
                                        <td>{{ ($tabungan->currentPage() - 1) * $tabungan->perPage() + $loop->iteration }}
                                        </td>
                                        <td>{{ $item->nisn }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
                                        <td>Rp {{ number_format($item->saldo_terakhir, 0, ',', '.') }}</td>
                                        <td>{{ $item->transaksi_terakhir ? $item->transaksi_terakhir->format('d/m/Y') : '-' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('tabungan.cari-siswa', ['nisn' => $item->nisn]) }}"
                                                class="btn btn-sm btn-info" title="Kelola Tabungan">
                                                <i class="mdi mdi-cash"></i>
                                            </a>
                                            <a href="{{ route('tabungan.show', $item->id) }}"
                                                class="btn btn-sm btn-primary" title="Detail Tabungan">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data tabungan ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($tabungan->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $tabungan->appends(request()->query())->previousPageUrl() }}"
                                class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $tabungan->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $tabungan->currentPage() ? 'active' : '' }}"
                                    href="{{ $tabungan->appends(request()->query())->url($i) }}">
                                    {{ $i }}
                                </a>
                            @endfor
                            <a href="{{ $tabungan->appends(request()->query())->nextPageUrl() }}"
                                class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('sweet')
    function confirmDelete(id) {
    Swal.fire({
    title: 'PERINGATAN!',
    text: "Yakin ingin menghapus transaksi tabungan ini?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal',
    }).then((result) => {
    if (result.value) {
    // Cari form yang sesuai dengan ID
    const form = document.querySelector(`form[action="{{ url('dashboard/tabungan') }}/${id}"]`);
    if (form) {
    form.submit();
    }
    }
    })
    }
@endsection

@section('scripts')
    <script>
        function toggleJumlah() {
            const tipe = document.getElementById('tipe').value;
            const labelJumlah = document.getElementById('label_jumlah');
            const saldoInfo = document.getElementById('saldo-info');
            const saldoSiswa = {{ $saldo ?? 0 }};

            if (tipe === 'debit') {
                labelJumlah.textContent = 'Jumlah Setoran';
                saldoInfo.classList.add('d-none');
            } else {
                labelJumlah.textContent = 'Jumlah Penarikan';

                document.getElementById('jumlah').addEventListener('input', function() {
                    if (parseFloat(this.value) > saldoSiswa) {
                        saldoInfo.classList.remove('d-none');
                    } else {
                        saldoInfo.classList.add('d-none');
                    }
                });
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            toggleJumlah();
        });
    </script>
@endsection
