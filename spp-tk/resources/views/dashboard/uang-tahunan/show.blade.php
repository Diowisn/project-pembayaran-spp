@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Uang Kegiatan Tahunan</li>
    <li class="breadcrumb-item active">{{ $siswa->nama }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Form Filter Tahun -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('uang-tahunan.show', $siswa->id) }}" class="form-inline">
                        <select name="tahun" class="form-control mr-2">
                            @foreach($tahunAjaran as $tahunOption)
                                <option value="{{ $tahunOption }}" {{ $tahun == $tahunOption ? 'selected' : '' }}>
                                    {{ $tahunOption }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary mr-2">Filter</button>
                        <a href="{{ route('uang-tahunan.show', ['id' => $siswa->id, 'tahun' => $tahun]) }}" 
                           class="btn btn-secondary mr-2">Refresh</a>
                        <a href="{{ route('uang-tahunan.report', ['id' => $siswa->id, 'tahun' => $tahun]) }}" 
                           class="btn btn-success" target="_blank">
                            <i class="mdi mdi-file-pdf"></i> Cetak Laporan
                        </a>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="card-title mb-0">
                            Detail Uang Kegiatan Tahunan - {{ $siswa->nama }} ({{ $siswa->nisn }})
                            <br>
                            <small class="text-muted">Tahun Ajaran: {{ $tahun }}</small>
                        </div>
                        <div class="saldo-info">
                            <h4 class="mb-0">Saldo: Rp {{ number_format($saldo, 0, ',', '.') }}</h4>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#tarikModal">
                            <i class="mdi mdi-cash-minus"></i> Penarikan
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>DEBIT</th>
                                    <th>KREDIT</th>
                                    <th>SALDO</th>
                                    <th>KETERANGAN</th>
                                    <th>PETUGAS</th>
                                    <th>TANGGAL</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($uangTahunan as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-success">Rp {{ number_format($item->debit, 0, ',', '.') }}</td>
                                        <td class="text-danger">Rp {{ number_format($item->kredit, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>{{ $item->petugas->name }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('uang-tahunan.edit', $item->id) }}"
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            @if (auth()->user()->level == 'admin')
                                                <form method="post" action="{{ route('uang-tahunan.destroy', $item->id) }}"
                                                    id="delete{{ $item->id }}" style="display: inline">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="deleteData({{ $item->id }})" title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($uangTahunan->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $uangTahunan->appends(['tahun' => $tahun])->previousPageUrl() }}"
                                class="btn btn-success">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for ($i = 1; $i <= $uangTahunan->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $uangTahunan->currentPage() ? 'active' : '' }}"
                                    href="{{ $uangTahunan->appends(['tahun' => $tahun])->url($i) }}">
                                    {{ $i }}
                                </a>
                            @endfor
                            <a href="{{ $uangTahunan->appends(['tahun' => $tahun])->nextPageUrl() }}" class="btn btn-success">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                    <!-- End Pagination -->

                    @if (count($uangTahunan) == 0)
                        <div class="alert alert-warning text-center">
                            Tidak ada transaksi uang kegiatan tahunan ditemukan untuk tahun ini
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Penarikan -->
    <div class="modal fade" id="tarikModal" tabindex="-1" role="dialog" aria-labelledby="tarikModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tarikModalLabel">Penarikan Uang Kegiatan Tahunan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('uang-tahunan.tarik', $siswa->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="tahun_ajaran">Tahun Ajaran</label>
                            <input type="number" class="form-control" id="tahun_ajaran" name="tahun_ajaran" 
                                   value="{{ $tahun }}" min="2000" max="2100" required>
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah Penarikan</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" 
                                   min="1" max="{{ $saldo }}" required>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Proses Penarikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('sweet')
    function deleteData(id) {
    Swal.fire({
    title: 'PERINGATAN!',
    text: "Yakin ingin menghapus transaksi uang kegiatan tahunan?",
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