<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class AddPasswordToExistingSiswa extends Seeder
{
    public function run()
    {
        $siswas = Siswa::all();
        
        foreach ($siswas as $siswa) {
            $siswa->password = Hash::make('123456');
            $siswa->save();
        }
        
        $this->command->info('Password semua siswa diubah ke default: 123456');
    }
}