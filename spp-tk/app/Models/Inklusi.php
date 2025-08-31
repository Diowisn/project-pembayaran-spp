<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inklusi extends Model
{
    protected $table = 'inklusi';
    protected $fillable = [
        'nama_paket', 
        'nominal',
        'keterangan'
    ];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_inklusi');
    }
}
