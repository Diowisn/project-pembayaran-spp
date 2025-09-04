<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nisn', 'nama', 'password', 'id_kelas', 'id_spp',
        'alamat', 'nomor_telp', 'id_infaq_gedung', 'inklusi', 'id_inklusi'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function infaqGedung()
    {
        return $this->belongsTo(InfaqGedung::class, 'id_infaq_gedung');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_siswa');
    }

    public function angsuranInfaq()
    {
        return $this->hasMany(AngsuranInfaq::class, 'id_siswa');
    }

    public function spp()
    {
        return $this->belongsTo(Spp::class, 'id_spp');
    }

    public function tabungan()
    {
        return $this->hasMany(Tabungan::class, 'id_siswa');
    }

    public function kegiatan()
    {
        return $this->belongsToMany(Kegiatan::class, 'kegiatan_siswa', 'siswa_id', 'kegiatan_id')
                    ->withPivot('ikut', 'status_bayar')
                    ->withTimestamps();
    }

    public function kegiatanSiswa()
    {
        return $this->hasMany(SiswaKegiatan::class, 'id_siswa');
    }

    public function kegiatanTahunan()
    {
        return $this->belongsToMany(KegiatanTahunan::class, 'siswa_kegiatan', 'id_siswa', 'id_kegiatan')
                    ->withPivot('angsuran_ke', 'jumlah_bayar', 'tgl_bayar', 'is_lunas')
                    ->withTimestamps();
    }

    public function paketInklusi()
    {
        return $this->belongsTo(Inklusi::class, 'id_inklusi');
    }
}