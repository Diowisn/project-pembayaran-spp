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
                                                <h6 class="card-title">Kelas {{ $data['kelas'] }}</h6>
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
                                        <div class="mt-2">
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Penerimaan Bersih:</small>
                                                    <h5>Rp {{ number_format($data['current'], 0, ',', '.') }}</h5>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Target Penerimaan:</small>
                                                    <h5>Rp {{ number_format($data['target_penerimaan'], 0, ',', '.') }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-info dropdown-toggle"
                                                        type="button" id="dropdownRincian{{ $loop->index }}"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Lihat Rincian Target
                                                    </button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="dropdownRincian{{ $loop->index }}">
                                                        <div class="px-3 py-2">
                                                            <small class="text-muted d-block">Rincian Target Kelas
                                                                {{ $data['kelas'] }}:</small>
                                                            <ul class="list-unstyled mb-0">
                                                                <li>
                                                                    <small>SPP: Rp
                                                                        {{ number_format($data['target_spp'], 0, ',', '.') }}</small>
                                                                </li>
                                                                <li>
                                                                    <small>Konsumsi: Rp
                                                                        {{ number_format($data['target_konsumsi'], 0, ',', '.') }}</small>
                                                                </li>
                                                                <li>
                                                                    <small>Fullday: Rp
                                                                        {{ number_format($data['target_fullday'], 0, ',', '.') }}</small>
                                                                </li>
                                                                <li>
                                                                    <small>Inklusi: Rp
                                                                        {{ number_format($data['target_inklusi'], 0, ',', '.') }}</small>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar bg-info"
                                                    style="width: {{ $data['target_penerimaan'] > 0 ? min(round(($data['current'] / $data['target_penerimaan']) * 100, 2), 100) : 0 }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ $data['target_penerimaan'] > 0 ? round(($data['current'] / $data['target_penerimaan']) * 100, 2) : 0 }}%
                                                dari target
                                            </small> --}}
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

    <style>
        #paymentChart {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
        }

        .chart-container {
            position: relative;
            width: 100%;
            height: 400px;
        }
    </style>

    <!-- Payment Comparison -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Perbandingan Pembayaran</h4>
                    <div class="chart-container" style="border:2px;height:400px;">
                        <canvas id="paymentChart" width="400" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('paymentChart');
            if (!canvas) return;

            // Set ukuran fisik canvas
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            // Coba render chart jika data ada
            @if (!empty($pemasukanSPPPerKelas))
                try {
                    new Chart(canvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_column($pemasukanSPPPerKelas, 'kelas')) !!},
                            datasets: [{
                                    label: '{{ $previousMonthName }}',
                                    data: {!! json_encode(array_column($pemasukanSPPPerKelas, 'previous')) !!},
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: '{{ $currentMonthName }}',
                                    data: {!! json_encode(array_column($pemasukanSPPPerKelas, 'current')) !!},
                                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        afterBody: function(context) {
                                            const datasetIndex = context[0].datasetIndex;
                                            const dataIndex = context[0].dataIndex;
                                            const kelas = context[0].label;
                                            const netPayment = context[0].raw;

                                            // Hitung jumlah siswa yang sudah bayar
                                            const paidStudents = {!! json_encode(array_column($pemasukanSPPPerKelas, 'total_students')) !!}[dataIndex] -
                                                {!! json_encode(array_column($pemasukanSPPPerKelas, 'unpaid_count')) !!}[dataIndex];

                                            return [
                                                `Kelas: ${kelas}`,
                                                `Siswa Bayar: ${paidStudents} orang`,
                                                `Penerimaan Bersih: Rp ${netPayment.toLocaleString('id-ID')}`
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    });
                    console.log('Chart rendered successfully');
                } catch (e) {
                    console.error('Chart error:', e);
                    canvas.parentElement.innerHTML = `
                <div class="alert alert-danger">
                    Error rendering chart: ${e.message}
                </div>
            `;
                }
            @endif
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

    <script>
        window.forceRenderChart = function() {
            const container = document.getElementById('chartFallback');
            container.innerHTML = `
            <canvas id="forcedChart" style="width:100%;height:100%"></canvas>
        `;

            new Chart(
                document.getElementById('forcedChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Force', 'Render'],
                        datasets: [{
                            label: 'Test',
                            data: [10, 20],
                            backgroundColor: 'green'
                        }]
                    }
                }
            );
            console.log('Forced chart rendered');
        };
    </script>

    <style>
        /* Card and History Styling */
        .history-section {
            margin-bottom: 30px;
        }

        .history-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .history-tabs {
            padding: 15px 15px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }

        .history-tab {
            padding: 10px 20px;
            margin-right: 10px;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            background: none;
            color: #6c757d;
            position: relative;
        }

        .history-tab.active {
            color: #2196f3;
            font-weight: 600;
        }

        .history-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #2196f3;
        }

        .history-content {
            padding: 0 20px;
        }

        .comment-row {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            background: #f8f9fa;
            transition: all 0.3s ease;
            border-left: 4px solid #2196f3;
        }

        .comment-row:hover {
            transform: translateX(5px);
            background: #fff;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .list-group-item {
            margin-bottom: 5px;
            border-radius: 6px;
            background: #f8f9fa;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .action-icons a {
            padding: 5px;
            border-radius: 50%;
            transition: all 0.2s ease;
            color: #2196f3;
        }

        .action-icons a:hover {
            background: #e3f2fd;
            transform: scale(1.1);
        }
    </style>

    <!-- Payment History Sections -->
    <div class="row">
        <!-- Combined History Section -->
        <div class="col-md-12">
            <div class="card history-card">
                <div class="history-tabs">
                    <h4 class="card-title">Histori Pembayaran Terbaru</h4>
                    <button class="history-tab active" onclick="showHistory('spp')">Histori SPP</button>
                    <button class="history-tab" onclick="showHistory('infaq')">Histori Infaq</button>
                    <button class="history-tab" onclick="showHistory('kegiatan')">Histori Kegiatan</button>
                </div>
                <div class="card-body">
                    <!-- SPP History -->
                    <div id="spp-history" class="history-content">
                        <div class="comment-widgets scrollable" style="max-height: 600px; overflow-y: auto;">

                            @foreach ($pembayaran as $history)
                                <div class="d-flex flex-row comment-row">
                                    <i class="mdi mdi-account display-3"></i>
                                    <div class="comment-text active w-100">
                                        <hr>
                                        <span
                                            class="badge badge-success badge-rounded float-right">{{ $history->created_at->diffforHumans() }}</span>
                                        <h6 class="font-medium">{{ $history->siswa->nama ?? 'N/A' }}</h6>
                                        <span class="m-b-15 d-block">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">Kelas
                                                    {{ $history->siswa->kelas->nama_kelas ?? 'N/A' }} ~
                                                    SPP Bulan <b
                                                        class="text-capitalize text-bold">{{ $history->bulan }}</b>
                                                    {{ $history->tahun }}</li>

                                                {{-- Ambil data langsung dari model Pembayaran --}}
                                                @php
                                                    $nominalSpp = $history->nominal_spp ?? 0;
                                                    $nominalKonsumsi = $history->nominal_konsumsi ?? 0;
                                                    $nominalFullday = $history->nominal_fullday ?? 0;
                                                    $nominalInklusi = $history->nominal_inklusi ?? 0;
                                                    $totalNominal =
                                                        $nominalSpp +
                                                        $nominalKonsumsi +
                                                        $nominalFullday +
                                                        $nominalInklusi;
                                                @endphp

                                                <li class="list-group-item">Nominal SPP: Rp
                                                    {{ number_format($nominalSpp, 0, ',', '.') }}</li>
                                                <li class="list-group-item">Nominal Konsumsi: Rp
                                                    {{ number_format($nominalKonsumsi, 0, ',', '.') }}</li>
                                                <li class="list-group-item">Nominal Fullday: Rp
                                                    {{ number_format($nominalFullday, 0, ',', '.') }}</li>
                                                <li class="list-group-item">Nominal Inklusi: Rp
                                                    {{ number_format($nominalInklusi, 0, ',', '.') }}</li>
                                                <li class="list-group-item font-weight-bold">Total Tagihan: Rp
                                                    {{ number_format($totalNominal, 0, ',', '.') }}</li>
                                                <li class="list-group-item">Jumlah Bayar: Rp
                                                    {{ number_format($history->jumlah_bayar, 0, ',', '.') }}</li>
                                                <li class="list-group-item">Kembalian: Rp
                                                    {{ number_format($history->kembalian, 0, ',', '.') }}</li>
                                                <li class="list-group-item font-weight-bold text-primary">
                                                    Penerimaan Bersih: Rp
                                                    {{ number_format($history->jumlah_bayar - $history->kembalian, 0, ',', '.') }}
                                                </li>
                                                <li class="list-group-item">
                                                    Status:
                                                    @if ($history->is_lunas)
                                                        <span class="badge badge-success">LUNAS</span>
                                                    @else
                                                        <span class="badge badge-warning">BELUM LUNAS</span>
                                                    @endif
                                                </li>
                                            </ul>
                                        </span>
                                        <div class="comment-footer">
                                            <span
                                                class="text-muted float-right">{{ $history->created_at->format('M d, Y') }}</span>
                                            <span class="action-icons active">
                                                <a href="{{ route('pembayaran.generate', $history->id) }}" class="mr-2"
                                                    title="Cetak Bukti">
                                                    <i class="mdi mdi-printer"></i>
                                                </a>
                                                <a href="{{ url('dashboard/pembayaran/' . $history->id . '/edit') }}"
                                                    title="Edit">
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
                <!-- Infaq History -->
                <div id="infaq-history" class="history-content" style="display: none;">
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
                                            <li class="list-group-item">Kelas {{ $history->siswa->kelas->nama_kelas }} ~
                                                Paket
                                                <b
                                                    class="text-uppercase text-bold">{{ $history->infaqGedung->paket ?? '-' }}</b>
                                            </li>
                                            <li class="list-group-item">Total Infaq Rp.
                                                {{ number_format($history->infaqGedung->nominal ?? 0, 0, ',', '.') }}</li>
                                            <li class="list-group-item">Angsuran Ke-{{ $history->angsuran_ke }}</li>
                                            <li class="list-group-item">Jumlah Bayar: Rp
                                                {{ number_format($history->jumlah_bayar, 0, ',', '.') }}</li>
                                            <li class="list-group-item">Kembalian: Rp
                                                {{ number_format($history->kembalian, 0, ',', '.') }}</li>
                                            <li class="list-group-item font-weight-bold">
                                                Penerimaan Bersih: Rp
                                                {{ number_format($history->jumlah_bayar - $history->kembalian, 0, ',', '.') }}
                                            </li>
                                        </ul>
                                    </span>
                                    <div class="comment-footer">
                                        <span
                                            class="text-muted float-right">{{ $history->created_at->format('M d, Y') }}</span>
                                        <span class="action-icons active">
                                            <a href="{{ route('infaq.generate', $history->id) }}" class="mr-2"
                                                title="Cetak Bukti">
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
                <!-- Kegiatan History -->
                <div id="kegiatan-history" class="history-content" style="display: none;">
                    <div class="comment-widgets scrollable" style="max-height: 600px; overflow-y: auto;">

                        @foreach ($kegiatanHistori as $history)
                            <div class="d-flex flex-row comment-row">
                                <i class="mdi mdi-calendar-check display-3"></i>
                                <div class="comment-text active w-100">
                                    <hr>
                                    <span
                                        class="badge badge-info badge-rounded float-right">{{ $history->created_at->diffforHumans() }}</span>
                                    <h6 class="font-medium">{{ $history->siswa->nama }}</h6>
                                    <span class="m-b-15 d-block">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">Kelas {{ $history->siswa->kelas->nama_kelas }} ~
                                                <b
                                                    class="text-capitalize">{{ $history->kegiatan->nama_kegiatan ?? '-' }}</b>
                                            </li>
                                            <li class="list-group-item">Total Kegiatan: Rp
                                                {{ number_format($history->kegiatan->nominal ?? 0, 0, ',', '.') }}</li>
                                            <li class="list-group-item">Angsuran Ke-{{ $history->angsuran_ke }}</li>
                                            <li class="list-group-item">Jumlah Bayar: Rp
                                                {{ number_format($history->jumlah_bayar, 0, ',', '.') }}</li>
                                            <li class="list-group-item">Kembalian: Rp
                                                {{ number_format($history->kembalian, 0, ',', '.') }}</li>
                                            <li class="list-group-item">
                                                Status:
                                                @if ($history->is_lunas)
                                                    <span class="badge badge-success">LUNAS</span>
                                                @else
                                                    <span class="badge badge-warning">BELUM LUNAS</span>
                                                @endif
                                            </li>
                                            <li class="list-group-item font-weight-bold">
                                                Penerimaan Bersih: Rp
                                                {{ number_format($history->jumlah_bayar - $history->kembalian, 0, ',', '.') }}
                                            </li>
                                        </ul>
                                    </span>
                                    <div class="comment-footer">
                                        <span
                                            class="text-muted float-right">{{ $history->created_at->format('M d, Y') }}</span>
                                        <span class="action-icons active">
                                            <!-- Sesuaikan route berikut dengan route yang ada di aplikasi Anda -->
                                            <a href="{{ route('entri-kegiatan.generate-pdf', $history->id) }}"
                                                class="mr-2" title="Cetak Bukti">
                                                <i class="mdi mdi-printer"></i>
                                            </a>
                                            <a href="{{ route('entri-kegiatan.edit', $history->id) }}" title="Edit">
                                                <i class="ti-pencil-alt"></i>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if (count($kegiatanHistori) == 0)
                            <div class="text-center"> Tidak ada histori pembayaran kegiatan!</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        function showHistory(type) {
            // Hide all history sections
            document.getElementById('spp-history').style.display = 'none';
            document.getElementById('infaq-history').style.display = 'none';
            document.getElementById('kegiatan-history').style.display = 'none';

            // Show selected history section
            document.getElementById(type + '-history').style.display = 'block';

            // Update tab active states
            document.querySelectorAll('.history-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Initialize with SPP history shown
        document.addEventListener('DOMContentLoaded', function() {
            showHistory('spp');
        });
    </script>

    <!-- Unpaid Students Modals -->
    @foreach ($pemasukanSPPPerKelas as $kelas => $data)
        <div class="modal fade" id="unpaidModal-{{ \Illuminate\Support\Str::slug($kelas) }}" tabindex="-1"
            role="dialog" aria-hidden="true">
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
@endsection
