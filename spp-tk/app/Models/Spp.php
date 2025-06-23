<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    protected $table = 'spp';
   
    protected $fillable = [
        'tahun', 
        'jenis_pembayaran', 
        'kelas',
        'nominal', 
        'paket'
    ];
   
    /**
   * Belongs To Spp -> User
   *
   * @return void
   */
   public function user()
   {
         return $this->belongsTo(User::class);
   }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_spp');
    }
}
