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

    // Akses ke infaq gedung melalui siswa
    public function infaqGedung()
    {
        return $this->hasOneThrough(
            InfaqGedung::class,
            Siswa::class,
            'id', // Foreign key pada tabel siswa
            'id', // Foreign key pada tabel infaq_gedung
            'id_siswa', // Local key pada tabel angsuran_infaq
            'id_infaq_gedung' // Local key pada tabel siswa
        );
    }
}