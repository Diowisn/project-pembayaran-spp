@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Paket Inklusi</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Form Tambah -->
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Tambah Paket Inklusi</div>

                    <form method="post" action="{{ route('data-inklusi.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="nama_paket_input">Nama Paket</label>
                            <input type="text" id="nama_paket_input" name="nama_paket" maxlength="50"
                                class="form-control @error('nama_paket') is-invalid @enderror"
                                value="{{ old('nama_paket') }}" placeholder="Contoh: Paket A, Paket Khusus, dll">
                            @error('nama_paket')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nominal_input">Nominal</label>
                            <input type="number" id="nominal_input" name="nominal"
                                class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal') }}"
                                placeholder="Masukkan nominal">
                            @error('nominal')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="keterangan_input">Keterangan (Opsional)</label>
                            <textarea id="keterangan_input" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                rows="3" placeholder="Deskripsi paket inklusi">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
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
                    <div class="card-title">Data Paket Inklusi</div>
                    <div class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Paket</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                    <th>Dibuat</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inklusi as $index => $item)
                                    <tr>
                                        <td>{{ $index + $inklusi->firstItem() }}</td>
                                        <td>{{ $item->nama_paket }}</td>
                                        <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                        <td>{{ $item->keterangan ?? '-' }}</td>
                                        <td>{{ $item->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="hide-menu">
                                                <a href="javascript:void(0)" class="text-dark dropdown-toggle"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item"
                                                        href="{{ route('data-inklusi.edit', $item->id) }}">
                                                        <i class="ti-pencil"></i> Edit
                                                    </a>

                                                    @if (auth()->user()->level == 'admin')
                                                        <form method="POST"
                                                            action="{{ route('data-inklusi.destroy', $item->id) }}"
                                                            id="delete{{ $item->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item"
                                                                onclick="deleteInklusi({{ $item->id }})">
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
                                        <td colspan="6" class="text-center">Tidak ada data!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{ $inklusi->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('sweet')
    {{-- <script> --}}
        function deleteInklusi(id) {
            Swal.fire({
                title: 'PERINGATAN!',
                text: "Yakin ingin menghapus data Paket Inklusi?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yakin',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    document.getElementById('delete' + id).submit();
                }
            })
        }
    {{-- </script> --}}
@endsection
