<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanTahunan extends Model
{
    protected $table = 'kegiatan_tahunan';
    protected $fillable = [
        'nama_kegiatan', 
        'nominal',
        // 'wajib',
        'keterangan'
    ];

    public function siswaKegiatan()
    {
        return $this->hasMany(SiswaKegiatan::class, 'id_kegiatan');
    }
}