<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kelas;
use App\Models\InfaqGedung;
use App\Models\Spp;
use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\AngsuranInfaq;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        User::truncate();
        Kelas::truncate();
        InfaqGedung::truncate();
        Spp::truncate();
        Siswa::truncate();
        Pembayaran::truncate();
        AngsuranInfaq::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Seed Users
        $this->seedUsers();
        
        // Seed Kelas
        $kelasIds = $this->seedKelas();
        
        // Seed Infaq Gedung
        $infaqIds = $this->seedInfaqGedung();
        
        // Seed SPP
        $this->seedSpp($kelasIds);
        
        // Seed Siswa
        $this->seedSiswa($kelasIds, $infaqIds);
        
        // Seed Pembayaran
        $this->seedPembayaran();
        
        // Seed Angsuran Infaq
        $this->seedAngsuranInfaq();
    }

    protected function seedUsers()
    {
        User::create([
            'name' => 'Admin SPP',
            'email' => 'admin@spp.com',
            'password' => Hash::make('admin'),
            'level' => 'admin',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        User::create([
            'name' => 'Petugas SPP',
            'email' => 'petugas@spp.com',
            'password' => Hash::make('petugas'),
            'level' => 'petugas',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    protected function seedKelas()
    {
        $kelas = [
            [
                'nama_kelas' => 'TPA',
                'has_konsumsi' => false,
                'has_fullday' => false
            ],
            [
                'nama_kelas' => 'KBT',
                'has_konsumsi' => true,
                'has_fullday' => false
            ],
            [
                'nama_kelas' => 'TK A',
                'has_konsumsi' => true,
                'has_fullday' => false
            ],
            [
                'nama_kelas' => 'TK B',
                'has_konsumsi' => true,
                'has_fullday' => true
            ]
        ];

        $ids = [];
        foreach ($kelas as $data) {
            $kelas = Kelas::create($data);
            $ids[] = $kelas->id;
        }

        return $ids;
    }

    protected function seedInfaqGedung()
    {
        $infaq = [
            [
                'paket' => 'A',
                'nominal' => 1500000,
                'jumlah_angsuran' => 12,
                'nominal_per_angsuran' => 125000
            ],
            [
                'paket' => 'B',
                'nominal' => 1000000,
                'jumlah_angsuran' => 10,
                'nominal_per_angsuran' => 100000
            ],
            [
                'paket' => 'C',
                'nominal' => 800000,
                'jumlah_angsuran' => 8,
                'nominal_per_angsuran' => 100000
            ]
        ];

        $ids = [];
        foreach ($infaq as $data) {
            $infaq = InfaqGedung::create($data);
            $ids[] = $infaq->id;
        }

        return $ids;
    }

    protected function seedSpp($kelasIds)
    {
        $tahun = date('Y');
        
        $sppData = [
            // TPA
            [
                'id_kelas' => $kelasIds[0], // TPA
                'nominal_spp' => 450000,
                'nominal_konsumsi' => null,
                'nominal_fullday' => null,
                'tahun' => $tahun
            ],
            // KBT
            [
                'id_kelas' => $kelasIds[1], // KBT
                'nominal_spp' => 110000,
                'nominal_konsumsi' => 70000,
                'nominal_fullday' => null,
                'tahun' => $tahun
            ],
            // TK A
            [
                'id_kelas' => $kelasIds[2], // TK A
                'nominal_spp' => 110000,
                'nominal_konsumsi' => 140000,
                'nominal_fullday' => null,
                'tahun' => $tahun
            ],
            // TK B
            [
                'id_kelas' => $kelasIds[3], // TK B
                'nominal_spp' => 110000,
                'nominal_konsumsi' => 140000,
                'nominal_fullday' => 125000,
                'tahun' => $tahun
            ]
        ];

        foreach ($sppData as $data) {
            Spp::create($data);
        }
    }

protected function seedSiswa($kelasIds, $infaqIds)
{
    // Pertama, dapatkan semua SPP yang sudah dibuat
    $sppTpa = Spp::where('id_kelas', $kelasIds[0])->first();
    $sppKbt = Spp::where('id_kelas', $kelasIds[1])->first();
    $sppTkA = Spp::where('id_kelas', $kelasIds[2])->first();
    $sppTkB = Spp::where('id_kelas', $kelasIds[3])->first();

    $siswaData = [
        [
            'nisn' => '1234567890',
            'nis' => '1001',
            'nama' => 'Ananda TPA',
            'id_kelas' => $kelasIds[0], // TPA
            'id_spp' => $sppTpa->id,
            'alamat' => 'Jl. TPA No.1',
            'nomor_telp' => '081234567890',
            'id_infaq_gedung' => $infaqIds[0] // Paket A
        ],
        [
            'nisn' => '2345678901',
            'nis' => '1002',
            'nama' => 'Budi KBT',
            'id_kelas' => $kelasIds[1], // KBT
            'id_spp' => $sppKbt->id,
            'alamat' => 'Jl. KBT No.2',
            'nomor_telp' => '082345678901',
            'id_infaq_gedung' => $infaqIds[1] // Paket B
        ],
        [
            'nisn' => '3456789012',
            'nis' => '1003',
            'nama' => 'Citra TK A',
            'id_kelas' => $kelasIds[2], // TK A
            'id_spp' => $sppTkA->id,
            'alamat' => 'Jl. TK A No.3',
            'nomor_telp' => '083456789012',
            'id_infaq_gedung' => $infaqIds[0] // Paket A
        ],
        [
            'nisn' => '4567890123',
            'nis' => '1004',
            'nama' => 'Doni TK B',
            'id_kelas' => $kelasIds[3], // TK B
            'id_spp' => $sppTkB->id,
            'alamat' => 'Jl. TK B No.4',
            'nomor_telp' => '084567890123',
            'id_infaq_gedung' => $infaqIds[2] // Paket C
        ]
    ];

    foreach ($siswaData as $data) {
        Siswa::create($data);
    }
}

    protected function seedPembayaran()
    {
        $bulan = strtolower(Carbon::now()->format('F'));
        $tahun = Carbon::now()->year;
        
        // Pembayaran SPP TPA
        Pembayaran::create([
            'id_petugas' => 2, // Petugas
            'id_siswa' => 1, // Ananda TPA
            'jenis_pembayaran' => 'spp',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jumlah_bayar' => 450000,
            'is_lunas' => true,
            'tgl_bayar' => Carbon::now()->subDays(5)
        ]);

        // Pembayaran SPP + Konsumsi KBT
        Pembayaran::create([
            'id_petugas' => 2,
            'id_siswa' => 2, // Budi KBT
            'jenis_pembayaran' => 'spp',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jumlah_bayar' => 110000,
            'is_lunas' => true,
            'tgl_bayar' => Carbon::now()->subDays(3)
        ]);

        Pembayaran::create([
            'id_petugas' => 2,
            'id_siswa' => 2, // Budi KBT
            'jenis_pembayaran' => 'konsumsi',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jumlah_bayar' => 70000,
            'is_lunas' => true,
            'tgl_bayar' => Carbon::now()->subDays(3)
        ]);

        // Pembayaran Infaq Gedung
        Pembayaran::create([
            'id_petugas' => 2,
            'id_siswa' => 3, // Citra TK A
            'jenis_pembayaran' => 'infaq_gedung',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jumlah_bayar' => 1500000,
            'is_lunas' => false,
            'tgl_bayar' => Carbon::now()->subDays(1)
        ]);
    }

    protected function seedAngsuranInfaq()
    {
// Angsuran untuk siswa dengan infaq gedung (5x dari 12x)
        for ($i = 1; $i <= 5; $i++) {
            AngsuranInfaq::create([
                'id_siswa' => 1, // Ananda TPA (Paket A)
                'angsuran_ke' => $i,
                'jumlah_bayar' => 125000,
                'tgl_bayar' => Carbon::now()->subMonths(6 - $i)
            ]);
        }

        // Angsuran untuk siswa lain (3x dari 10x)
        for ($i = 1; $i <= 3; $i++) {
            AngsuranInfaq::create([
                'id_siswa' => 2, // Budi KBT (Paket B)
                'angsuran_ke' => $i,
                'jumlah_bayar' => 100000,
                'tgl_bayar' => Carbon::now()->subMonths(4 - $i)
            ]);
        }
    }
}