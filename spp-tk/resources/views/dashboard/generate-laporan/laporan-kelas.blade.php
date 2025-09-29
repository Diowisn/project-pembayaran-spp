@extends('layouts.dashboard')

@section('breadcrumb')
   <li class="breadcrumb-item">Dashboard</li>
   <li class="breadcrumb-item"><a href="{{ url('dashboard/laporan') }}">Laporan</a></li>
   <li class="breadcrumb-item active">Laporan Per Kelas</li>
@endsection

@section('content')
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-body">
               <div class="card-title">Laporan Pembayaran SPP Per Kelas</div>
               
               <div class="alert alert-info">
                  <strong>Panduan:</strong> Pilih kelas yang ingin dimasukkan dalam laporan. Kosongkan untuk memilih semua kelas.
               </div>
                       
               <form action="{{ url('dashboard/laporan/create') }}" method="POST" target="_blank" id="laporanForm">
                  @csrf
                  <input type="hidden" name="jenis_laporan" value="per_kelas">
                  
                  <!-- Tahun Laporan -->
                  <div class="row mb-4">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="tahun_laporan" class="font-weight-bold">Tahun Laporan *</label>
                           <select name="tahun_laporan" id="tahun_laporan" class="form-control" required>
                              @for($i = 2020; $i <= date('Y') + 1; $i++)
                                 <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
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
                        <h6 class="mb-0">👥 Pilih Kelas</h6>
                     </div>
                     <div class="card-body">
                        <div class="form-group">
                           <label class="font-weight-bold">Pilih Kelas (Bisa memilih beberapa)</label>
                           <div class="alert alert-warning py-2">
                              <small>✔ Centang kelas-kelas yang ingin dimasukkan dalam laporan. Kosongkan untuk memilih semua kelas.</small>
                           </div>
                           <div class="row">
                              @foreach($kelas as $item)
                                 <div class="col-md-3 mb-2">
                                    <div class="custom-control custom-checkbox">
                                       <input type="checkbox" class="custom-control-input" name="kelas_id[]" value="{{ $item->id }}" id="kelas{{ $item->id }}">
                                       <label class="custom-control-label" for="kelas{{ $item->id }}">
                                          {{ $item->nama_kelas }}
                                       </label>
                                    </div>
                                 </div>
                              @endforeach
                           </div>
                           <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="toggleAllKelas()">
                              <i class="fas fa-check-double"></i> Pilih Semua
                           </button>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Tombol Aksi -->
                  <div class="row mt-4">
                     <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-lg">
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
   function toggleAllKelas() {
      const checkboxes = document.querySelectorAll('input[name="kelas_id[]"]');
      const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
      
      checkboxes.forEach(checkbox => {
         checkbox.checked = !allChecked;
      });
   }

   $(document).ready(function() {
      $('#laporanForm').submit(function(e) {
         // Tampilkan loading indicator
         $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Generating...').prop('disabled', true);
      });
   });
</script>
@endsection