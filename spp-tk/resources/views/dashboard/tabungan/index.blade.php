@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Tabungan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Form Pencarian -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('tabungan.index') }}" class="form-inline">
                        <input type="text" name="search" class="form-control mr-2" placeholder="Cari NISN/Nama Siswa"
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary mr-2">Cari</button>
                        @if(request()->has('search'))
                            <a href="{{ route('tabungan.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">Data Tabungan Siswa</div>

                    <div class="table-responsive mb-3">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>NISN</th>
                                    <th>NAMA SISWA</th>
                                    <th>DEBIT</th>
                                    <th>KREDIT</th>
                                    <th>SALDO</th>
                                    <th>KETERANGAN</th>
                                    <th>TANGGAL</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tabungan as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->siswa->nisn }}</td>
                                    <td>{{ $item->siswa->nama }}</td>
                                    <td class="text-success">Rp {{ number_format($item->debit, 0, ',', '.') }}</td>
                                    <td class="text-danger">Rp {{ number_format($item->kredit, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('tabungan.show', $item->id_siswa) }}" class="btn btn-sm btn-info">
                                            <i class="mdi mdi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $tabungan->appends(request()->query())->links() }}

                    @if(count($tabungan) == 0)
                        <div class="alert alert-warning text-center">
                            Tidak ada data tabungan ditemukan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection