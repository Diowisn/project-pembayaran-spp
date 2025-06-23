<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
   
    protected $fillable = [
         'nisn', 
         'nis', 
         'nama', 
         'id_kelas', 
         'nomor_telp', 
         'alamat', 
         'id_spp'
    ];
   
   /**
   * Belongs To Siswa -> Spp
   *
   * @return void
   */
    public function spp()
    {
         return $this->belongsTo(Spp::class,'id_spp','id');
    }
   
//    public function pembayaran(){
//         return  $this->hasMany(Pembayaran::class,'id_spp');
//    }
   
    public function kelas()
    {
        return  $this->belongsTo(Kelas::class,'id_kelas');
    }

     public function tarifSpp()
    {
        return $this->belongsTo(Spp::class, 'id_spp');
    }

    // Relasi ke Pembayaran
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_siswa');
    }

    // Method untuk cek lunas/belum per bulan
    public function isLunas($bulan, $tahun)
    {
        return $this->pembayaran()
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('is_lunas', true)
            ->exists();
    }
}
