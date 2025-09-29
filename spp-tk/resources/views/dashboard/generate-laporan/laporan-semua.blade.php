@extends('layouts.dashboard')

@section('breadcrumb')
   <li class="breadcrumb-item">Dashboard</li>
   <li class="breadcrumb-item"><a href="{{ url('dashboard/laporan') }}">Laporan</a></li>
   <li class="breadcrumb-item active">Laporan Semua Data</li>
@endsection

@section('content')
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-body">
               <div class="card-title">Laporan Pembayaran SPP Semua Data</div>
               
               <!-- Tampilkan error/success messages -->
               @if(session('error'))
                   <div class="alert alert-danger">
                       {{ session('error') }}
                   </div>
               @endif
               
               @if(session('success'))
                   <div class="alert alert-success">
                       {{ session('success') }}
                   </div>
               @endif
               
               <div class="alert alert-info">
                  <strong>Panduan:</strong> Laporan ini akan menampilkan semua data pembayaran SPP yang tersedia dalam sistem tanpa filter tertentu.
               </div>
                       
               <form action="{{ route('laporan.create') }}" method="POST" target="_blank" id="laporanForm">
                  @csrf
                  <input type="hidden" name="jenis_laporan" value="semua">
                  
                  <!-- Informasi Laporan -->
                  <div class="row mb-4">
                     <div class="col-md-12">
                        <div class="alert alert-warning">
                           <h6><i class="fas fa-exclamation-triangle"></i> Informasi Penting</h6>
                           <ul class="mb-0">
                              <li>Laporan ini akan menampilkan <strong>semua data pembayaran</strong> yang ada dalam sistem</li>
                              <li>Data yang ditampilkan mencakup semua tahun, bulan, dan kelas</li>
                              <li>Laporan mungkin berisi banyak halaman tergantung jumlah data</li>
                              <li>Disarankan untuk menggunakan filter tertentu jika data terlalu banyak</li>
                           </ul>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Statistik Data Keseluruhan -->
                  <div class="card mb-3">
                     <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">📊 Statistik Data Keseluruhan</h6>
                     </div>
                     <div class="card-body">
                        <div class="row text-center">
                           @php
                              // Hitung statistik cepat
                              $totalPembayaran = \App\Models\Pembayaran::count();
                              $totalSiswa = \App\Models\Siswa::count();
                              $totalKelas = \App\Models\Kelas::count();
                              $tahunAwal = \App\Models\Pembayaran::min('tahun') ?: date('Y');
                              $tahunAkhir = \App\Models\Pembayaran::max('tahun') ?: date('Y');
                           @endphp
                           <div class="col-md-3">
                              <div class="border rounded p-3">
                                 <h5 class="text-primary">{{ number_format($totalPembayaran) }}</h5>
                                 <small class="text-muted">Total Transaksi</small>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="border rounded p-3">
                                 <h5 class="text-success">{{ number_format($totalSiswa) }}</h5>
                                 <small class="text-muted">Total Siswa</small>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="border rounded p-3">
                                 <h5 class="text-warning">{{ number_format($totalKelas) }}</h5>
                                 <small class="text-muted">Total Kelas</small>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="border rounded p-3">
                                 <h5 class="text-danger">{{ $tahunAwal }} - {{ $tahunAkhir }}</h5>
                                 <small class="text-muted">Rentang Tahun</small>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Peringatan Data Besar -->
                  @if($totalPembayaran > 1000)
                  <div class="alert alert-danger">
                     <h6><i class="fas fa-database"></i> Peringatan: Data Besar</h6>
                     <p class="mb-0">
                        Data yang akan di-generate sangat besar ({{ number_format($totalPembayaran) }} transaksi). 
                        Proses mungkin memakan waktu lebih lama dan file PDF bisa berukuran besar.
                        Disarankan untuk menggunakan filter tahun atau bulan tertentu.
                     </p>
                  </div>
                  @endif
                  
                  <!-- Tombol Aksi -->
                  <div class="row mt-4">
                     <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                           <i class="fas fa-file-pdf"></i> Generate Laporan PDF
                        </button>
                        
                        <a href="{{ url('dashboard/laporan') }}" class="btn btn-secondary btn-lg">
                           <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        
                        <div class="mt-2">
                           <small class="text-muted">
                              <i class="fas fa-info-circle"></i> Laporan akan dibuka di tab baru dalam format PDF
                           </small>
                        </div>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
@endsection

@section('scripts')
<script>
   $(document).ready(function() {
      // Peringatan konfirmasi untuk data besar
      $('#laporanForm').on('submit', function(e) {
         const totalData = {{ $totalPembayaran }};
         
         if (totalData > 1000) {
            if (!confirm(`Anda akan generate laporan dengan ${totalData.toLocaleString()} transaksi. Proses ini mungkin memakan waktu beberapa menit. Lanjutkan?`)) {
               e.preventDefault();
               return false;
            }
         }
         
         // Tampilkan loading indicator
         $('#generateBtn').html('<i class="fas fa-spinner fa-spin"></i> Generating...').prop('disabled', true);
      });
   });
</script>
@endsection