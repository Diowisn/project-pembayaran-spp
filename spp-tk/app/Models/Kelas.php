<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_kelas', 'has_konsumsi', 'has_fullday', 'has_inklusi'
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

// menambahkan kolom untuk status
// terus dipanggil dibagian siswa sebagai tanda untuk naik kelas
// ketika siswa sudah lulus, maka statusnya akan diubah menjadi 'lulus', dan otomatis masuk ke tingkat kelas selanjutnya
// jadi saat melakukan input kelas, ditambahkan seperti ini untuk tahap 1,2,3, dan seterusnya
// 'status' => 'aktif', // atau 'lulus' jika sudah lulus (jadi ada 4 enum, aktif, lulus, tidak aktif, dan tinggal kelas)