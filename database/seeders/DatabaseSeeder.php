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
        // Facility data seeder first so buildings exist
        $this->call(FmLightningSeeder::class);

        // Clear all users first
        User::query()->delete();

        // Admin User
        User::create([
            'email' => 'admin@sideral.com',
            'name' => 'Admin SIDERAL',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Gedung A Technicians
        $gedungATechnicians = [
            ['name' => 'Hadiyanto', 'email' => 'hadiyanto@sideral.com'],
            ['name' => 'Malik', 'email' => 'malik@sideral.com'],
            ['name' => 'Imam Ahmad Junaidi', 'email' => 'imam@sideral.com'],
        ];

        foreach ($gedungATechnicians as $t) {
            User::create([
                'name' => $t['name'],
                'email' => $t['email'],
                'password' => Hash::make('password'),
                'plain_password' => 'password',
                'building_id' => 1,  // Gedung A / Gedung SIDERAL
                'role' => 'teknisi',
            ]);
        }

        // Gedung B Technicians
        $gedungBTechnicians = [
            ['name' => 'Yudiansyah', 'email' => 'yudiansyah@sideral.com'],
            ['name' => 'Teguh Ardiansyah', 'email' => 'teguh@sideral.com'],
            ['name' => 'Fajar Bayu Saptaji', 'email' => 'fajar@sideral.com'],
        ];

        foreach ($gedungBTechnicians as $t) {
            User::create([
                'name' => $t['name'],
                'email' => $t['email'],
                'password' => Hash::make('password'),
                'plain_password' => 'password',
                'building_id' => 2,  // Gedung B
                'role' => 'teknisi',
            ]);
        }
    }
}
