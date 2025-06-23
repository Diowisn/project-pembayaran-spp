<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Spp;
use App\Models\Pembayaran;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         
        User::create([
            'name' => 'admin',
            'email' => 'admin@spp.com',
            'password' => Hash::make('admin'),
            'level' => 'admin',
            'created_at' => now(),
            'updated_at' => now()
         ]);
         
         User::create([
            'name' => 'petugas',
            'email' => 'petugas@spp.com',
            'password' => Hash::make('petugas'),
            'level' => 'petugas',
            'created_at' => now(),
            'updated_at' => now()
         ]);

        Kelas::create([
            'nama_kelas' => 'XII RPL 2',
            'kompetensi_keahlian' => 'Rekayasa Perangkat Lunak'
        ]);

        Spp::create([
            'tahun' => 2020,
            'nominal' => 150000
        ]);

        Siswa::create([
            'nisn' => '123456789876',
            'nis'  => '22373687',
            'nama' => 'siswa',
            'id_kelas' => 1,
            'nomor_telp' => '089689957106',
            'alamat' => 'Majalengka',
            'id_spp' => 1
        ]);

        Pembayaran::create([
            'id_petugas' => 2,
            'id_siswa' => 1,
            'spp_bulan' => 'februari',
            'jumlah_bayar' => 150000

        ]);
    }
}
