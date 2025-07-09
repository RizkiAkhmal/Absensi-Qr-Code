<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@absensi.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'qrcode' => Str::uuid(),
        ]);

        // Create sample pegawai
        User::create([
            'name' => 'John Doe',
            'email' => 'john@absensi.com',
            'password' => Hash::make('pegawai123'),
            'role' => 'pegawai',
            'qrcode' => Str::uuid(),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@absensi.com',
            'password' => Hash::make('pegawai123'),
            'role' => 'pegawai',
            'qrcode' => Str::uuid(),
        ]);
    }
}
