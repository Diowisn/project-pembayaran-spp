<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UangTahunan extends Model
{
    protected $table = 'uang_tahunan';
    protected $fillable = [
        'id_siswa', 'id_pembayaran', 'id_petugas', 'tahun_ajaran',
        'debit', 'kredit', 'saldo', 'keterangan'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'id_pembayaran');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_petugas');
    }
}