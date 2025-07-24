<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nisn', 'nama', 'password', 'id_kelas', 'id_spp',
        'alamat', 'nomor_telp', 'id_infaq_gedung'
        //  'nis', 
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

    public function spp()
    {
        return $this->belongsTo(Spp::class, 'id_spp');
    }

    public function tabungan()
    {
        return $this->hasMany(Tabungan::class, 'id_siswa');
    }
}