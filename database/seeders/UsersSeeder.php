<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create system super-admin (protected)
        User::updateOrCreate([
            'email' => 'admin@cgg.holdings',
        ], [
            'name' => 'Administrator',
            'password' => Hash::make('password'),
            'api_token' => 'edff951e464a5290515173df1d8a761cd27dcc55531d6f622fcb834fdf74c441',
            'email_verified_at' => now(),
            'role' => User::ROLE_SYSTEM,
        ]);
    }
}
