<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
   
    protected $fillable = [
          'id_petugas',
          'id_siswa', 
          'spp_bulan', 
          'jumlah_bayar',
          'jenis_pembayaran',
          'bulan', 
          'tahun',
          'jumlah_bayar',
          'tgl_bayar',
          'is_lunas'
    ];
   
 /**
   * Belongs To Pembayaran -> User (petugas)
   *
   * @return void
   */
    public function users()
    {
         return $this->belongsTo(User::class,'id_petugas', 'id');
    }
   
 /**
   * Has Many Pembayaran -> Siswa
   *
   * @return void
   */
    public function siswa()
    {
         return $this->belongsTo(Siswa::class,'id_siswa','id','nisn');
    }
   
    // Format tanggal pembayaran (accessor)
    public function getTglBayarFormattedAttribute()
    {
        return \Carbon\Carbon::parse($this->tgl_bayar)->format('d-m-Y');
    }

    // Scope untuk filter bulan/tahun
    public function scopeFilterByBulan($query, $bulan)
    {
        return $query->where('bulan', strtolower($bulan));
    }

    public function scopeFilterByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }
}
