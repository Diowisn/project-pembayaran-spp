@extends('layouts.dashboard')

@section('breadcrumb')
   <li class="breadcrumb-item">Dashboard</li>
   <li class="breadcrumb-item active">Laporan</li>
@endsection

@section('content')
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-body">
               <div class="card-title">Pilih Jenis Laporan Pembayaran SPP</div>
               
               <div class="alert alert-info">
                  <strong>Panduan:</strong> Pilih jenis laporan yang ingin Anda buat.
               </div>
                       
               <div class="row">
                  <!-- Laporan Per Kelas -->
                  <div class="col-md-4 mb-4">
                     <div class="card h-100">
                        <div class="card-body text-center">
                           <div class="mb-3">
                              <i class="fas fa-users fa-3x text-primary"></i>
                           </div>
                           <h5 class="card-title">Laporan Per Kelas</h5>
                           <p class="card-text">Buat laporan berdasarkan kelas tertentu atau beberapa kelas</p>
                           <a href="{{ url('dashboard/laporan/kelas') }}" class="btn btn-primary">
                              <i class="fas fa-file-alt"></i> Buat Laporan
                           </a>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Laporan Per Bulan -->
                  <div class="col-md-4 mb-4">
                     <div class="card h-100">
                        <div class="card-body text-center">
                           <div class="mb-3">
                              <i class="fas fa-calendar-alt fa-3x text-success"></i>
                           </div>
                           <h5 class="card-title">Laporan Per Bulan</h5>
                           <p class="card-text">Buat laporan berdasarkan bulan tertentu atau beberapa bulan</p>
                           <a href="{{ url('dashboard/laporan/bulan') }}" class="btn btn-success">
                              <i class="fas fa-file-alt"></i> Buat Laporan
                           </a>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Laporan Per Semester -->
                  <div class="col-md-4 mb-4">
                     <div class="card h-100">
                        <div class="card-body text-center">
                           <div class="mb-3">
                              <i class="fas fa-book fa-3x text-warning"></i>
                           </div>
                           <h5 class="card-title">Laporan Per Semester</h5>
                           <p class="card-text">Buat laporan berdasarkan semester 1 atau semester 2</p>
                           <a href="{{ url('dashboard/laporan/semester') }}" class="btn btn-warning">
                              <i class="fas fa-file-alt"></i> Buat Laporan
                           </a>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Laporan Per Tahun -->
                  <div class="col-md-4 mb-4">
                     <div class="card h-100">
                        <div class="card-body text-center">
                           <div class="mb-3">
                              <i class="fas fa-calendar fa-3x text-info"></i>
                           </div>
                           <h5 class="card-title">Laporan Per Tahun</h5>
                           <p class="card-text">Buat laporan berdasarkan tahun tertentu</p>
                           <a href="{{ url('dashboard/laporan/tahun') }}" class="btn btn-info">
                              <i class="fas fa-file-alt"></i> Buat Laporan
                           </a>
                        </div>
                     </div>
                  </div>
                  
                  <!-- Laporan Semua Data -->
                  <div class="col-md-4 mb-4">
                     <div class="card h-100">
                        <div class="card-body text-center">
                           <div class="mb-3">
                              <i class="fas fa-database fa-3x text-secondary"></i>
                           </div>
                           <h5 class="card-title">Laporan Semua Data</h5>
                           <p class="card-text">Buat laporan semua data pembayaran tanpa filter</p>
                           <a href="{{ url('dashboard/laporan/semua') }}" class="btn btn-secondary">
                              <i class="fas fa-file-alt"></i> Buat Laporan
                           </a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
@endsection