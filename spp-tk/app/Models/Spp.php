<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    protected $table = 'spp';
   
    protected $fillable = [
         'tahun', 'jenjang_pendidikan_id', 'nominal', 'tahun_berlaku'
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

    public function jenjangPendidikan()
    {
        return $this->belongsTo(JenjangPendidikan::class);
    }
}
