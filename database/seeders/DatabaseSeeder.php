<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@sideral.com'],
            [
                'name'     => 'Admin SIDERAL',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Teknisi User
        User::updateOrCreate(
            ['email' => 'teknisi@sideral.com'],
            [
                'name'     => 'Teknisi SIDERAL',
                'password' => Hash::make('password'),
                'role'     => 'teknisi',
            ]
        );

        // Facility data seeder
        $this->call(FmLightningSeeder::class);
    }
}
