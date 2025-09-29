@extends('layouts.dashboard')

@section('breadcrumb')
   <li class="breadcrumb-item">Dashboard</li>
   <li class="breadcrumb-item"><a href="{{ url('dashboard/laporan') }}">Laporan</a></li>
   <li class="breadcrumb-item active">Laporan Per Semester</li>
@endsection

@section('content')
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-body">
               <div class="card-title">Laporan Pembayaran SPP Per Semester</div>
               
               <div class="alert alert-info">
                  <strong>Panduan:</strong> Pilih semester dan tahun yang ingin dibuat laporannya.
               </div>
                       
               <form action="{{ url('dashboard/laporan/create') }}" method="POST" target="_blank" id="laporanForm">
                  @csrf
                  <input type="hidden" name="jenis_laporan" value="per_semester">
                  
                  <!-- Tahun Laporan -->
                  <div class="row mb-4">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="tahun" class="font-weight-bold">Tahun Laporan *</label>
                           <select name="tahun" id="tahun" class="form-control" required>
                              @for($i = 2020; $i <= date('Y') + 1; $i++)
                                 <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                    {{ $i }}
                                 </option>
                              @endfor
                           </select>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Pilihan Semester -->
                  <div class="card mb-3">
                     <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">📚 Pilih Semester</h6>
                     </div>
                     <div class="card-body">
                        <div class="form-group">
                           <label class="font-weight-bold">Pilih Semester *</label>
                           <div class="row">
                              <div class="col-md-6">
                                 <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="semester" value="1" id="semester1" checked>
                                    <label class="custom-control-label" for="semester1">
                                       <strong>Semester 1</strong> (Januari - Juni)
                                    </label>
                                 </div>
                                 <small class="text-muted">Laporan untuk periode Januari sampai Juni</small>
                              </div>
                              <div class="col-md-6">
                                 <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="semester" value="2" id="semester2">
                                    <label class="custom-control-label" for="semester2">
                                       <strong>Semester 2</strong> (Juli - Desember)
                                    </label>
                                 </div>
                                 <small class="text-muted">Laporan untuk periode Juli sampai Desember</small>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Informasi Semester -->
                  <div class="alert alert-primary">
                     <h6><i class="fas fa-info-circle"></i> Informasi Semester</h6>
                     <div class="row">
                        <div class="col-md-6">
                           <strong>Semester 1:</strong>
                           <ul class="mb-0">
                              <li>Januari</li>
                              <li>Februari</li>
                              <li>Maret</li>
                              <li>April</li>
                              <li>Mei</li>
                              <li>Juni</li>
                           </ul>
                        </div>
                        <div class="col-md-6">
                           <strong>Semester 2:</strong>
                           <ul class="mb-0">
                              <li>Juli</li>
                              <li>Agustus</li>
                              <li>September</li>
                              <li>Oktober</li>
                              <li>November</li>
                              <li>Desember</li>
                           </ul>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Tombol Aksi -->
                  <div class="row mt-4">
                     <div class="col-md-12">
                        <button type="submit" class="btn btn-warning btn-lg">
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
      $('#laporanForm').on('submit', function(e) {
         // Validasi sederhana
         const semester = $('input[name="semester"]:checked').val();
         if (!semester) {
            e.preventDefault();
            alert('Pilih semester terlebih dahulu!');
            return false;
         }
         
         // Tampilkan loading indicator
         $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Generating...').prop('disabled', true);
      });
   });
</script>
@endsection