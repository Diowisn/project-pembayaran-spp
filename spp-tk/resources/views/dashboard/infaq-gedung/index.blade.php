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
    <label for="paket_input">Paket</label>
    <input type="text" id="paket_input" name="paket" maxlength="1"
        class="form-control @error('paket') is-invalid @enderror" value="{{ old('paket') }}">
    @error('paket')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="nominal_input">Nominal Total</label>
    <input type="number" id="nominal_input" name="nominal" 
        class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal') }}">
    @error('nominal')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="jumlah_angsuran_input">Jumlah Angsuran</label>
    <input type="number" id="jumlah_angsuran_input" name="jumlah_angsuran"
        class="form-control @error('jumlah_angsuran') is-invalid @enderror"
        value="{{ old('jumlah_angsuran', 12) }}">
    @error('jumlah_angsuran')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="nominal_per_angsuran_input">Nominal per Angsuran</label>
    <input type="number" id="nominal_per_angsuran_input" name="nominal_per_angsuran"
        class="form-control @error('nominal_per_angsuran') is-invalid @enderror"
        value="{{ old('nominal_per_angsuran') }}">
    @error('nominal_per_angsuran')
        <span class="text-danger">{{ $message }}</span>
    @enderror
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
        <a href="javascript:void(0)" class="text-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-dots-vertical"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route('infaq-gedung.edit', $item->id) }}">
                <i class="ti-pencil"></i> Edit
            </a>

            @if (auth()->user()->level == 'admin')
                <form method="POST" action="{{ route('infaq-gedung.destroy', $item->id) }}" id="delete-form-{{ $item->id }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="dropdown-item" onclick="handleDelete({{ $item->id }}, event)">
                        <i class="ti-trash"></i> Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>
</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data!</td>
                                    </tr>
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
{{-- <script> --}}
    function handleDelete(id, event) {
        event.preventDefault();
        event.stopPropagation();
        
        // Pastikan form yang benar ditemukan
        const form = document.getElementById(`delete-form-${id}`);
        
        if (!form) {
            console.error(`Form with ID delete-form-${id} not found`);
            return;
        }

        // Debug: Tampilkan action form
        console.log('Form action:', form.action);
        
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
                // Submit form secara langsung
                form.submit();
            }
        });
    }
{{-- </script> --}}
@endsection