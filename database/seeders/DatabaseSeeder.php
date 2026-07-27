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

        // Budi User (Teknisi)
        User::updateOrCreate(
            ['email' => 'budi@sideral.com'],
            [
                'name'     => 'Budi',
                'password' => Hash::make('password'),
                'role'     => 'teknisi',
            ]
        );

        // Candra User (Teknisi)
        User::updateOrCreate(
            ['email' => 'candra@sideral.com'],
            [
                'name'     => 'Candra',
                'password' => Hash::make('password'),
                'role'     => 'teknisi',
            ]
        );

        // Facility data seeder
        $this->call(FmLightningSeeder::class);
    }
}
