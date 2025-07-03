<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Buat user admin
        User::create([
            'name' => 'Admin SPP',
            'email' => 'admin@spp.com',
            'password' => Hash::make('admin'),
            'level' => 'admin',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Buat user petugas
        User::create([
            'name' => 'Petugas SPP',
            'email' => 'petugas@spp.com',
            'password' => Hash::make('petugas'),
            'level' => 'petugas',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $this->command->info('Users seeded successfully!');
    }
}