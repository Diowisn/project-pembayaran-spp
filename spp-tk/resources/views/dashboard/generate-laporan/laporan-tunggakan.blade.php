@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item"><a href="{{ url('dashboard/laporan') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Laporan Tunggakan SPP</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Laporan Siswa yang Belum Membayar SPP</div>

                    <div class="alert alert-info">
                        <strong>Panduan:</strong> Pilih filter dan klik "Terapkan Filter" untuk melihat data.
                    </div>

                    <form action="{{ url('dashboard/laporan/tunggakan') }}" method="GET" id="filterForm">
                        <div class="row">
                            <!-- Tahun Laporan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tahun" class="font-weight-bold">Tahun Laporan *</label>
                                    <select name="tahun" id="tahun" class="form-control" required>
                                        @for ($i = 2020; $i <= date('Y'); $i++)
                                            <option value="{{ $i }}" {{ $i == $tahun ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Pilihan Kelas -->
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">🏫 Pilih Kelas (Opsional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Pilih Kelas</label>
                                    <div class="alert alert-secondary py-2">
                                        <small>✔ Centang kelas yang ingin diperiksa. Kosongkan untuk memilih semua
                                            kelas.</small>
                                    </div>
                                    <div class="row">
                                        @foreach ($kelas as $kelasItem)
                                            <div class="col-md-3 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" name="kelas_id[]"
                                                        value="{{ $kelasItem->id }}" id="kelas{{ $kelasItem->id }}"
                                                        {{ in_array($kelasItem->id, $filter_kelas) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="kelas{{ $kelasItem->id }}">
                                                        {{ $kelasItem->nama_kelas }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-2">
                                        @php
                                            $paramsBulan = array_unique($bulan_dipilih);
                                            $queryParams = http_build_query(['bulan' => $paramsBulan]);
                                        @endphp
                                        <a href="{{ url('dashboard/laporan/tunggakan') }}?tahun={{ $tahun }}&{{ $queryParams }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-times"></i> Hapus Filter Kelas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pilihan Bulan -->
                        <div class="card mb-3">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">📅 Pilih Bulan yang Diperiksa *</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Pilih Bulan untuk Pengecekan Tunggakan</label>
                                    <div class="alert alert-warning py-2">
                                        <small>✔ Centang bulan-bulan yang ingin diperiksa tunggakannya.</small>
                                    </div>
                                    <div class="row">
                                        @php
                                            $bulanArr = [
                                                1 => 'Januari',
                                                2 => 'Februari',
                                                3 => 'Maret',
                                                4 => 'April',
                                                5 => 'Mei',
                                                6 => 'Juni',
                                                7 => 'Juli',
                                                8 => 'Agustus',
                                                9 => 'September',
                                                10 => 'Oktober',
                                                11 => 'November',
                                                12 => 'Desember',
                                            ];
                                        @endphp
                                        @foreach ($bulanArr as $key => $value)
                                            <div class="col-md-3 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" name="bulan[]"
                                                        value="{{ $key }}" id="bulan{{ $key }}"
                                                        {{ in_array($key, $bulan_dipilih) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="bulan{{ $key }}">
                                                        {{ $value }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-2">
                                        @php
                                            $paramsKelas = array_unique($filter_kelas);
                                            $queryParams = http_build_query(['kelas_id' => $paramsKelas]);
                                        @endphp
                                        <a href="{{ url('dashboard/laporan/tunggakan') }}?tahun={{ $tahun }}&{{ $queryParams }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-times"></i> Hapus Filter Bulan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-filter"></i> Terapkan Filter
                                </button>

                                <a href="{{ url('dashboard/laporan/tunggakan') }}" class="btn btn-warning btn-lg">
                                    <i class="fas fa-sync"></i> Reset Semua
                                </a>

                                <!-- Form untuk Generate PDF -->
                                @if (count($bulan_dipilih) > 0)
                                    @php
                                        $pdfParams = [
                                            'tahun' => $tahun,
                                            'bulan' => array_unique($bulan_dipilih),
                                            'kelas_id' => array_unique($filter_kelas),
                                        ];

                                        $pdfUrl = route('laporan.tunggakan.pdf') . '?' . http_build_query($pdfParams);
                                    @endphp

                                    <a href="{{ $pdfUrl }}" class="btn btn-danger btn-lg" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Generate Laporan PDF
                                    </a>
                                @endif

                                <a href="{{ url('dashboard/laporan') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Hasil Tunggakan -->
                    @if (count($bulan_dipilih) > 0)
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0 text-white">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        DATA SISWA DENGAN TUNGGAKAN SPP
                                        <span class="float-right">
                                            {{ count($siswa_belum_bayar) }} Siswa
                                        </span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <strong>Ditemukan {{ $total_siswa }} siswa dengan tunggakan SPP</strong><br>
                                        Periode: Tahun {{ $tahun }}, Bulan:
                                        @php
                                            $bulanNames = array_map(function ($bulan) use ($bulanArr) {
                                                return $bulanArr[$bulan] ?? 'Tidak Valid';
                                            }, $bulan_dipilih);
                                        @endphp
                                        {{ implode(', ', $bulanNames) }}

                                        @if (!empty($filter_kelas))
                                            <br>Kelas Terpilih:
                                            @php
                                                $kelasNames = [];
                                                foreach ($kelas as $kelasItem) {
                                                    if (in_array($kelasItem->id, $filter_kelas)) {
                                                        $kelasNames[] = $kelasItem->nama_kelas;
                                                    }
                                                }
                                            @endphp
                                            {{ implode(', ', $kelasNames) }}
                                        @else
                                            <br>Kelas Terpilih: Semua Kelas
                                        @endif
                                        <br>
                                        Total Nominal Tunggakan: <span class="text-danger">Rp
                                            {{ number_format($total_tunggakan, 0, ',', '.') }}</span>
                                    </div>

                                    @if ($total_siswa > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>NISN</th>
                                                        <th>Nama Siswa</th>
                                                        <th>Kelas</th>
                                                        <th>Bulan Tunggakan</th>
                                                        <th>Jumlah Bulan</th>
                                                        <th>Total Tunggakan</th>
                                                        <th>Nominal per Bulan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($siswa_belum_bayar as $key => $item)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $item['siswa']->nisn }}</td>
                                                            <td><strong>{{ $item['siswa']->nama }}</strong></td>
                                                            <td>{{ $item['siswa']->kelas->nama_kelas }}</td>
                                                            <td>
                                                                <span class="badge badge-danger">
                                                                    @foreach ($item['tunggakan'] as $tunggakan)
                                                                        {{ $tunggakan['nama_bulan'] }}@if (!$loop->last)
                                                                            ,
                                                                        @endif
                                                                    @endforeach
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-warning">
                                                                    {{ $item['jumlah_bulan_tunggakan'] }} bulan
                                                                </span>
                                                            </td>
                                                            <td class="text-danger font-weight-bold">
                                                                Rp
                                                                {{ number_format($item['total_tunggakan'], 0, ',', '.') }}
                                                            </td>
                                                            <td class="text-muted">
                                                                Rp
                                                                {{ number_format($item['tunggakan'][0]['nominal_spp'] ?? 0, 0, ',', '.') }}/bulan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center text-success py-4">
                                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                                            <h4>Tidak ada siswa dengan tunggakan SPP</h4>
                                            <p class="text-muted">Semua siswa sudah melunasi pembayaran SPP untuk periode
                                                yang dipilih</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle"></i> Silakan pilih minimal 1 bulan untuk melihat data tunggakan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        .badge {
            font-size: 0.8em;
            padding: 4px 8px;
        }

        .table td {
            vertical-align: middle;
        }
    </style>
@endsection
