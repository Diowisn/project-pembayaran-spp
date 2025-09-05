@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Pembayaran</li>
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
                    <div class="card-title">Entri Pembayaran SPP</div>

                    <form method="GET" action="{{ route('pembayaran.cari-siswa') }}">
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
                                                <strong>Status SPP Bulan Ini:</strong>
                                                @if ($sudah_bayar_bulan_ini)
                                                    <span class="badge badge-success">SUDAH BAYAR</span>
                                                @else
                                                    <span class="badge badge-warning">BELUM BAYAR</span>
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <a href="{{ route('pembayaran.rekap-siswa', $siswa->id) }}" 
                                                class="btn btn-info" target="_blank">
                                                    <i class="mdi mdi-file-pdf"></i> Print Rekap
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Total -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6>Tagihan Per Bulan</h6>
                                        <h4>Rp {{ number_format($tagihan_per_bulan, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6>Total Tagihan Tahunan</h6>
                                        <h4>Rp {{ number_format($total_tagihan, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6>Total Dibayar</h6>
                                        <h4>Rp {{ number_format($total_dibayar, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card {{ $sisa_pembayaran > 0 ? 'bg-warning' : 'bg-secondary' }} text-white">
                                    <div class="card-body text-center">
                                        <h6>Sisa Pembayaran</h6>
                                        <h4>Rp {{ number_format($sisa_pembayaran, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Pembayaran SPP -->
                        @if ($show_payment_form ?? true)
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Pembayaran SPP</h5>

                                    <form method="post" action="{{ route('entry-pembayaran.store') }}">
                                        @csrf
                                        <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                                        <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Nominal SPP</label>
                                                    <input type="text" class="form-control"
                                                        value="Rp {{ number_format($siswa->spp->nominal_spp, 0, ',', '.') }}"
                                                        disabled>
                                                    <input type="hidden" name="nominal_spp"
                                                        value="{{ $siswa->spp->nominal_spp ?? 0 }}">
                                                </div>
                                            </div>

                                            @if (isset($siswa) && $siswa->spp->nominal_konsumsi > 0)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Nominal Konsumsi</label>
                                                        <input type="text" class="form-control"
                                                            value="Rp {{ number_format($siswa->spp->nominal_konsumsi, 0, ',', '.') }}"
                                                            disabled>
                                                        <input type="hidden" name="nominal_konsumsi_awal"
                                                            value="{{ $siswa->spp->nominal_konsumsi }}">
                                                        <input type="number" name="nominal_konsumsi"
                                                            class="form-control @error('nominal_konsumsi') is-invalid @enderror"
                                                            value="{{ old('nominal_konsumsi', $siswa->spp->nominal_konsumsi) }}"
                                                            min="0" max="{{ $siswa->spp->nominal_konsumsi }}"
                                                            onchange="hitungTotal()">
                                                        <small class="text-muted">Bisa dikurangi jika ada
                                                            pengembalian</small>
                                                        <span class="text-danger">
                                                            @error('nominal_konsumsi')
                                                                {{ $message }}
                                                            @enderror
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if (isset($siswa) && $siswa->spp->nominal_fullday > 0)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Nominal Fullday</label>
                                                        <input type="text" class="form-control"
                                                            value="Rp {{ number_format($siswa->spp->nominal_fullday, 0, ',', '.') }}"
                                                            disabled>
                                                        <input type="hidden" name="nominal_fullday"
                                                            value="{{ $siswa->spp->nominal_fullday }}">
                                                    </div>
                                                </div>
                                            @endif

                                            @if (isset($siswa) && $siswa->inklusi && $siswa->paketInklusi)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Nominal Inklusi</label>
                                                        <input type="text" class="form-control"
                                                            value="Rp {{ number_format($siswa->paketInklusi->nominal, 0, ',', '.') }}"
                                                            disabled>
                                                        <input type="hidden" name="nominal_inklusi"
                                                            value="{{ $siswa->paketInklusi->nominal }}">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        @php
                                            $nominal_inklusi = 0;
                                            if ($siswa->inklusi && $siswa->paketInklusi) {
                                                $nominal_inklusi = $siswa->paketInklusi->nominal;
                                            }

                                            $nominal_konsumsi = old(
                                                'nominal_konsumsi',
                                                $siswa->spp->nominal_konsumsi ?? 0,
                                            );

                                            $total_bayar =
                                                $siswa->spp->nominal_spp +
                                                $nominal_konsumsi +
                                                ($siswa->spp->nominal_fullday ?? 0) +
                                                $nominal_inklusi;
                                        @endphp

                                        <div class="form-group">
                                            <label>Total Tagihan</label>
                                            <input type="text" class="form-control" id="total_tagihan"
                                                value="Rp {{ number_format($total_bayar, 0, ',', '.') }}" readonly>
                                            <input type="hidden" name="jumlah_tagihan" id="jumlah_tagihan"
                                                value="{{ $total_bayar }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Bulan</label>
                                            <select class="form-control @error('bulan') is-invalid @enderror"
                                                name="bulan" required>
                                                <option value="">Pilih Bulan</option>
                                                @php
                                                    $bulanSekarang = strtolower(date('F'));
                                                    $tahunSekarang = date('Y');
                                                @endphp
                                                @foreach ([
                                                    'januari' => 'Januari',
                                                    'februari' => 'Februari',
                                                    'maret' => 'Maret',
                                                    'april' => 'April',
                                                    'mei' => 'Mei',
                                                    'juni' => 'Juni',
                                                    'juli' => 'Juli',
                                                    'agustus' => 'Agustus',
                                                    'september' => 'September',
                                                    'oktober' => 'Oktober',
                                                    'november' => 'November',
                                                    'desember' => 'Desember',
                                                ] as $key => $bulan)
                                                    @php
                                                        // Cek apakah bulan ini sudah dibayar
                                                        $sudahDibayar = $riwayat_pembayaran->contains(function (
                                                            $item,
                                                        ) use ($key, $tahunSekarang) {
                                                            return $item->bulan == $key &&
                                                                $item->tahun == $tahunSekarang;
                                                        });
                                                    @endphp
                                                    <option value="{{ $key }}"
                                                        {{ $key == $bulanSekarang ? 'selected' : '' }}
                                                        {{ $sudahDibayar ? 'disabled' : '' }}>
                                                        {{ $bulan }} {{ $tahunSekarang }}
                                                        @if ($sudahDibayar)
                                                            - (Sudah Dibayar)
                                                        @endif
                                                    </option>
                                                @endforeach

                                                <!-- Opsi untuk tahun sebelumnya -->
                                                @for ($tahun = $tahunSekarang - 1; $tahun >= $tahunSekarang - 3; $tahun--)
                                                    @foreach ([
                                                        'januari' => 'Januari',
                                                        'februari' => 'Februari',
                                                        'maret' => 'Maret',
                                                        'april' => 'April',
                                                        'mei' => 'Mei',
                                                        'juni' => 'Juni',
                                                        'juli' => 'Juli',
                                                        'agustus' => 'Agustus',
                                                        'september' => 'September',
                                                        'oktober' => 'Oktober',
                                                        'november' => 'November',
                                                        'desember' => 'Desember',
                                                    ] as $key => $bulan)
                                                        @php
                                                            $sudahDibayar = $riwayat_pembayaran->contains(function (
                                                                $item,
                                                            ) use ($key, $tahun) {
                                                                return $item->bulan == $key && $item->tahun == $tahun;
                                                            });
                                                        @endphp
                                                        <option value="{{ $key }}"
                                                            data-tahun="{{ $tahun }}"
                                                            {{ $sudahDibayar ? 'disabled' : '' }}>
                                                            {{ $bulan }} {{ $tahun }}
                                                            @if ($sudahDibayar)
                                                                - (Sudah Dibayar)
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                @endfor
                                            </select>
                                            <input type="hidden" name="tahun" id="tahun_pembayaran"
                                                value="{{ $tahunSekarang }}">
                                            <span class="text-danger">
                                                @error('bulan')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>

                                        <div class="form-group">
                                            <label>Jumlah Uang Dibayarkan</label>
                                            <input type="number"
                                                class="form-control @error('nominal_pembayaran') is-invalid @enderror"
                                                name="nominal_pembayaran" id="nominal_pembayaran"
                                                value="{{ old('nominal_pembayaran', $total_bayar) }}" required
                                                oninput="hitungKembalian({{ $total_bayar }}, this.value)">
                                            <small class="text-muted">Pembayaran harus sesuai dengan nominal atau lebih
                                                dari nominal</small>
                                            <span class="text-danger">
                                                @error('nominal_pembayaran')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>

                                        <div id="info_pembayaran" class="mt-2 p-2 bg-light rounded">
                                            <small>
                                                <div id="status_pembayaran">
                                                    <span id="status_text">Pembayaran sesuai tagihan</span>
                                                </div>
                                                <div id="kembalian" class="text-success d-none">
                                                    Kembalian: <span id="jumlah_kembalian">0</span>
                                                </div>
                                            </small>
                                        </div>

                                        <button type="submit" class="btn btn-success btn-rounded float-right mt-3">
                                            <i class="mdi mdi-check"></i> Simpan Pembayaran
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if ($sudah_bayar_bulan_ini)
                            <div class="alert alert-info">
                                <i class="mdi mdi-information"></i>
                                Siswa ini sudah melakukan pembayaran SPP untuk bulan ini pada
                                {{ $pembayaran_bulan_ini->created_at->format('d F Y') }}.
                                Namun Anda masih dapat melakukan pembayaran untuk bulan lainnya.
                            </div>
                        @endif

                        <!-- Riwayat Pembayaran -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Riwayat Pembayaran SPP</h5>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Bulan/Tahun</th>
                                                <th>Nominal SPP</th>
                                                <th>Konsumsi</th>
                                                <th>Fullday</th>
                                                <th>Inklusi</th>
                                                <th>Total Tagihan</th>
                                                <th>Jumlah Bayar</th>
                                                <th>Kembalian</th>
                                                <th>Status</th>
                                                <th>Tanggal Bayar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($riwayat_pembayaran as $riwayat)
                                                <tr>
                                                    <td>{{ ucfirst($riwayat->bulan) }}/{{ $riwayat->tahun }}</td>
                                                    <td>Rp {{ number_format($riwayat->nominal_spp, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($riwayat->nominal_konsumsi, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp {{ number_format($riwayat->nominal_fullday, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($riwayat->nominal_inklusi, 0, ',', '.') }}</td>
                                                    <td>Rp
                                                        {{ number_format($riwayat->nominal_spp + $riwayat->nominal_konsumsi + $riwayat->nominal_fullday + $riwayat->nominal_inklusi, 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp {{ number_format($riwayat->jumlah_bayar, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($riwayat->kembalian, 0, ',', '.') }}</td>
                                                    <td>
                                                        @if ($riwayat->is_lunas)
                                                            <span class="badge badge-success">LUNAS</span>
                                                        @else
                                                            <span class="badge badge-warning">BELUM LUNAS</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $riwayat->created_at->format('d/m/Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
            <!-- Form Pencarian -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('entry-pembayaran.index') }}" class="form-inline">
                        <input type="text" name="search" class="form-control mr-2"
                            placeholder="Cari NISN / Nama Siswa" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary mr-2">Cari</button>

                        @if (request()->has('search'))
                            <a href="{{ route('entry-pembayaran.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">Data Pembayaran</div>

                    <div class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">PETUGAS</th>
                                    <th scope="col">NISN SISWA</th>
                                    <th scope="col">NAMA SISWA</th>
                                    <th scope="col">
                                        <a href="{{ route('entry-pembayaran.index', [
                                            'search' => request('search'),
                                            'sort_by' => 'jumlah_bayar',
                                            'order' => request('sort_by') == 'jumlah_bayar' && request('order') == 'asc' ? 'desc' : 'asc',
                                        ]) }}"
                                            class="text-dark">
                                            TOTAL BAYAR
                                            @if (request('sort_by') == 'jumlah_bayar')
                                                <i
                                                    class="mdi mdi-chevron-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col">JUMLAH BAYAR</th>
                                    <th scope="col">SISA</th>
                                    <th scope="col">BULAN</th>
                                    <th scope="col">STATUS</th>
                                    <th scope="col">
                                        <a href="{{ route('entry-pembayaran.index', [
                                            'search' => request('search'),
                                            'sort_by' => 'created_at',
                                            'order' => request('sort_by') == 'created_at' && request('order') == 'asc' ? 'desc' : 'asc',
                                        ]) }}"
                                            class="text-dark">
                                            TANGGAL BAYAR
                                            @if (request('sort_by') == 'created_at')
                                                <i
                                                    class="mdi mdi-chevron-{{ request('order') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = ($pembayaran->currentPage() - 1) * $pembayaran->perPage() + 1;
                                @endphp
                                @forelse ($pembayaran as $value)
                                    <tr>
                                        <th scope="row">{{ $i }}</th>
                                        <td>{{ $value->petugas->name ?? 'N/A' }}</td>
                                        <td>{{ $value->siswa->nisn }}</td>
                                        <td>{{ $value->siswa->nama }}</td>
                                        <td>Rp
                                            {{ number_format(
                                                $value->nominal_spp +
                                                    ($value->nominal_konsumsi ?? 0) +
                                                    ($value->nominal_fullday ?? 0) +
                                                    ($value->nominal_inklusi ?? 0),
                                                0,
                                                ',',
                                                '.',
                                            ) }}
                                        </td>
                                        <td>Rp {{ number_format($value->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($value->kembalian, 0, ',', '.') }}</td>
                                        <td> {{ ucfirst($value->bulan) }}</td>
                                        <td>
                                            @php
                                                $total_tagihan =
                                                    $value->nominal_spp +
                                                    ($value->nominal_konsumsi ?? 0) +
                                                    ($value->nominal_fullday ?? 0) +
                                                    ($value->nominal_inklusi ?? 0);
                                                $kekurangan = max(0, $total_tagihan - $value->jumlah_bayar);
                                                $kelebihan = max(0, $value->jumlah_bayar - $total_tagihan);
                                            @endphp

                                            @if ($value->is_lunas)
                                                @if ($kelebihan > 0)
                                                    <span class="badge badge-info">Lunas</span>
                                                @else
                                                    <span class="badge badge-success">Lunas</span>
                                                @endif
                                            @else
                                                <span class="badge badge-warning">Belum Lunas</span>
                                                <br>
                                                <small class="text-danger">Kurang: Rp
                                                    {{ number_format($kekurangan, 0, ',', '.') }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $value->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <div class="hide-menu">
                                                <a href="javascript:void(0)" class="text-dark" id="actiondd"
                                                    role="button" data-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actiondd">

                                                    <a class="dropdown-item"
                                                        href="{{ url('dashboard/pembayaran/' . $value->id . '/edit') }}">
                                                        <i class="ti-pencil"></i> Edit
                                                    </a>

                                                    <a class="dropdown-item"
                                                        href="{{ route('pembayaran.generate-pdf', $value->id) }}"
                                                        target="_blank">
                                                        <i class="ti-printer"></i> Print
                                                    </a>

                                                    @if (auth()->user()->level == 'admin')
                                                        <form method="post"
                                                            action="{{ url('dashboard/pembayaran', $value->id) }}"
                                                            id="delete{{ $value->id }}">
                                                            @csrf
                                                            @method('delete')

                                                            <button type="button" class="dropdown-item"
                                                                onclick="deleteData({{ $value->id }})">
                                                                <i class="ti-trash"></i> Hapus
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">Tidak ada data pembayaran!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($pembayaran->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $pembayaran->appends(request()->query())->previousPageUrl() }}"
                                class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $pembayaran->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $pembayaran->currentPage() ? 'active' : '' }}"
                                    href="{{ $pembayaran->appends(request()->query())->url($i) }}">
                                    {{ $i }}
                                </a>
                            @endfor
                            <a href="{{ $pembayaran->appends(request()->query())->nextPageUrl() }}"
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
    function deleteData(id) {
    Swal.fire({
    title: 'PERINGATAN!',
    text: "Yakin ingin menghapus data SPP?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yakin',
    cancelButtonText: 'Batal',
    }).then((result) => {
    if (result.value) {
    $('#delete' + id).submit();
    }
    })
    }
@endsection

@section('scripts')
    <script>
        function searchStudent() {
            const nisn = document.getElementById('nisn_input').value;
            const form = document.getElementById('searchForm');
            form.action = "{{ route('pembayaran.cari-siswa', ['nisn' => '']) }}/" + nisn;
            form.submit();
        }

        function hitungTotal() {
            const nominalSpp = parseFloat(document.querySelector('input[name="nominal_spp"]').value) || 0;
            const nominalKonsumsi = parseFloat(document.querySelector('input[name="nominal_konsumsi"]').value) || 0;
            const nominalFullday = parseFloat(document.querySelector('input[name="nominal_fullday"]').value) || 0;
            const nominalInklusi = parseFloat(document.querySelector('input[name="nominal_inklusi"]').value) || 0;

            const totalTagihan = nominalSpp + nominalKonsumsi + nominalFullday + nominalInklusi;

            document.getElementById('total_tagihan').value = 'Rp ' + totalTagihan.toLocaleString('id-ID');
            document.getElementById('jumlah_tagihan').value = totalTagihan;

            // Update minimum payment
            const nominalPembayaran = document.getElementById('nominal_pembayaran');
            nominalPembayaran.min = totalTagihan;

            if (parseFloat(nominalPembayaran.value) < totalTagihan) {
                nominalPembayaran.value = totalTagihan;
            }

            // Hitung kembalian
            hitungKembalian(totalTagihan, nominalPembayaran.value);
        }

        function hitungKembalian(tagihan, bayar) {
            const jumlahBayar = parseFloat(bayar) || 0;
            const jumlahTagihan = parseFloat(tagihan);

            const kembalian = Math.max(0, jumlahBayar - jumlahTagihan);
            const kekurangan = Math.max(0, jumlahTagihan - jumlahBayar);

            // Update tampilan kembalian
            const kembalianElement = document.getElementById('jumlah_kembalian');
            kembalianElement.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');

            // Update status pembayaran
            const statusElement = document.getElementById('status_text');
            const containerKembalian = document.getElementById('kembalian');

            if (kembalian > 0) {
                statusElement.textContent = 'Pembayaran lebih: Rp ' + kembalian.toLocaleString('id-ID') +
                    ' akan masuk ke tabungan';
                statusElement.className = 'text-success';
                containerKembalian.classList.remove('d-none');
            } else if (kekurangan > 0) {
                statusElement.textContent = 'Pembayaran kurang: Rp ' + kekurangan.toLocaleString('id-ID');
                statusElement.className = 'text-danger';
                containerKembalian.classList.add('d-none');
            } else {
                statusElement.textContent = 'Pembayaran sesuai tagihan';
                statusElement.className = 'text-info';
                containerKembalian.classList.add('d-none');
            }
        }

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            hitungTotal();

            // Event listener untuk input konsumsi
            const konsumsiInput = document.querySelector('input[name="nominal_konsumsi"]');
            if (konsumsiInput) {
                konsumsiInput.addEventListener('input', hitungTotal);
            }

            // Event listener untuk input pembayaran
            const nominalPembayaran = document.getElementById('nominal_pembayaran');
            if (nominalPembayaran) {
                nominalPembayaran.addEventListener('input', function() {
                    const totalTagihan = parseFloat(document.getElementById('jumlah_tagihan').value);
                    hitungKembalian(totalTagihan, this.value);
                });
            }
        });

        // Handler untuk perubahan bulan (untuk menangani tahun)
        const selectBulan = document.querySelector('select[name="bulan"]');
        const inputTahun = document.getElementById('tahun_pembayaran');

        if (selectBulan && inputTahun) {
            selectBulan.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const tahun = selectedOption.getAttribute('data-tahun');
                if (tahun) {
                    inputTahun.value = tahun;
                } else {
                    inputTahun.value = {{ date('Y') }};
                }
            });
        }
    </script>
@endsection
