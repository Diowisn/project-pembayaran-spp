@extends('layouts.dashboard')

@section('breadcrumb')
   <li class="breadcrumb-item">Dashboard</li>
   <li class="breadcrumb-item">Infaq Gedung</li>
   <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-body">
            <div class="card-title">Edit Pembayaran Infaq Gedung</div>
            
            @php
                // Hitung total yang sudah dibayar (selain pembayaran ini)
                $totalDibayarLainnya = \App\Models\AngsuranInfaq::where('id_siswa', $edit->id_siswa)
                    ->where('id', '!=', $edit->id)
                    ->sum('jumlah_bayar');
                
                $totalTagihan = $edit->infaqGedung->nominal ?? 0;
                $sisaTagihan = max(0, $totalTagihan - $totalDibayarLainnya);
            @endphp
            
            <form method="post" action="{{ route('infaq.update', $edit->id) }}">
               @csrf
               @method('put')
               
               <div class="form-group">
                  <label>NISN Siswa</label>
                  <input type="text" class="form-control" 
                         value="{{ $edit->siswa->nisn }}" readonly>
               </div>
               
               <div class="form-group">
                  <label>Nama Siswa</label>
                  <input type="text" class="form-control" value="{{ $edit->siswa->nama }}" readonly>
               </div>

               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Paket Infaq</label>
                        <input type="text" class="form-control" 
                               value="{{ $edit->infaqGedung->paket ?? '-' }}" readonly>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Total Infaq Gedung</label>
                        <input type="text" class="form-control" 
                               value="Rp {{ number_format($totalTagihan, 0, ',', '.') }}" readonly>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Sisa Tagihan</label>
                        <input type="text" class="form-control" 
                               value="Rp {{ number_format($sisaTagihan, 0, ',', '.') }}" readonly>
                     </div>
                  </div>
               </div>

               <div class="form-group">
                  <label>Angsuran Ke</label>
                  <input type="number" class="form-control @error('angsuran_ke') is-invalid @enderror" 
                         name="angsuran_ke" value="{{ old('angsuran_ke', $edit->angsuran_ke) }}" 
                         min="1" required>
                  <span class="text-danger">@error('angsuran_ke') {{ $message }} @enderror</span>
               </div>

               <div class="form-group">
                  <label>Jumlah Bayar</label>
                  <div class="input-group">
                     <div class="input-group-prepend">
                         <span class="input-group-text">Rp</span>
                     </div>
                     <input type="number" class="form-control @error('jumlah_bayar') is-invalid @enderror" 
                            name="jumlah_bayar" value="{{ old('jumlah_bayar', $edit->jumlah_bayar) }}" 
                            min="1" required>
                  </div>
                  <small class="form-text text-muted">
                      Maksimal pembayaran: Rp {{ number_format($sisaTagihan + $edit->jumlah_bayar, 0, ',', '.') }}
                  </small>
                  <span class="text-danger">@error('jumlah_bayar') {{ $message }} @enderror</span>
               </div>

               <div class="form-group">
                  <label>Tanggal Bayar</label>
                  <input type="date" class="form-control @error('tgl_bayar') is-invalid @enderror" 
                         name="tgl_bayar" value="{{ old('tgl_bayar', $edit->tgl_bayar->format('Y-m-d')) }}" required>
                  <span class="text-danger">@error('tgl_bayar') {{ $message }} @enderror</span>
               </div>

               <div class="alert alert-info">
                  <i class="mdi mdi-information"></i>
                  <strong>Informasi:</strong> Jika jumlah bayar melebihi sisa tagihan, kelebihan akan ditambahkan ke tabungan sebagai kembalian.
               </div>

               <a href="{{ route('infaq.index') }}" class="btn btn-primary btn-rounded">
                  <i class="mdi mdi-chevron-left"></i> Kembali
               </a>
               <button type="submit" class="btn btn-success btn-rounded float-right">
                  <i class="mdi mdi-check"></i> Simpan Perubahan
               </button>
            </form>
         </div>
      </div>
   </div>
</div>
@endsection