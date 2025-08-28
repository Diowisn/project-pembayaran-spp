<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanSiswa extends Model
{
    protected $table = 'kegiatan_siswa';
    protected $fillable = ['siswa_id', 'kegiatan_id', 'ikut', 'status_bayar'];
    
    // Tambahkan casting untuk boolean jika perlu
    protected $casts = [
        'ikut' => 'boolean',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}