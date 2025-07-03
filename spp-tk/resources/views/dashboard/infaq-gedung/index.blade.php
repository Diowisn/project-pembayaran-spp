@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Infaq Gedung</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Form Tambah -->
        <div class="card">
            <div class="card-body">
                <div class="card-title">Tambah Infaq Gedung</div>

                <form method="post" action="{{ route('infaq-gedung.store') }}">
                    @csrf

                    <div class="form-group">
                        <label>Paket</label>
                        <input type="text" name="paket" maxlength="1" class="form-control @error('paket') is-invalid @enderror" value="{{ old('paket') }}">
                        <span class="text-danger">@error('paket') {{ $message }} @enderror</span>
                    </div>

                    <div class="form-group">
                        <label>Nominal Total</label>
                        <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal') }}">
                        <span class="text-danger">@error('nominal') {{ $message }} @enderror</span>
                    </div>

                    <div class="form-group">
                        <label>Jumlah Angsuran</label>
                        <input type="number" name="jumlah_angsuran" class="form-control @error('jumlah_angsuran') is-invalid @enderror" value="{{ old('jumlah_angsuran', 12) }}">
                        <span class="text-danger">@error('jumlah_angsuran') {{ $message }} @enderror</span>
                    </div>

                    <div class="form-group">
                        <label>Nominal per Angsuran</label>
                        <input type="number" name="nominal_per_angsuran" class="form-control @error('nominal_per_angsuran') is-invalid @enderror" value="{{ old('nominal_per_angsuran') }}">
                        <span class="text-danger">@error('nominal_per_angsuran') {{ $message }} @enderror</span>
                    </div>

                    <button type="submit" class="btn btn-success btn-rounded float-right">
                        <i class="mdi mdi-check"></i> Simpan
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel -->
        <div class="card">
            <div class="card-body">
                <div class="card-title">Data Infaq Gedung</div>
                <div class="table-responsive mb-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Paket</th>
                                <th>Nominal Total</th>
                                <th>Jumlah Angsuran</th>
                                <th>Per Angsuran</th>
                                <th>Dibuat</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($infaq as $index => $item)
                                <tr>
                                    <td>{{ $index + $infaq->firstItem() }}</td>
                                    <td>{{ $item->paket }}</td>
                                    <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                    <td>{{ $item->jumlah_angsuran }}x</td>
                                    <td>Rp {{ number_format($item->nominal_per_angsuran, 0, ',', '.') }}</td>
                                    <td>{{ $item->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="hide-menu">
                                            <a href="javascript:void(0)" class="text-dark" data-toggle="dropdown">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{ route('infaq-gedung.edit', $item->id) }}"><i class="ti-pencil"></i> Edit</a>
                                                <form method="post" action="{{ route('infaq-gedung.destroy', $item->id) }}" id="delete{{ $item->id }}">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="dropdown-item" onclick="deleteData({{ $item->id }})">
                                                        <i class="ti-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">Tidak ada data!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{ $infaq->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('sweet')
<script>
function deleteData(id) {
    Swal.fire({
        title: 'PERINGATAN!',
        text: "Yakin ingin menghapus data Infaq Gedung?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yakin',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#delete'+id).submit();
        }
    });
}
</script>
@endsection
