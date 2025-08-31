@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Siswa</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Data Siswa</div>
                    <a href="{{ url('dashboard/data-siswa/create') }}" class="btn btn-success btn-rounded float-right mb-3">
                        <i class="mdi mdi-plus-circle"></i> {{ __('Tambah Siswa') }}
                    </a>

                    {{-- Form Pencarian Nama / NISN --}}
                    <form method="GET" action="{{ route('data-siswa.index') }}" class="form-inline mb-3">
                        <input type="text" name="search" class="form-control mr-2" placeholder="Cari NISN / Nama"
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary mr-2">Cari</button>

                        @if(request()->has('search'))
                            <a href="{{ route('data-siswa.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>

                    {{-- Form Filter Kelas & Sortir --}}
                    <form method="GET" action="{{ route('data-siswa.index') }}" class="form-inline mb-3">
                        {{-- Kelas --}}
                        <select name="kelas_id" class="form-control mr-2">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($allKelas as $kelasItem)
                                <option value="{{ $kelasItem->id }}" {{ request('kelas_id') == $kelasItem->id ? 'selected' : '' }}>
                                    {{ $kelasItem->nama_kelas }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Sorting --}}
                        <select name="sort_by" class="form-control mr-2">
                            <option value="">-- Urutkan --</option>
                            <option value="nama" {{ request('sort_by') == 'nama' ? 'selected' : '' }}>Nama</option>
                            <option value="nisn" {{ request('sort_by') == 'nisn' ? 'selected' : '' }}>NISN</option>
                        </select>

                        <select name="order" class="form-control mr-2">
                            <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>A-Z</option>
                            <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Z-A</option>
                        </select>
                        
                        {{-- Pilihan jumlah data per halaman --}}
                        <select name="per_page" class="form-control mr-2" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per halaman</option>
                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 per halaman</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per halaman</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per halaman</option>
                        </select>

                        <button type="submit" class="btn btn-success mr-2">Terapkan</button>

                        @if(request()->hasAny(['kelas_id', 'sort_by', 'order', 'per_page']))
                            <a href="{{ route('data-siswa.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </form>

                    <div class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">NISN</th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">KELAS</th>
                                    <th scope="col">TELEPON</th>
                                    <th scope="col">ALAMAT</th>
                                    <th scope="col">SPP</th>
                                    <th scope="col">INFAQ GEDUNG</th>
                                    <th scope="col">INFAQ GEDUNG</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Hitung nomor awal untuk halaman saat ini
                                    $currentPage = $siswa->currentPage();
                                    $perPage = $siswa->perPage();
                                    $startNumber = ($currentPage - 1) * $perPage + 1;
                                @endphp
                                @foreach($siswa as $index => $value)
                                <tr>
                                    <th scope="row">{{ $startNumber + $index }}</th>
                                    <td>{{ $value->nisn }}</td>
                                    <td>{{ $value->nama }}</td>
                                    <td>{{ $value->kelas->nama_kelas }}</td>
                                    <td>{{ $value->nomor_telp }}</td>
                                    <td>{{ Str::limit($value->alamat, 20) }}</td>
                                    <td>
                                        @if($value->spp)
                                            <strong>Rp {{ number_format($value->spp->nominal_spp, 0, ',', '.') }}</strong><br>
                                            <small>
                                                Tahun: {{ $value->spp->tahun }}<br>
                                                @if($value->spp->nominal_konsumsi)
                                                    Konsumsi: Rp {{ number_format($value->spp->nominal_konsumsi, 0, ',', '.') }}<br>
                                                @endif
                                                @if($value->spp->nominal_fullday)
                                                    Fullday: Rp {{ number_format($value->spp->nominal_fullday, 0, ',', '.') }}
                                                @endif
                                            </small>
                                        @else
                                            <em>Tidak Ada</em>
                                        @endif
                                    </td>

                                    <td>
                                        @if($value->infaqGedung)
                                            Paket {{ $value->infaqGedung->paket }}<br>
                                            <small>
                                                {{ $value->infaqGedung->jumlah_angsuran }}x @ Rp {{ number_format($value->infaqGedung->nominal_per_angsuran, 0, ',', '.') }}
                                            </small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ $value->inklusi == 1 ? 'Inklusi' : 'Reguler' }}
                                        @if($value->inklusi == 1 && $value->paketInklusi)
                                            <br>
                                            <small class="text-muted">
                                                {{ $value->paketInklusi->nama_paket }} - 
                                                Rp {{ number_format($value->paketInklusi->nominal, 0, ',', '.') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="hide-menu">
                                            <a href="javascript:void(0)" class="text-dark" id="actiondd" role="button" data-toggle="dropdown">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actiondd">
                                                <a class="dropdown-item" href="{{ url('dashboard/data-siswa/'.$value->id.'/edit') }}">
                                                    <i class="ti-pencil"></i> Edit
                                                </a>
                                                <form method="post" action="{{ url('dashboard/data-siswa', $value->id) }}" id="delete{{ $value->id }}">
                                                    @csrf
                                                    @method('delete')
                                                    
                                                    <button type="button" class="dropdown-item" onclick="deleteData({{ $value->id }})">
                                                        <i class="ti-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($siswa->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $siswa->previousPageUrl() }}" class="btn btn-success {{ $siswa->onFirstPage() ? 'disabled' : '' }}">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for($i = 1; $i <= $siswa->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $siswa->currentPage() ? 'active' : '' }}" href="{{ $siswa->url($i) }}">{{ $i }}</a>
                            @endfor
                            <a href="{{ $siswa->nextPageUrl() }}" class="btn btn-success {{ $siswa->hasMorePages() ? '' : 'disabled' }}">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                    <!-- End Pagination -->
                    
                    @if(count($siswa) == 0)
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
            text: "Yakin ingin menghapus data siswa? Data pembayaran atas nama siswa ini pun akan dihapus jika ada.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yakin',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.value) {
                $('#delete'+id).submit();
            }
        })
    }
@endsection