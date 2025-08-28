<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';
    protected $fillable = ['nama_kegiatan', 'biaya', 'tahun'];

    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'kegiatan_siswa')
                    ->withPivot('ikut')
                    ->withTimestamps();
    }
}