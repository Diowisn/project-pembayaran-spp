<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Spp;
use App\Models\Pembayaran;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed Users (Admin dan Petugas)
        $this->seedUsers();
        
        // Seed Kelas
        $this->seedKelas();
        
        // Seed SPP (Tarif Pembayaran)
        $this->seedSpp();
        
        // Seed Siswa
        $this->seedSiswa();
        
        // Seed Pembayaran Contoh
        $this->seedPembayaran();
    }

    protected function seedUsers()
    {
        User::create([
            'name' => 'Admin SPP',
            'email' => 'admin@spp.com',
            'password' => Hash::make('admin123'),
            'level' => 'admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        User::create([
            'name' => 'Petugas SPP',
            'email' => 'petugas@spp.com',
            'password' => Hash::make('petugas123'),
            'level' => 'petugas',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    protected function seedKelas()
    {
        $kelas = [
            ['nama_kelas' => 'TPA', 'paket_infaq' => null, 'is_fullday' => false],
            ['nama_kelas' => 'KBT', 'paket_infaq' => 'B', 'is_fullday' => false],
            ['nama_kelas' => 'TK A', 'paket_infaq' => 'A', 'is_fullday' => false],
            ['nama_kelas' => 'TK B', 'paket_infaq' => 'A', 'is_fullday' => false],
            ['nama_kelas' => 'TK B Fullday', 'paket_infaq' => 'A', 'is_fullday' => true],
        ];

        foreach ($kelas as $data) {
            Kelas::create($data);
        }
    }

    protected function seedSpp()
    {
        $tahun = date('Y');
        
        $tarif = [
            // Infaq Gedung
            ['tahun' => $tahun, 'jenis_pembayaran' => 'infaq_gedung', 'kelas' => 'ALL', 'nominal' => 1500000, 'paket' => 'A'],
            ['tahun' => $tahun, 'jenis_pembayaran' => 'infaq_gedung', 'kelas' => 'ALL', 'nominal' => 1000000, 'paket' => 'B'],
            ['tahun' => $tahun, 'jenis_pembayaran' => 'infaq_gedung', 'kelas' => 'ALL', 'nominal' => 800000, 'paket' => 'C'],
            
            // SPP TPA
            ['tahun' => $tahun, 'jenis_pembayaran' => 'spp', 'kelas' => 'TPA', 'nominal' => 450000, 'paket' => null],
            
            // SPP KBT
            ['tahun' => $tahun, 'jenis_pembayaran' => 'spp', 'kelas' => 'KBT', 'nominal' => 110000, 'paket' => null],
            ['tahun' => $tahun, 'jenis_pembayaran' => 'konsumsi', 'kelas' => 'KBT', 'nominal' => 70000, 'paket' => null],
            
            // SPP TK A
            ['tahun' => $tahun, 'jenis_pembayaran' => 'spp', 'kelas' => 'TK A', 'nominal' => 110000, 'paket' => null],
            ['tahun' => $tahun, 'jenis_pembayaran' => 'konsumsi', 'kelas' => 'TK A', 'nominal' => 140000, 'paket' => null],
            
            // SPP TK B
            ['tahun' => $tahun, 'jenis_pembayaran' => 'spp', 'kelas' => 'TK B', 'nominal' => 110000, 'paket' => null],
            ['tahun' => $tahun, 'jenis_pembayaran' => 'konsumsi', 'kelas' => 'TK B', 'nominal' => 140000, 'paket' => null],
            ['tahun' => $tahun, 'jenis_pembayaran' => 'fullday', 'kelas' => 'TK B', 'nominal' => 125000, 'paket' => null],
        ];

        foreach ($tarif as $data) {
            Spp::create($data);
        }
    }

    protected function seedSiswa()
    {
        $siswa = [
            [
                'nisn' => '1234567890',
                'nis' => '1001',
                'nama' => 'Ananda TPA',
                'id_kelas' => 1, // TPA
                'alamat' => 'Jl. TPA No.1',
                'nomor_telp' => '081234567890',
                'id_spp' => 4 // SPP TPA
            ],
            [
                'nisn' => '2345678901',
                'nis' => '1002',
                'nama' => 'Budi KBT',
                'id_kelas' => 2, // KBT
                'alamat' => 'Jl. KBT No.2',
                'nomor_telp' => '082345678901',
                'id_spp' => 5 // SPP KBT
            ],
            [
                'nisn' => '3456789012',
                'nis' => '1003',
                'nama' => 'Citra TK A',
                'id_kelas' => 3, // TK A
                'alamat' => 'Jl. TK A No.3',
                'nomor_telp' => '083456789012',
                'id_spp' => 7 // SPP TK A
            ],
            [
                'nisn' => '4567890123',
                'nis' => '1004',
                'nama' => 'Doni TK B',
                'id_kelas' => 4, // TK B Regular
                'alamat' => 'Jl. TK B No.4',
                'nomor_telp' => '084567890123',
                'id_spp' => 9 // SPP TK B
            ],
            [
                'nisn' => '5678901234',
                'nis' => '1005',
                'nama' => 'Eka TK B Fullday',
                'id_kelas' => 5, // TK B Fullday
                'alamat' => 'Jl. TK B No.5',
                'nomor_telp' => '085678901234',
                'id_spp' => 9 // SPP TK B
            ]
        ];

        foreach ($siswa as $data) {
            Siswa::create($data);
        }
    }

    protected function seedPembayaran()
    {
        $bulan = strtolower(Carbon::now()->format('F'));
        $tahun = Carbon::now()->year;
        
        // Pembayaran SPP Tepat Waktu
        Pembayaran::create([
            'id_petugas' => 2,
            'id_siswa' => 1, // Ananda TPA
            'jenis_pembayaran' => 'spp',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jumlah_bayar' => 450000,
            'tgl_bayar' => Carbon::create($tahun, Carbon::now()->month, 5),
            'is_lunas' => true
        ]);

        // Pembayaran TK B Fullday (SPP + Konsumsi + Fullday)
        Pembayaran::create([
            'id_petugas' => 2,
            'id_siswa' => 5, // Eka TK B Fullday
            'jenis_pembayaran' => 'spp',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jumlah_bayar' => 110000 + 140000 + 125000, // Total 375000
            'tgl_bayar' => Carbon::create($tahun, Carbon::now()->month, 8),
            'is_lunas' => true
        ]);

        // Pembayaran Infaq Gedung Paket A
        Pembayaran::create([
            'id_petugas' => 2,
            'id_siswa' => 3, // Citra TK A
            'jenis_pembayaran' => 'infaq_gedung',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jumlah_bayar' => 1500000,
            'tgl_bayar' => Carbon::create($tahun, Carbon::now()->month, 1),
            'is_lunas' => true
        ]);
    }
}