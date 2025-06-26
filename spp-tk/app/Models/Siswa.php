<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nisn', 'nis', 'nama', 'id_kelas', 
        'alamat', 'nomor_telp', 'id_infaq_gedung'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function infaqGedung()
    {
        return $this->belongsTo(InfaqGedung::class, 'id_infaq_gedung');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_siswa');
    }

    public function angsuranInfaq()
    {
        return $this->hasMany(AngsuranInfaq::class, 'id_siswa');
    }

    // public function spp()
    // {
    //     return $this->hasOneThrough(
    //         Spp::class,
    //         Kelas::class,
    //         'id',
    //         'id_kelas',
    //         'id_kelas',
    //         'id'
    //     );
    // }
}