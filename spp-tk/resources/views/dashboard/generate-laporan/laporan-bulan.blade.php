@extends('layouts.dashboard')

@section('breadcrumb')
   <li class="breadcrumb-item">Dashboard</li>
   <li class="breadcrumb-item"><a href="{{ url('dashboard/laporan') }}">Laporan</a></li>
   <li class="breadcrumb-item active">Laporan Per Bulan</li>
@endsection

@section('content')
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-body">
               <div class="card-title">Laporan Pembayaran SPP Per Bulan</div>
               
               <div class="alert alert-info">
                  <strong>Panduan:</strong> Pilih bulan yang ingin dimasukkan dalam laporan. Centang bulan-bulan yang diinginkan.
               </div>
                       
               <form action="{{ url('dashboard/laporan/create') }}" method="POST" target="_blank" id="laporanForm">
                  @csrf
                  <input type="hidden" name="jenis_laporan" value="per_bulan">
                  
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
                  
                  <!-- Pilihan Bulan -->
                  <div class="card mb-3">
                     <div class="card-header bg-success text-white">
                        <h6 class="mb-0">📅 Pilih Bulan Laporan *</h6>
                     </div>
                     <div class="card-body">
                        <div class="form-group">
                           <label class="font-weight-bold">Pilih Bulan (Bisa memilih beberapa)</label>
                           <div class="alert alert-warning py-2">
                              <small>✔ Centang bulan-bulan yang ingin dimasukkan dalam laporan. Kosongkan untuk memilih semua bulan pada tahun tersebut.</small>
                           </div>
                           <div class="row">
                              @php
                                 $bulanArr = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                 ];
                                 $currentMonth = date('n');
                              @endphp
                              @foreach($bulanArr as $key => $value)
                                 <div class="col-md-3 mb-2">
                                    <div class="custom-control custom-checkbox">
                                       <input type="checkbox" class="custom-control-input" 
                                              name="bulan[]" value="{{ $key }}" 
                                              id="bulan{{ $key }}">
                                       <label class="custom-control-label" for="bulan{{ $key }}">
                                          {{ $value }}
                                       </label>
                                    </div>
                                 </div>
                              @endforeach
                           </div>
                           
                           <!-- Tombol sederhana tanpa JavaScript -->
                           <div class="mt-3">
                              <small class="text-muted">
                                 <i class="fas fa-info-circle"></i> 
                                 Centang bulan-bulan yang ingin ditampilkan dalam laporan
                              </small>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Tombol Aksi -->
                  <div class="row mt-4">
                     <div class="col-md-12">
                        <button type="submit" class="btn btn-success btn-lg">
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
   // Hanya validasi sederhana
   $(document).ready(function() {
      $('#laporanForm').on('submit', function(e) {
         // Tampilkan loading indicator
         $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Generating...').prop('disabled', true);
      });
   });
</script>
@endsection