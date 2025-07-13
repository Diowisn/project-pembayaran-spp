@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Home</li>
@endsection

@section('content')
    <div class="alert alert-success text-center"><b>Selamat Datang</b> di aplikasi pembayaran SPP Sekolah</div>

    <!-- Monthly Statistics -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Statistik Pembayaran SPP Bulan {{ $currentMonthName }}</h4>
                    <div class="row">
                        @foreach ($pemasukanSPPPerKelas as $kelas => $data)
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-title">Kelas {{ $kelas }}</h6>
                                                <h4 class="mb-0">{{ $data['payment_rate'] }}%</h4>
                                                <small>{{ $data['total_students'] - $data['unpaid_count'] }} dari
                                                    {{ $data['total_students'] }} siswa</small>
                                            </div>
                                            <div
                                                class="bg-{{ $data['payment_rate'] >= 80 ? 'success' : ($data['payment_rate'] >= 50 ? 'warning' : 'danger') }} rounded p-3">
                                                <i class="mdi mdi-account-multiple text-white"></i>
                                            </div>
                                        </div>
                                        <div class="progress mt-3">
                                            <div class="progress-bar bg-{{ $data['payment_rate'] >= 80 ? 'success' : ($data['payment_rate'] >= 50 ? 'warning' : 'danger') }}"
                                                role="progressbar" style="width: {{ $data['payment_rate'] }}%"
                                                aria-valuenow="{{ $data['payment_rate'] }}" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <div class="mt-2">
                                            <a href="#" data-toggle="modal"
                                                data-target="#unpaidModal-{{ Str::slug($kelas) }}" class="text-danger">
                                                <small>{{ $data['unpaid_count'] }} siswa belum bayar</small>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Comparison -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Perbandingan Pembayaran</h4>
                    <canvas id="paymentChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Sections -->
    <div class="row">
        <!-- Histori Pembayaran SPP -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Histori Pembayaran SPP</div>
                    <div class="comment-widgets scrollable" style="max-height: 600px; overflow-y: auto;">

                        @foreach ($pembayaran as $history)
                            <div class="d-flex flex-row comment-row">
                                <i class="mdi mdi-account display-3"></i>
                                <div class="comment-text active w-100">
                                    <hr>
                                    <span
                                        class="badge badge-success badge-rounded float-right">{{ $history->created_at->diffforHumans() }}</span>
                                    <h6 class="font-medium">{{ $history->siswa->nama }}</h6>
                                    <span class="m-b-15 d-block">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">Kelas {{ $history->siswa->kelas->nama_kelas }} ~ SPP
                                                Bulan <b class="text-capitalize text-bold">{{ $history->bulan }}</b></li>
                                            <li class="list-group-item">Nominal SPP Rp.
                                                {{ $history->siswa->spp->nominal_spp ?? '-' }}</li>
                                            <li class="list-group-item">Nominal Konsumsi
                                                Rp. {{ $history->siswa->spp->nominal_konsumsi ?? '-' }}</li>
                                            <li class="list-group-item">Nominal Fullday Rp.
                                                {{ $history->siswa->spp->nominal_fullday ?? '-' }}</li>
                                            <li class="list-group-item">Jumlah Bayar Rp. {{ $history->jumlah_bayar }}</li>
                                            <li class="list-group-item">Kembalian Rp. {{ $history->kembalian }}</li>
                                        </ul>
                                    </span>
                                    <div class="comment-footer">
                                        <span class="text-muted float-right">{{ $history->created_at->format('M d, Y') }}</span>
                                        <span class="action-icons active">
                                            <a href="{{ route('pembayaran.generate', $history->id) }}" class="mr-2" title="Cetak Bukti">
                                                <i class="mdi mdi-printer"></i>
                                            </a>
                                            <a href="{{ url('dashboard/pembayaran/' . $history->id . '/edit') }}" title="Edit">
                                                <i class="ti-pencil-alt"></i>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if (count($pembayaran) == 0)
                            <div class="text-center"> Tidak ada histori pembayaran SPP!</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Histori Pembayaran Infaq Gedung -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Histori Pembayaran Infaq Gedung</div>
                    <div class="comment-widgets scrollable" style="max-height: 600px; overflow-y: auto;">

                        @foreach ($infaqHistori as $history)
                            <div class="d-flex flex-row comment-row">
                                <i class="mdi mdi-home display-3"></i>
                                <div class="comment-text active w-100">
                                    <hr>
                                    <span
                                        class="badge badge-primary badge-rounded float-right">{{ $history->created_at->diffforHumans() }}</span>
                                    <h6 class="font-medium">{{ $history->siswa->nama }}</h6>
                                    <span class="m-b-15 d-block">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">Kelas {{ $history->siswa->kelas->nama_kelas }} ~ Paket 
                                                <b class="text-uppercase text-bold">{{ $history->infaqGedung->paket ?? '-' }}</b></li>
                                            <li class="list-group-item">Total Infaq Rp.
                                                {{ number_format($history->infaqGedung->nominal ?? 0, 0, ',', '.') }}</li>
                                            <li class="list-group-item">Angsuran Ke-{{ $history->angsuran_ke }}</li>
                                            <li class="list-group-item">Jumlah Bayar Rp. 
                                                {{ number_format($history->jumlah_bayar, 0, ',', '.') }}</li>
                                            <li class="list-group-item">Sisa Pembayaran Rp. 
                                                {{ number_format($history->infaqGedung->nominal - $history->siswa->angsuranInfaq->sum('jumlah_bayar'), 0, ',', '.') }}</li>
                                        </ul>
                                    </span>
                                    <div class="comment-footer">
                                        <span class="text-muted float-right">{{ $history->created_at->format('M d, Y') }}</span>
                                        <span class="action-icons active">
                                            <a href="{{ route('infaq.generate', $history->id) }}" class="mr-2" title="Cetak Bukti">
                                                <i class="mdi mdi-printer"></i>
                                            </a>
                                            <a href="{{ route('infaq.edit', $history->id) }}" title="Edit">
                                                <i class="ti-pencil-alt"></i>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if (count($infaqHistori) == 0)
                            <div class="text-center"> Tidak ada histori pembayaran infaq gedung!</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unpaid Students Modals -->
    @foreach ($pemasukanSPPPerKelas as $kelas => $data)
        <div class="modal fade" id="unpaidModal-{{ \Illuminate\Support\Str::slug($kelas) }}" tabindex="-1" role="dialog"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Siswa Belum Membayar - Kelas {{ $kelas }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($data['unpaid_students']->count() > 0)
                            <ul class="list-group">
                                @foreach ($data['unpaid_students'] as $siswa)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $siswa->nama }} ({{ $siswa->nisn }})
                                        <a href="{{ route('pembayaran.cari-siswa', ['nisn' => $siswa->nisn]) }}"
                                            class="badge badge-primary">Entri Pembayaran</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-success">Semua siswa sudah membayar!</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Payment comparison chart
    const ctx = document.getElementById('paymentChart').getContext('2d');
    const paymentChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($kelasList->pluck('nama_kelas')) !!},
            datasets: [
                {
                    label: 'Pembayaran {{ $previousMonthName }}',
                    data: {!! json_encode(array_column($pemasukanSPPPerKelas, 'previous')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Pembayaran {{ $currentMonthName }}',
                    data: {!! json_encode(array_column($pemasukanSPPPerKelas, 'current')) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Pembayaran (Rp)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.raw.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            }
        }
    });
</script>
    @endpush
@endsection