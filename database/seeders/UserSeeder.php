<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@pos.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kasir satu',
            'email' => 'kasir1@pos.test',
            'password' => 'password123',
            'role' => 'kasir',
        ]);
    }
}
