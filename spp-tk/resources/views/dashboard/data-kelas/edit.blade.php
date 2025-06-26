@extends('layouts.dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item">Kelas</li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('Edit Kelas') }}</div>
                    
                    <form method="post" action="{{ url('/dashboard/data-kelas', $edit->id) }}">
                        @csrf
                        @method('put')
                        
                        <div class="form-group">
                            <label>Nama Kelas</label>
                            <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror" 
                                   name="nama_kelas" value="{{ old('nama_kelas', $edit->nama_kelas) }}">
                            <span class="text-danger">@error('nama_kelas') {{ $message }} @enderror</span>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="has_konsumsi" id="has_konsumsi" 
                                       value="1" {{ $edit->has_konsumsi ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_konsumsi">Menyediakan Konsumsi</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="has_fullday" id="has_fullday" 
                                       value="1" {{ $edit->has_fullday ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_fullday">Program Fullday</label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-rounded">
                            <i class="mdi mdi-check"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>     
        </div>     
    </div>

@endsection

@section('sweet')
function deleteData(id){
    Swal.fire({
        title: 'PERINGATAN!',
        text: "Yakin ingin menghapus data kelas?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yakin',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.value) {
            $('#delete'+id).submit();
        }
    });
}
@endsection