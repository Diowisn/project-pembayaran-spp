@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">SPP</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('Tambah Tarif Pembayaran') }}</div>
                    
<form method="post" action="{{ url('/dashboard/data-spp') }}">
    @csrf
    
    <div class="form-group">
        <label>Tahun</label>
        <input type="number" class="form-control @error('tahun') is-invalid @enderror" 
               name="tahun" value="{{ old('tahun', date('Y')) }}" min="2020" max="2030" required>
        <span class="text-danger">@error('tahun') {{ $message }} @enderror</span>
    </div>
    
<div class="form-group">
    <label>Kelas</label>
    <select class="form-control @error('id_kelas') is-invalid @enderror" name="id_kelas" required>
        <option value="">-- Pilih Kelas --</option>
        @foreach($kelas as $k)
            <option value="{{ $k->id }}" {{ old('id_kelas') == $k->id ? 'selected' : '' }}>
                {{ $k->nama_kelas }}
            </option>
        @endforeach
    </select>
    <span class="text-danger">@error('id_kelas') {{ $message }} @enderror</span>
</div>
    
    <div class="form-group">
        <label>Nominal SPP (Rp)</label>
        <input type="text" class="form-control @error('nominal_spp') is-invalid @enderror" 
               name="nominal_spp" value="{{ old('nominal_spp') }}" id="nominal-spp" required>
        <span class="text-danger">@error('nominal_spp') {{ $message }} @enderror</span>
    </div>
    
    <div class="form-group">
        <label>Nominal Konsumsi (Rp)</label>
        <input type="text" class="form-control @error('nominal_konsumsi') is-invalid @enderror" 
               name="nominal_konsumsi" value="{{ old('nominal_konsumsi') }}" id="nominal-konsumsi">
        <span class="text-danger">@error('nominal_konsumsi') {{ $message }} @enderror</span>
    </div>
    
    <div class="form-group">
        <label>Nominal Fullday (Rp)</label>
        <input type="text" class="form-control @error('nominal_fullday') is-invalid @enderror" 
               name="nominal_fullday" value="{{ old('nominal_fullday') }}" id="nominal-fullday">
        <span class="text-danger">@error('nominal_fullday') {{ $message }} @enderror</span>
    </div>
    
    {{-- <div class="form-group" id="infaq-container">
        <label>Infaq Gedung (Opsional)</label>
        <select class="form-control @error('id_infaq_gedung') is-invalid @enderror" name="id_infaq_gedung">
            <option value="">-- Pilih Paket Infaq --</option>
            @foreach($infaqGedung as $infaq)
                <option value="{{ $infaq->id }}" {{ old('id_infaq_gedung') == $infaq->id ? 'selected' : '' }}>
                    Paket {{ $infaq->paket }} (Rp {{ number_format($infaq->nominal, 0, ',', '.') }})
                </option>
            @endforeach
        </select>
        <span class="text-danger">@error('id_infaq_gedung') {{ $message }} @enderror</span>
    </div> --}}
    
    <button type="submit" class="btn btn-success btn-rounded float-right">
        <i class="mdi mdi-check"></i> Simpan
    </button>
</form>

@section('scripts')
<script>
    // Format input nominal
    function formatRupiahInput(inputId) {
        $(`#${inputId}`).on('keyup', function() {
            let value = $(this).val().replace(/\D/g, '');
            $(this).val(formatRupiah(value));
        });
    }

    function formatRupiah(angka) {
        if (!angka) return '';
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Format semua input nominal
    formatRupiahInput('nominal-spp');
    formatRupiahInput('nominal-konsumsi');
    formatRupiahInput('nominal-fullday');

    // Set nilai default untuk tahun
    document.querySelector('input[name="tahun"]').value = new Date().getFullYear();
</script>
@endsection
                </div>
            </div>     
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Data Tarif Pembayaran</div>
                    
                    <div class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tahun</th>
                                    <th>Jenis Pembayaran</th>
                                    <th>Kelas</th>
                                    <th>Tagihan</th>
                                    <th>Nominal</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
@foreach($spp as $value)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $value->tahun }}</td>
        <td>
            <!-- Jenis Pembayaran -->
            SPP
            @if($value->nominal_konsumsi)
                <br><small class="text-muted">+ Konsumsi</small>
            @endif
            @if($value->nominal_fullday)
                <br><small class="text-muted">+ Fullday</small>
            @endif
        </td>
        <td>{{ $value->kelas->nama_kelas }}</td>
        <td>
            @if($value->kelas->has_konsumsi)
                <span class="badge badge-info">Konsumsi</span>
            @endif
            @if($value->kelas->has_fullday)
                <span class="badge badge-primary">Fullday</span>
            @endif
        </td>
        <td>
            <strong>SPP:</strong> Rp {{ number_format($value->nominal_spp, 0, ',', '.') }}<br>
            @if($value->nominal_konsumsi)
                <strong>Konsumsi:</strong> Rp {{ number_format($value->nominal_konsumsi, 0, ',', '.') }}<br>
            @endif
            @if($value->nominal_fullday)
                <strong>Fullday:</strong> Rp {{ number_format($value->nominal_fullday, 0, ',', '.') }}
            @endif
        </td>
        <td>{{ $value->created_at->format('d M Y') }}</td>
        <td>
            <div class="hide-menu">
                <a href="javascript:void(0)" class="text-dark" id="actiondd" role="button" data-toggle="dropdown">
                    <i class="mdi mdi-dots-vertical"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actiondd">
                    <a class="dropdown-item" href="{{ url('dashboard/data-spp/'.$value->id.'/edit') }}">
                        <i class="ti-pencil"></i> Edit
                    </a>
                    <form method="post" action="{{ url('dashboard/data-spp', $value->id) }}" id="delete{{ $value->id }}">
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
                    @if($spp->lastPage() != 1)
                        <div class="btn-group float-right">
                            <a href="{{ $spp->previousPageUrl() }}" class="btn btn-success {{ $spp->currentPage() == 1 ? 'disabled' : '' }}">
                                <i class="mdi mdi-chevron-left"></i>
                            </a>
                            @for($i = 1; $i <= $spp->lastPage(); $i++)
                                <a class="btn btn-success {{ $i == $spp->currentPage() ? 'active' : '' }}" href="{{ $spp->url($i) }}">{{ $i }}</a>
                            @endfor
                            <a href="{{ $spp->nextPageUrl() }}" class="btn btn-success {{ $spp->currentPage() == $spp->lastPage() ? 'disabled' : '' }}">
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    @endif
                    
                    @if(count($spp) == 0)
                        <div class="text-center">Tidak ada data!</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Tampilkan field paket hanya untuk infaq gedung
    $('select[name="jenis_pembayaran"]').change(function() {
        if ($(this).val() == 'infaq_gedung') {
            $('#paket-container').show();
        } else {
            $('#paket-container').hide();
        }
    });

    // Format input nominal
    $('#nominal-input').on('keyup', function() {
        let value = $(this).val().replace(/\D/g, '');
        $(this).val(formatRupiah(value));
    });

    function formatRupiah(angka) {
        if (!angka) return '';
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Jika ada error, pastikan field paket ditampilkan
    @if(old('jenis_pembayaran') == 'infaq_gedung')
        $('#paket-container').show();
    @endif
</script>
@endsection

@section('sweet')
function deleteData(id){
    Swal.fire({
        title: 'PERINGATAN!',
        text: "Yakin ingin menghapus data tarif pembayaran?",
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