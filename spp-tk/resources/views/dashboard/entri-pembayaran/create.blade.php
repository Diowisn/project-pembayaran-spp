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
                
                @if(!isset($siswa))
                <!-- Form Pencarian Siswa -->
                <form method="post" action="{{ route('pembayaran.cari-siswa') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Masukkan NISN Siswa</label>
                                <input type="text" name="nisn" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary mt-4">
                                <i class="mdi mdi-magnify"></i> Cari Siswa
                            </button>
                        </div>
                    </div>
                </form>
                @else
                <!-- Form Pembayaran -->
                <form method="post" action="{{ route('entry-pembayaran.store') }}">
                    @csrf
                    <input type="hidden" name="id_siswa" value="{{ $siswa->id }}">
                    <input type="hidden" name="nisn" value="{{ $nisn }}">
                    <input type="hidden" name="jumlah_tagihan" value="{{ $siswa->spp->nominal_spp + ($siswa->spp->nominal_konsumsi ?? 0) + ($siswa->spp->nominal_fullday ?? 0) }}">
                    <input type="hidden" name="nominal_spp" value="{{ $siswa->spp->nominal_spp }}">
                    <input type="hidden" name="nominal_konsumsi" value="{{ $siswa->spp->nominal_konsumsi ?? 0 }}">
                    <input type="hidden" name="nominal_fullday" value="{{ $siswa->spp->nominal_fullday ?? 0 }}">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>NISN</label>
                                <input type="text" class="form-control" value="{{ $nisn }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Siswa</label>
                                <input type="text" class="form-control" value="{{ $siswa->nama }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>SPP</label>
                                <input type="text" class="form-control" value="Rp {{ number_format($siswa->spp->nominal_spp, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                        @if($siswa->spp->nominal_konsumsi)
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Konsumsi</label>
                                <input type="text" class="form-control" value="Rp {{ number_format($siswa->spp->nominal_konsumsi, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                        @endif
                        @if($siswa->spp->nominal_fullday)
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fullday</label>
                                <input type="text" class="form-control" value="Rp {{ number_format($siswa->spp->nominal_fullday, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total Tagihan</label>
                                <input type="text" class="form-control" 
                                    value="Rp {{ number_format($siswa->spp->nominal_spp) + ($siswa->spp->nominal_konsumsi ?? 0) + ($siswa->spp->nominal_fullday ?? 0), 0, ',', '.' }}" 
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nominal Pembayaran</label>
                                <input type="number" name="nominal_pembayaran" class="form-control" 
                                    min="{{ $siswa->spp->nominal_spp + ($siswa->spp->nominal_konsumsi ?? 0) + ($siswa->spp->nominal_fullday ?? 0) }}" 
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('entry-pembayaran.create') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-check"></i> Simpan Pembayaran
                        </button>
                    </div>
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
                                    <th scope="col">SPP</th>
                                    <th scope="col">JUMLAH BAYAR</th>
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
                                        <td>{{ $value->siswa->spp->nominal }}</td>
                                        <td>{{ $value->jumlah_bayar }}</td>
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
$(document).ready(function() {
    $('#nisn').on('blur', function() {
        let nisn = $(this).val().trim();
        if (!nisn) return;

        $.ajax({
            url: '/dashboard/pembayaran/' + nisn,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (!res.data) {
                    alert('Siswa tidak ditemukan');
                    return;
                }

                const d = res.data;
                $('#nama').val(d.nama);
                $('input[name="id_siswa"]').val(d.id_siswa);

                $('#nominal_spp').val(formatRupiah(d.nominal_spp));
                $('#nominal_spp_hidden').val(d.nominal_spp);

                toggleField('#konsumsi-field', '#nominal_konsumsi', d.nominal_konsumsi);
                toggleField('#fullday-field', '#nominal_fullday', d.nominal_fullday);

                let total = d.nominal_spp + d.nominal_konsumsi + d.nominal_fullday;
                $('#jumlah_bayar').val(formatRupiah(total));
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                alert('Terjadi kesalahan saat mengambil data siswa');
            }
        });
    });

    function toggleField(selector, inputSelector, value) {
        if (value > 0) {
            $(selector).show();
            $(inputSelector).val(formatRupiah(value));
            $(inputSelector + '_hidden').val(value);
        } else {
            $(selector).hide();
            $(inputSelector).val('');
            $(inputSelector + '_hidden').val('');
        }
    }

    function formatRupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }
});
</script>
@endsection
