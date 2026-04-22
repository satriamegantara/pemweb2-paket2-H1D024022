<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '08123456789',
            'address' => 'Jl. Admin No. 1',
            'email_verified_at' => now(),
        ]);

        // Seller user
        User::create([
            'name' => 'Penjual',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'role' => 'penjual',
            'phone' => '08987654321',
            'address' => 'Jl. Seller No. 2',
            'email_verified_at' => now(),
        ]);

        // Buyer user
        User::create([
            'name' => 'Pembeli',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password'),
            'role' => 'pembeli',
            'phone' => '08555555555',
            'address' => 'Jl. Buyer No. 3',
            'email_verified_at' => now(),
        ]);
    }
}
