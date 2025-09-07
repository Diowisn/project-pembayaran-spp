<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tabungan extends Model
{
    protected $table = 'tabungan';
    protected $fillable = [
        'id_siswa', 'id_pembayaran', 'id_pembayaran_infaq', 'id_petugas',
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

    public function pembayaranKegiatan()
    {
        return $this->belongsTo(SiswaKegiatan::class, 'id_pembayaran_kegiatan');
    }
}