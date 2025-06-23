<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Kelas;
use App\Models\Spp;

class KelasSeeder extends Seeder
{
    public function run()
    {
        // Data Kelas
        $kelas = [
            [
                'nama_kelas' => 'TPA',
                'jenjang' => 'TPA',
                'spp_nominal' => 450000,
                'konsumsi_nominal' => null,
                'fullday_nominal' => null
            ],
            [
                'nama_kelas' => 'KBT',
                'jenjang' => 'KB',
                'spp_nominal' => 110000,
                'konsumsi_nominal' => 70000,
                'fullday_nominal' => null
            ],
            [
                'nama_kelas' => 'TK A',
                'jenjang' => 'TK',
                'spp_nominal' => 110000,
                'konsumsi_nominal' => 140000,
                'fullday_nominal' => 125000
            ],
            [
                'nama_kelas' => 'TK B',
                'jenjang' => 'TK',
                'spp_nominal' => 110000,
                'konsumsi_nominal' => 140000,
                'fullday_nominal' => 125000
            ]
        ];

        foreach ($kelas as $data) {
            Kelas::create($data);
        }

        // Data SPP (opsional, jika tetap ingin menggunakan tabel SPP terpisah)
        $tahun = date('Y');
        Spp::create(['tahun' => $tahun, 'kategori' => 'spp', 'nominal' => 450000]);
        Spp::create(['tahun' => $tahun, 'kategori' => 'spp', 'nominal' => 110000]);
        Spp::create(['tahun' => $tahun, 'kategori' => 'konsumsi', 'nominal' => 70000]);
        Spp::create(['tahun' => $tahun, 'kategori' => 'konsumsi', 'nominal' => 140000]);
        Spp::create(['tahun' => $tahun, 'kategori' => 'fullday', 'nominal' => 125000]);
    }
}