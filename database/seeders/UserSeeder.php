<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nama' => 'Manajer SIM-Budidaya',
                'email' => 'manajer@example.com',
                'password' => Hash::make('password123'),
                'role' => 'manajer',
                'no_tlp' => '081234567890'
            ],
            [
                'nama' => 'Petugas Distribusi',
                'email' => 'distribusi@example.com',
                'password' => Hash::make('password123'),
                'role' => 'petugas_distribusi',
                'no_tlp' => '081234567891'
            ],
            [
                'nama' => 'Tim Pembesaran',
                'email' => 'pembesaran@example.com',
                'password' => Hash::make('password123'),
                'role' => 'pembesaran',
                'no_tlp' => '081234567892'
            ],
            [
                'nama' => 'Tim Pembibitan',
                'email' => 'pembibitan@example.com',
                'password' => Hash::make('password123'),
                'role' => 'pembibitan',
                'no_tlp' => '081234567893'
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}