<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_kelas', 'has_konsumsi', 'has_fullday'
    ];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas');
    }

    public function spp()
    {
        return $this->hasMany(Spp::class, 'id_kelas');
    }
}