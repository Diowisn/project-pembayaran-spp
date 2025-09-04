<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaKegiatan extends Model
{
    protected $table = 'siswa_kegiatan';
    protected $fillable = [
        'id_siswa', 'id_kegiatan', 'partisipasi', 'alasan_tidak_ikut', 
        'angsuran_ke', 'jumlah_bayar', 'kembalian', 'tgl_bayar', 'is_lunas',
        'id_petugas'
    ];

    protected $casts = [
        'tgl_bayar' => 'date'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function kegiatan()
    {
        return $this->belongsTo(KegiatanTahunan::class, 'id_kegiatan');
    }
    
    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_petugas');
    }
}