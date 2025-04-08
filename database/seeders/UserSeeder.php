<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'Front Office',
            'email' => 'fo@example.com',
            'password' => Hash::make('12345678'), // password terenkripsi
            'role' => 'front_office',
        ]);

        User::create([
            'nama' => 'IT Admin',
            'email' => 'it@example.com',
            'password' => Hash::make('12345678'), // password terenkripsi
            'role' => 'it',
        ]);

        User::create([
            'nama' => 'Marketing',
            'email' => 'marketing@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'marketing',
        ]);
    }
}
