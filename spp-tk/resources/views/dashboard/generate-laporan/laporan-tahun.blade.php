@extends('layouts.dashboard')

@section('breadcrumb')
   <li class="breadcrumb-item">Dashboard</li>
   <li class="breadcrumb-item"><a href="{{ url('dashboard/laporan') }}">Laporan</a></li>
   <li class="breadcrumb-item active">Laporan Per Tahun</li>
@endsection

@section('content')
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-body">
               <div class="card-title">Laporan Pembayaran SPP Per Tahun</div>
               
               <div class="alert alert-info">
                  <strong>Panduan:</strong> Pilih tahun yang ingin dibuat laporannya. Laporan akan menampilkan semua data pembayaran pada tahun tersebut.
               </div>
                       
               <form action="{{ url('dashboard/laporan/create') }}" method="POST" target="_blank" id="laporanForm">
                  @csrf
                  <input type="hidden" name="jenis_laporan" value="per_tahun">
                  
                  <!-- Tahun Laporan -->
                  <div class="row mb-4">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="tahun" class="font-weight-bold">Pilih Tahun *</label>
                           <select name="tahun" id="tahun" class="form-control" required>
                              @for($i = 2020; $i <= date('Y') + 1; $i++)
                                 <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                    {{ $i }}
                                 </option>
                              @endfor
                           </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="alert alert-secondary">
                           <small>
                              <i class="fas fa-info-circle"></i> 
                              <strong>Informasi:</strong> Laporan akan menampilkan semua data pembayaran SPP, konsumsi, fullday, dan inklusi untuk tahun yang dipilih.
                           </small>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Statistik Cepat -->
                  <div class="card mb-3">
                     <div class="card-header bg-info text-white">
                        <h6 class="mb-0">📊 Preview Tahun yang Dipilih</h6>
                     </div>
                     <div class="card-body">
                        <div class="row text-center">
                           <div class="col-md-3">
                              <div class="border rounded p-3">
                                 <h5 class="text-primary" id="previewBulan">12</h5>
                                 <small class="text-muted">Bulan</small>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="border rounded p-3">
                                 <h5 class="text-success" id="previewHari">365</h5>
                                 <small class="text-muted">Hari</small>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="border rounded p-3">
                                 <h5 class="text-warning" id="previewSemester">2</h5>
                                 <small class="text-muted">Semester</small>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="border rounded p-3">
                                 <h5 class="text-danger" id="previewTriwulan">4</h5>
                                 <small class="text-muted">Triwulan</small>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Informasi Tambahan -->
                  <div class="alert alert-warning">
                     <h6><i class="fas fa-lightbulb"></i> Tips Laporan Tahunan</h6>
                     <ul class="mb-0">
                        <li>Laporan tahunan berguna untuk evaluasi kinerja tahunan</li>
                        <li>Dapat melihat trend pembayaran dari bulan ke bulan</li>
                        <li>Cocok untuk laporan keuangan akhir tahun</li>
                        <li>Berguna untuk perencanaan anggaran tahun berikutnya</li>
                     </ul>
                  </div>
                  
                  <!-- Tombol Aksi -->
                  <div class="row mt-4">
                     <div class="col-md-12">
                        <button type="submit" class="btn btn-info btn-lg">
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
   function updatePreview() {
      const tahun = $('#tahun').val();
      const tahunSekarang = new Date().getFullYear();
      
      // Hitung apakah tahun kabisat
      const isKabisat = (tahun % 4 === 0 && (tahun % 100 !== 0 || tahun % 400 === 0));
      const jumlahHari = isKabisat ? 366 : 365;
      
      // Update preview
      $('#previewBulan').text('12');
      $('#previewHari').text(jumlahHari);
      $('#previewSemester').text('2');
      $('#previewTriwulan').text('4');
      
      // Jika tahun yang dipilih adalah tahun depan, beri warning
      if (parseInt(tahun) > tahunSekarang) {
         $('#tahun').addClass('border-warning');
         $('.alert-secondary').html(`
            <small>
               <i class="fas fa-exclamation-triangle text-warning"></i> 
               <strong>Perhatian:</strong> Anda memilih tahun depan. Data mungkin belum tersedia.
            </small>
         `);
      } else {
         $('#tahun').removeClass('border-warning');
         $('.alert-secondary').html(`
            <small>
               <i class="fas fa-info-circle"></i> 
               <strong>Informasi:</strong> Laporan akan menampilkan semua data pembayaran SPP, konsumsi, fullday, dan inklusi untuk tahun yang dipilih.
            </small>
         `);
      }
   }

   $(document).ready(function() {
      // Update preview saat halaman dimuat
      updatePreview();
      
      // Update preview saat tahun berubah
      $('#tahun').change(function() {
         updatePreview();
      });

      $('#laporanForm').on('submit', function(e) {
         const tahun = $('#tahun').val();
         const tahunSekarang = new Date().getFullYear();
         
         // Validasi tahun depan
         if (parseInt(tahun) > tahunSekarang) {
            if (!confirm('Anda memilih tahun depan. Data mungkin belum tersedia. Lanjutkan?')) {
               e.preventDefault();
               return false;
            }
         }
         
         // Tampilkan loading indicator
         $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Generating...').prop('disabled', true);
      });
   });
</script>
@endsection