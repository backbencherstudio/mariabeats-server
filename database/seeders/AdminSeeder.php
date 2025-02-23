<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // check if admin already exists do not create again
        if (User::where('email', 'maria@admin.com')->exists()) {
            return;
        }
        User::create([
            'name' => 'Admin',
            'email' => 'maria@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
