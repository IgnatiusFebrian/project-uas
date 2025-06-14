<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Create Employee User
        User::create([
            'name' => 'Employee',
            'email' => 'employee@example.com',
            'password' => Hash::make('employee123'),
            'role' => 'employee'
        ]);
    }
}