<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'User',
            'username' => 'user',
            'password' => bcrypt('password'),
            'email' => 'hradmin@example.com',
            'role' => 'user',
            'division_id' => 3,
        ]);
    }
}
