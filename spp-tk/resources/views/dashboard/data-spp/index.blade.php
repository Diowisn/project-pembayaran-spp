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
                            <input type="number" class="form-control @error('tahun') is-invalid @enderror" name="tahun" value="{{ old('tahun') }}" min="2020" max="2030">
                            <span class="text-danger">@error('tahun') {{ $message }} @enderror</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Jenis Pembayaran</label>
                            <select class="form-control @error('jenis_pembayaran') is-invalid @enderror" name="jenis_pembayaran">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="infaq_gedung" {{ old('jenis_pembayaran') == 'infaq_gedung' ? 'selected' : '' }}>Infaq Gedung</option>
                                <option value="spp" {{ old('jenis_pembayaran') == 'spp' ? 'selected' : '' }}>SPP</option>
                                <option value="konsumsi" {{ old('jenis_pembayaran') == 'konsumsi' ? 'selected' : '' }}>Konsumsi</option>
                                <option value="fullday" {{ old('jenis_pembayaran') == 'fullday' ? 'selected' : '' }}>Fullday + Nutrisi</option>
                            </select>
                            <span class="text-danger">@error('jenis_pembayaran') {{ $message }} @enderror</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Kelas</label>
                            <select class="form-control @error('kelas') is-invalid @enderror" name="kelas">
                                <option value="">-- Pilih Kelas --</option>
                                <option value="TPA" {{ old('kelas') == 'TPA' ? 'selected' : '' }}>TPA</option>
                                <option value="KBT" {{ old('kelas') == 'KBT' ? 'selected' : '' }}>KBT</option>
                                <option value="TK A" {{ old('kelas') == 'TK A' ? 'selected' : '' }}>TK A</option>
                                <option value="TK B" {{ old('kelas') == 'TK B' ? 'selected' : '' }}>TK B</option>
                                <option value="ALL" {{ old('kelas') == 'ALL' ? 'selected' : '' }}>Semua Kelas (Infaq Gedung)</option>
                            </select>
                            <span class="text-danger">@error('kelas') {{ $message }} @enderror</span>
                        </div>
                        
                        <div class="form-group" id="paket-container" style="display: none;">
                            <label>Paket Infaq</label>
                            <select class="form-control" name="paket">
                                <option value="A" {{ old('paket') == 'A' ? 'selected' : '' }}>Paket A (Rp 1.500.000)</option>
                                <option value="B" {{ old('paket') == 'B' ? 'selected' : '' }}>Paket B (Rp 1.000.000)</option>
                                <option value="C" {{ old('paket') == 'C' ? 'selected' : '' }}>Paket C (Rp 800.000)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Nominal (Rp)</label>
                            <input type="text" class="form-control @error('nominal') is-invalid @enderror" name="nominal" value="{{ old('nominal') }}" id="nominal-input">
                            <span class="text-danger">@error('nominal') {{ $message }} @enderror</span>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-rounded float-right">
                            <i class="mdi mdi-check"></i> Simpan
                        </button>
                    </form>
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
                                    <th>Paket</th>
                                    <th>Nominal</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @foreach($spp as $value)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value->tahun }}</td>
                                        <td>
                                            @if($value->jenis_pembayaran == 'infaq_gedung')
                                                Infaq Gedung
                                            @elseif($value->jenis_pembayaran == 'spp')
                                                SPP
                                            @elseif($value->jenis_pembayaran == 'konsumsi')
                                                Konsumsi
                                            @else
                                                Fullday + Nutrisi
                                            @endif
                                        </td>
                                        <td>{{ $value->kelas }}</td>
                                        <td>{{ $value->paket ?? '-' }}</td>
                                        <td>Rp {{ number_format($value->nominal, 0, ',', '.') }}</td>
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
                                    @php $i++; @endphp
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