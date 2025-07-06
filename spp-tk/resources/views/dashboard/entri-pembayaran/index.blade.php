@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Pembayaran</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <div class="card-title">Entri Pembayaran</div>

                    <form method="GET" action="{{ route('pembayaran.cari-siswa') }}">
                        <div class="form-group">
                            <label for="nisn">Cari Berdasarkan NISN</label>
                            <div class="input-group">
                                <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN"
                                    value="{{ old('nisn', $siswa_data->nisn ?? '') }}" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Cari</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if (isset($siswa))
                        <form method="post" action="{{ route('entry-pembayaran.store') }}">
                            @csrf
                            <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                            <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">

                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" class="form-control" value="{{ $siswa->nama ?? '' }}" disabled>
                            </div>

                            <div class="form-group">
                                <label>Nominal SPP</label>
                                <input type="text" class="form-control"
                                    value="{{ isset($siswa) ? 'Rp ' . number_format($siswa->spp->nominal_spp, 0, ',', '.') : '' }}"
                                    disabled>
                                <input type="hidden" name="nominal_spp" value="{{ $siswa->spp->nominal_spp ?? 0 }}">
                            </div>

                            @if (isset($siswa) && $siswa->spp->nominal_konsumsi > 0)
                                <div class="form-group">
                                    <label>Nominal Konsumsi</label>
                                    <input type="text" class="form-control"
                                        value="{{ 'Rp ' . number_format($siswa->spp->nominal_konsumsi, 0, ',', '.') }}"
                                        disabled>
                                    <input type="hidden" name="nominal_konsumsi"
                                        value="{{ $siswa->spp->nominal_konsumsi }}">
                                </div>
                            @endif

                            @if (isset($siswa) && $siswa->spp->nominal_fullday > 0)
                                <div class="form-group">
                                    <label>Nominal Fullday</label>
                                    <input type="text" class="form-control"
                                        value="{{ 'Rp ' . number_format($siswa->spp->nominal_fullday, 0, ',', '.') }}"
                                        disabled>
                                    <input type="hidden" name="nominal_fullday"
                                        value="{{ $siswa->spp->nominal_fullday }}">
                                </div>
                            @endif

                            @if ($siswa)
                                @php
                                    $total_bayar =
                                        $siswa->spp->nominal_spp +
                                        $siswa->spp->nominal_konsumsi +
                                        $siswa->spp->nominal_fullday;
                                @endphp

                                <div class="form-group">
                                    <label>Total Tagihan</label>
                                    <input type="text" class="form-control"
                                        value="{{ 'Rp ' . number_format($total_bayar, 0, ',', '.') }}" readonly>
                                    <input type="hidden" name="jumlah_tagihan" value="{{ $total_bayar }}">
                                </div>

                                <div class="form-group">
                                    <label>Jumlah Uang Dibayarkan</label>
                                    <input type="number"
                                        class="form-control @error('nominal_pembayaran') is-invalid @enderror"
                                        name="nominal_pembayaran" value="{{ old('nominal_pembayaran') }}" required
                                        min="{{ $total_bayar }}">
                                    <span class="text-danger">
                                        @error('nominal_pembayaran')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            @endif

                            <button type="submit" class="btn btn-success btn-rounded float-right">
                                <i class="mdi mdi-check"></i> Simpan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

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
                                    <th scope="col">TOTAL BAYAR</th>
                                    <th scope="col">JUMLAH BAYAR</th>
                                    <th scope="col">SISA</th>
                                    <th scope="col">TANGGAL BAYAR</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($pembayaran as $value)
                                    <tr>
                                        <th scope="row">{{ $i }}</th>
                                        <td>{{ $value->petugas->name ?? 'N/A' }}</td>
                                        <td>{{ $value->siswa->nisn }}</td>
                                        <td>{{ $value->siswa->nama }}</td>
<td>Rp {{ number_format(
    $value->siswa->spp->nominal_spp + 
    ($value->siswa->spp->nominal_konsumsi ?? 0) + 
    ($value->siswa->spp->nominal_fullday ?? 0), 
    0, ',', '.') 
}}</td>
                                        <td>{{ $value->jumlah_bayar }}</td>
                                        <td>{{ $value->kembalian }}</td>
                                        <td>{{ $value->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <div class="hide-menu">
                                                <a href="javascript:void(0)" class="text-dark" id="actiondd" role="button"
                                                    data-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actiondd">
                                                    <a class="dropdown-item"
                                                        href="{{ url('dashboard/pembayaran/' . $value->id . '/edit') }}"><i
                                                            class="ti-pencil"></i> Edit </a>
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

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                    {{-- <! -- Pagination --> --}}
                    @if ($pembayaran->lastPage() != 1)
                        <div class="btn-group float-right">
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
                    <!-- End Pagination -->

                    @if (count($pembayaran) == 0)
                        <div class="text-center">Tidak ada data!</div>
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
    </script>
@endsection
