<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    protected $table = 'spp';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tahun', 
        'id_kelas',
        'nominal_spp',
        'nominal_konsumsi',
        'nominal_fullday',
        // 'nominal_inklusi',
        'id_infaq_gedung' 
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_spp');
    }

    public function infaqGedung()
    {
        return $this->belongsTo(InfaqGedung::class, 'id_infaq_gedung');
    }  

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_spp');
    }
}