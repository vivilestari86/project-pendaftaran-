<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::withTrashed()->updateOrCreate(
            ['email' => 'admin@pendaftaran.test'],
            [
                'name' => 'Admin Pendaftaran',
                'phone_number' => '081234567890',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'Active',
                'last_active_at' => now(),
                'terms_agreed' => true,
                'deleted_at' => null,
            ],
        );

        User::withTrashed()->updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'phone_number' => '081234567891',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'Active',
                'last_active_at' => now(),
                'terms_agreed' => true,
                'deleted_at' => null,
            ],
        );         
    }
}
