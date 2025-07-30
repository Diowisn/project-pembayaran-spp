<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AngsuranInfaq extends Model
{
    protected $table = 'angsuran_infaq';
    protected $fillable = [
        'id_siswa', 'angsuran_ke', 'jumlah_bayar', 'tgl_bayar'
    ];

    protected $casts = [
        'tgl_bayar' => 'date'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function infaqGedung()
    {
        return $this->hasOneThrough(
            InfaqGedung::class,
            Siswa::class,
            'id', // Foreign key on siswa table
            'id', // Foreign key on infaq_gedung table
            'id_siswa', // Local key on angsuran_infaq table
            'id_infaq_gedung' // Local key on siswa table
        );
    }
}