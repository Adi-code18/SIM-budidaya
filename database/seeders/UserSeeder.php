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
                'nama' => 'Manajer AMS Budidaya',
                'email' => 'adi2618e@gmail.com',
                'password' => Hash::make('Manajer123'),
                'role' => 'manajer',
                'no_tlp' => '081234567890',
                'alamat' => 'Jl.Hj.Nurjamil Blok Panyaweuyan Desa Buniseuri Kec.Cipaku Ciamis, Jawa Barat',
            ],
            [
                'nama' => 'Tim Pembibitan',
                'email' => 'pembibitan@gmail.com',
                'password' => Hash::make('pembibitan123'),
                'role' => 'pembibitan',
                'no_tlp' => '081234567891',
                'alamat' => 'Tasikmalaya, Jawa Barat',
            ],
            [
                'nama' => 'Tim Pembesaran',
                'email' => 'pembesaran@gmail.com',
                'password' => Hash::make('pembesaran123'),
                'role' => 'pembesaran',
                'no_tlp' => '081234567892',
                'alamat' => 'Tasikmalaya, Jawa Barat',
            ],
            [
                'nama' => 'Tim Distribusi',
                'email' => 'distribusi@gmail.com',
                'password' => Hash::make('distribusi123'),
                'role' => 'petugas_distribusi',
                'no_tlp' => '081234567893',
                'alamat' => 'Tasikmalaya, Jawa Barat',
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