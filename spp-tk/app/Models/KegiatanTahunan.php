<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanTahunan extends Model
{
    protected $table = 'kegiatan_tahunan';
    protected $fillable = [
        'nama_paket',
        'nama_kegiatan', 
        'nominal',
        'keterangan'
    ];

    // Tambahkan casting untuk memastikan tipe data
    protected $casts = [
        'nominal' => 'integer',
    ];

    public function siswaKegiatan()
    {
        return $this->hasMany(SiswaKegiatan::class, 'id_kegiatan');
    }
    
    // Scope untuk mengambil data paket saja (tanpa kegiatan)
    public function scopeOnlyPaket($query)
    {
        return $query->whereNull('nama_kegiatan');
    }
    
    // Scope untuk mengambil data kegiatan saja
    public function scopeOnlyKegiatan($query)
    {
        return $query->whereNotNull('nama_kegiatan');
    }
    
    // Scope untuk mengambil data berdasarkan paket
    public function scopeByPaket($query, $paket)
    {
        return $query->where('nama_paket', $paket);
    }
    
    // Method untuk mendapatkan daftar paket unik
    public static function getPaketList()
    {
        return self::select('nama_paket')
            ->whereNotNull('nama_paket')
            ->distinct()
            ->orderBy('nama_paket')
            ->pluck('nama_paket');
    }
    
    // Method untuk mendapatkan semua paket (widget)
    public static function getPaketWidgets()
    {
        return self::select('id', 'nama_paket')
            ->whereNotNull('nama_paket')
            ->whereNull('nama_kegiatan')
            ->orderBy('nama_paket')
            ->get();
    }
    
    // Method untuk mendapatkan kegiatan berdasarkan paket
    public static function getKegiatanByPaket($paket)
    {
        return self::where('nama_paket', $paket)
            ->whereNotNull('nama_kegiatan')
            ->orderBy('id', 'DESC')
            ->get();
    }
    
    // Accessor untuk menampilkan nominal yang formatted
    public function getNominalFormattedAttribute()
    {
        return $this->nominal ? 'Rp. ' . number_format($this->nominal, 0, ',', '.') : '-';
    }
}