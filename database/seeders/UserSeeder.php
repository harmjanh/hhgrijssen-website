<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Webmaster',
                'email' => 'webmaster@hhgrijssen.nl',
                'password' => Hash::make(Str::random(10)), // Random password
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Koster',
                'email' => 'koster@hhgrijssen.nl',
                'password' => Hash::make(Str::random(10)),
                'role' => null,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}

