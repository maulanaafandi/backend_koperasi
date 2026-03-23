<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['nomor_admin' => 'ADM-001'], 
            [
                'password' => Hash::make('password123'), 
            ]
        );

        $this->command->info('Akun Admin ADM001 berhasil dibuat!');
    }
}

