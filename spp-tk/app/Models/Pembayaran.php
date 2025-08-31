<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_petugas', 'name', 'id_siswa', 'id_spp', 'bulan', 'tahun',
        'nominal_spp', 'nominal_konsumsi', 'nominal_fullday', 'nominal_inklusi',
        'jumlah_bayar', 'kembalian', 'is_lunas', 'tgl_bayar'
    ];

    protected $casts = [
        'is_lunas' => 'boolean',
        'tgl_bayar' => 'date'
    ];

    public function spp()
    {
        return $this->belongsTo(Spp::class, 'id_spp');
    }
    
    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_petugas')->withDefault([
            'name' => $this->name ?? 'Administrator',
        ]);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function getJenisPembayaranAttribute($value)
    {
        return ucfirst(str_replace('_', ' ', $value));
    }

    public function tabungan() 
    {
        return $this->hasOne(Tabungan::class, 'id_pembayaran');
    }
}