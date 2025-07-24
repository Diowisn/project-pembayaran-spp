@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tabungan.index') }}">Tabungan</a></li>
    <li class="breadcrumb-item active">Detail Tabungan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            Tabungan {{ $siswa->nama }} (NISN: {{ $siswa->nisn }})
                        </h4>
                        <div>
                            <span class="badge badge-primary p-2">
                                Saldo: Rp {{ number_format($saldo, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('tabungan.report', $siswa->id) }}" class="btn btn-success ml-2">
                                <i class="mdi mdi-file-pdf"></i> Cetak Laporan
                            </a>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#tarikModal">
                        <i class="mdi mdi-bank-minus"></i> Tarik Tabungan
                    </button>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>TANGGAL</th>
                                    <th>DEBIT</th>
                                    <th>KREDIT</th>
                                    <th>SALDO</th>
                                    <th>KETERANGAN</th>
                                    <th>PETUGAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tabungan as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-success">Rp {{ number_format($item->debit, 0, ',', '.') }}</td>
                                    <td class="text-danger">Rp {{ number_format($item->kredit, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->petugas->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $tabungan->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tarik Tabungan -->
    <div class="modal fade" id="tarikModal" tabindex="-1" role="dialog" aria-labelledby="tarikModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tarikModalLabel">Tarik Tabungan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('tabungan.tarik', $siswa->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Jumlah Penarikan</label>
                            <input type="number" name="jumlah" class="form-control" required 
                                   min="1" max="{{ $saldo }}">
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <input type="text" name="keterangan" class="form-control" required
                                   placeholder="Contoh: Penarikan untuk kegiatan study tour">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection