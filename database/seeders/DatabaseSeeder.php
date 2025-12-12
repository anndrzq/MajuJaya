<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Ananda',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('123123'),
            'gender' => 'L',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
