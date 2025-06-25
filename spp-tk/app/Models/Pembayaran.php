<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_petugas', 'id_siswa', 'jenis_pembayaran',
        'bulan', 'tahun', 'jumlah_bayar', 'is_lunas', 'tgl_bayar'
    ];

    protected $casts = [
        'is_lunas' => 'boolean',
        'tgl_bayar' => 'date'
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_petugas');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function getJenisPembayaranAttribute($value)
    {
        return ucfirst(str_replace('_', ' ', $value));
    }
}