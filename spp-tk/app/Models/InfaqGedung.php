<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfaqGedung extends Model
{
    protected $table = 'infaq_gedung';
    protected $fillable = ['paket', 'nominal', 'jumlah_angsuran'];
    
    // Hitung nominal per angsuran secara dinamis
    public function getNominalPerAngsuranAttribute()
    {
        return $this->nominal / $this->jumlah_angsuran;
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_infaq_gedung');
    }
}